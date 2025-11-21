<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use App\Services\NLUService;
use App\Services\BookingService;

class ChatbotController extends Controller
{
    protected $nlu;
    protected $booking;

    public function __construct(NLUService $nlu, BookingService $booking)
    {
        $this->nlu = $nlu;
        $this->booking = $booking;
    }

    public function handle(): void
    {
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        $botman = BotManFactory::create([]);

        $botman->hears('{message}', function (BotMan $bot, $message) {
            foreach ($this->processMessage($message) as $reply) {
                $bot->reply($reply);
            }
        });

        $botman->listen();
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $responses = $this->processMessage($validated['message'], $request);

        return response()->json([
            'reply' => $responses[0] ?? '😅 Xin lỗi, tôi chưa hiểu ý bạn.',
            'replies' => $responses,
        ]);
    }

    private function processMessage(string $message, Request $request = null): array
    {
        $responses = [];
        $nluData = $this->nlu->analyze($message);
        $intent = $nluData['intent'] ?? null;

        // Kiểm tra các context từ session
        $bookingFlow = session('booking_flow', null);
        $isFindingOtherFacilities = session('chatbot_finding_other_facilities', false);
        $isCheckingPrice = session('chatbot_checking_price', false);

        // XỬ LÝ BOOKING FLOW - Ưu tiên cao nhất
        if ($bookingFlow) {
            return $this->handleBookingFlow($message, $nluData, $request);
        }

        // Nếu đang trong flow tìm cơ sở khác
        if ($isFindingOtherFacilities && $nluData['entities']['time'] &&
            ($intent === 'check_availability' || $intent === 'unknown')) {
            $intent = 'find_other_facilities';
            $nluData['intent'] = 'find_other_facilities';
        }

        switch ($intent) {
            case 'greeting':
                $responses[] = 'Xin chào 👋! Tôi là AI hỗ trợ đặt sân. Tôi có thể giúp bạn:<br>• Đặt sân<br>• Kiểm tra giờ trống<br>• Xem giá<br>• Tìm cơ sở khác';
                $this->clearAllSessions($request);
                break;

            case 'booking_request':
                // BẮT ĐẦU LUỒNG ĐẶT SÂN
                $responses[] = $this->startBookingFlow($nluData, $request);
                break;

            case 'check_price':
                $facilityName = $nluData['entities']['facility_name'] ?? null;

                if (!$facilityName) {
                    session(['chatbot_checking_price' => true]);
                    $responses[] = 'Bạn muốn xem giá sân ở cơ sở nào? Vui lòng cho tôi biết tên cơ sở.<br>VD: Thủ Đức, Quận 1, CuChi...';
                } else {
                    $priceInfo = $this->booking->getPriceInfo($facilityName);
                    
                    if ($priceInfo === null) {
                        session(['chatbot_checking_price' => true]);
                        $responses[] = '❌ Không tìm thấy cơ sở "<b>' . htmlspecialchars($facilityName) . '</b>".<br>Vui lòng kiểm tra lại tên cơ sở hoặc thử tên khác.<br>VD: Thủ Đức, Quận 1, CuChi...';
                    } else {
                        $responses[] = $priceInfo;
                        session()->forget('chatbot_checking_price');
                    }
                }
                
                if ($request) {
                    session()->forget('chatbot_finding_other_facilities');
                }
                break;

            case 'view_booking':
                $responses[] = $this->buildBookingHistoryResponse();
                $this->clearAllSessions($request);
                break;

            case 'check_availability':
                $responses[] = $this->buildAvailabilityResponse($nluData);
                $this->clearAllSessions($request);
                break;

            case 'find_other_facilities':
                if ($request && !$nluData['entities']['time']) {
                    session(['chatbot_finding_other_facilities' => true]);
                } else if ($request) {
                    session()->forget('chatbot_finding_other_facilities');
                }
                $responses[] = $this->buildOtherFacilitiesResponse($nluData);
                
                if ($request) {
                    session()->forget('chatbot_checking_price');
                }
                break;

            default:
                if ($isFindingOtherFacilities && $nluData['entities']['time']) {
                    $nluData['intent'] = 'find_other_facilities';
                    $responses[] = $this->buildOtherFacilitiesResponse($nluData);
                    if ($request) {
                        session()->forget('chatbot_finding_other_facilities');
                    }
                }
                else if ($isCheckingPrice) {
                    $facilityName = $this->extractFacilityNameFromMessage($message);
                    
                    if ($facilityName) {
                        $priceInfo = $this->booking->getPriceInfo($facilityName);
                        
                        if ($priceInfo === null) {
                            $responses[] = '❌ Không tìm thấy cơ sở "<b>' . htmlspecialchars($facilityName) . '</b>".<br>Vui lòng nhập tên cơ sở khác.<br>VD: Thủ Đức, Quận 1, CuChi...';
                        } else {
                            $responses[] = $priceInfo;
                            if ($request) {
                                session()->forget('chatbot_checking_price');
                            }
                        }
                    } else {
                        $responses[] = '❓ Tôi không nhận diện được tên cơ sở trong tin nhắn của bạn.<br>Vui lòng nhập lại tên cơ sở rõ ràng hơn.<br>VD: Thủ Đức, Quận 1, CuChi...';
                    }
                } 
                else {
                    $responses[] = '😅 Xin lỗi, tôi chưa hiểu ý bạn.<br>Hãy thử:<br>• "Đặt sân"<br>• "Kiểm tra sân trống hôm nay 18h"<br>• "Giá sân bao nhiêu"<br>• "Tìm cơ sở khác"';
                }
                break;
        }

        return $responses;
    }

    // ==================== BOOKING FLOW ====================
    
    private function startBookingFlow(array $nluData, Request $request = null): string
    {
        if (!auth()->id()) {
            return '🔒 Bạn cần đăng nhập để đặt sân.';
        }

        // Khởi tạo booking flow
        $flow = [
            'step' => 'ask_flow_choice',
            'data' => []
        ];

        if ($request) {
            session(['booking_flow' => $flow]);
        }

        return "🎾 <b>Bạn muốn đặt sân như thế nào?</b><br><br>" .
               "1️⃣ Tôi biết cơ sở muốn đặt<br>" .
               "2️⃣ Giúp tôi tìm cơ sở phù hợp<br><br>" .
               "Vui lòng nhập <b>1</b> hoặc <b>2</b>";
    }

    private function handleBookingFlow(string $message, array $nluData, Request $request = null): array
    {
        $flow = session('booking_flow');
        $step = $flow['step'] ?? null;
        $data = $flow['data'] ?? [];

        switch ($step) {
            case 'ask_flow_choice':
                return $this->handleFlowChoice($message, $request);

            // LUỒNG 1: Biết cơ sở
            case 'flow1_ask_facility':
                return $this->handleFlow1AskFacility($message, $nluData, $request);
            
            case 'flow1_select_time_date':
                return $this->handleFlow1SelectTimeDate($message, $nluData, $request);

            case 'flow1_select_court':
                return $this->handleFlow1SelectCourt($message, $request);

            case 'flow1_confirm_booking':
                return $this->handleFlow1ConfirmBooking($message, $request);

            // LUỒNG 2: Không biết cơ sở
            case 'flow2_ask_time':
                return $this->handleFlow2AskTime($message, $nluData, $request);

            case 'flow2_ask_date':
                return $this->handleFlow2AskDate($message, $nluData, $request);

            case 'flow2_show_facilities':
                return $this->handleFlow2ShowFacilities($message, $request);

            case 'flow2_select_court':
                return $this->handleFlow2SelectCourt($message, $request);

            case 'flow2_confirm_booking':
                return $this->handleFlow2ConfirmBooking($message, $request);

            default:
                session()->forget('booking_flow');
                return ['❌ Có lỗi xảy ra. Vui lòng thử lại bằng cách gõ "Đặt sân"'];
        }
    }

    // LUỒNG 1: User biết cơ sở muốn đặt
    private function handleFlowChoice(string $message, Request $request = null): array
    {
        $choice = trim($message);

        if ($choice === '1') {
            $flow = [
                'step' => 'flow1_ask_facility',
                'data' => ['flow_type' => 1]
            ];
            if ($request) session(['booking_flow' => $flow]);

            return ['📍 Bạn muốn đặt sân tại cơ sở nào?<br>VD: Thủ Đức, Quận 1, CuChi...'];
        } 
        else if ($choice === '2') {
            $flow = [
                'step' => 'flow2_ask_time',
                'data' => ['flow_type' => 2]
            ];
            if ($request) session(['booking_flow' => $flow]);

            return ['⏰ Bạn muốn đặt sân vào khung giờ nào?<br>VD: 18h, 20h, 19:30...'];
        } 
        else {
            return ['❓ Vui lòng chọn <b>1</b> hoặc <b>2</b>'];
        }
    }

    private function handleFlow1AskFacility(string $message, array $nluData, Request $request = null): array
    {
        $facilityName = $this->extractFacilityNameFromMessage($message);
        
        if (!$facilityName) {
            return ['❓ Tôi không nhận diện được tên cơ sở. Vui lòng nhập lại.<br>VD: Thủ Đức, Quận 1, CuChi...'];
        }

        // Kiểm tra cơ sở có tồn tại không
        $facility = $this->booking->getFacilityByName($facilityName);
        
        if (!$facility) {
            return ['❌ Không tìm thấy cơ sở "<b>' . htmlspecialchars($facilityName) . '</b>".<br>Vui lòng thử tên khác.'];
        }

        $flow = session('booking_flow');
        $flow['step'] = 'flow1_select_time_date';
        $flow['data']['facility_id'] = $facility['facility_id'];
        $flow['data']['facility_name'] = $facility['facility_name'];
        
        if ($request) session(['booking_flow' => $flow]);

        return [
            "✅ Đã chọn cơ sở: <b>{$facility['facility_name']}</b><br><br>" .
            "📅 Bạn muốn đặt vào ngày nào và giờ nào?<br>" .
            "VD: <b>18h hôm nay</b>, <b>20h ngày mai</b>, <b>19:30 ngày 25/12</b>"
        ];
    }

    private function handleFlow1SelectTimeDate(string $message, array $nluData, Request $request = null): array
    {
        $time = $nluData['entities']['time'] ?? null;
        $date = $nluData['entities']['date'] ?? date('Y-m-d');

        if (!$time) {
            return ['⏰ Vui lòng cho biết giờ muốn đặt.<br>VD: 18h, 20h, 19:30...'];
        }

        $flow = session('booking_flow');
        $facilityId = $flow['data']['facility_id'];
        
        // Kiểm tra sân trống
        $availability = $this->booking->checkAvailabilityByFacility($facilityId, $date, $time);
        
        if (isset($availability['error'])) {
            return [$availability['error']];
        }

        if (empty($availability['available'])) {
            return [
                "❌ Rất tiếc, tại <b>{$flow['data']['facility_name']}</b> không còn sân trống vào " . 
                date('H:i', strtotime($time)) . " ngày " . date('d/m/Y', strtotime($date)) . ".<br><br>" .
                "💡 Vui lòng chọn giờ khác hoặc gõ <b>Hủy</b> để kết thúc đặt sân."
            ];
        }

        $flow['step'] = 'flow1_select_court';
        $flow['data']['date'] = $date;
        $flow['data']['time'] = $time;
        $flow['data']['available_courts'] = $availability['available'];
        
        if ($request) session(['booking_flow' => $flow]);

        $courtsList = implode(', ', array_map(function($court) {
            return "<b>$court</b>";
        }, $availability['available']));

        return [
            "✅ Còn trống các sân: $courtsList<br><br>" .
            "🎾 Bạn muốn đặt sân nào?<br>" .
            "VD: Sân 1, Sân 3..."
        ];
    }

    private function handleFlow1SelectCourt(string $message, Request $request = null): array
    {
        $flow = session('booking_flow');
        $availableCourts = $flow['data']['available_courts'] ?? [];
        
        // Extract số sân
        if (preg_match('/sân\s*(\d+)/iu', $message, $matches)) {
            $courtNumber = $matches[1];
            $courtName = "Sân " . $courtNumber;
            
            if (!in_array($courtName, $availableCourts)) {
                return ["❌ Sân $courtNumber không khả dụng. Vui lòng chọn trong danh sách: " . implode(', ', $availableCourts)];
            }
            
            $flow['step'] = 'flow1_confirm_booking';
            $flow['data']['court_name'] = $courtName;
            
            if ($request) session(['booking_flow' => $flow]);

            $formattedTime = date('H:i', strtotime($flow['data']['time']));
            $formattedDate = date('d/m/Y', strtotime($flow['data']['date']));

            return [
                "📋 <b>Xác nhận thông tin đặt sân:</b><br><br>" .
                "📍 Cơ sở: <b>{$flow['data']['facility_name']}</b><br>" .
                "🎾 Sân: <b>$courtName</b><br>" .
                "📅 Ngày: <b>$formattedDate</b><br>" .
                "⏰ Giờ: <b>$formattedTime</b><br><br>" .
                "Gõ <b>Xác nhận</b> để đặt sân hoặc <b>Hủy</b> để hủy bỏ."
            ];
        }

        return ['❓ Vui lòng chọn số sân. VD: Sân 1, Sân 2...'];
    }

    private function handleFlow1ConfirmBooking(string $message, Request $request = null): array
    {
        $message = mb_strtolower(trim($message));
        
        if (str_contains($message, 'hủy')) {
            session()->forget('booking_flow');
            return ['❌ Đã hủy đặt sân. Gõ "Đặt sân" để bắt đầu lại.'];
        }

        if (str_contains($message, 'xác nhận') || str_contains($message, 'đồng ý') || str_contains($message, 'ok')) {
            $flow = session('booking_flow');
            $userId = auth()->id();
            
            $result = $this->booking->createBooking(
                $userId,
                $flow['data']['facility_id'],
                $flow['data']['court_name'],
                $flow['data']['date'],
                $flow['data']['time']
            );

            session()->forget('booking_flow');

            if (isset($result['success']) && $result['success']) {
                // Lưu thông tin booking vào session để trang thanh toán lấy
                session([
                    'chatbot_payment_data' => [
                        'facility_id' => $result['facility_id'],
                        'slots' => $result['slots'],
                        'booking_id' => $result['booking_id'],
                    ]
                ]);

                $paymentUrl = route('chatbot.payment', ['booking_id' => $result['booking_id']]);
                
                return [
                    "✅ <b>Đặt sân thành công!</b><br><br>" .
                    "🎫 Mã đặt sân: <b>{$result['booking_code']}</b><br>" .
                    "💰 Tổng tiền: <b>" . number_format($result['total'], 0, ',', '.') . "đ</b><br><br>" .
                    "💳 Vui lòng thanh toán để hoàn tất:<br>" .
                    "👉 <a href='$paymentUrl' target='_blank' style='color: #667eea; font-weight: bold;'>NHẤN VÀO ĐÂY ĐỂ THANH TOÁN</a>"
                ];
            } else {
                return ["❌ " . ($result['message'] ?? 'Có lỗi xảy ra khi đặt sân.')];
            }
        }

        return ['❓ Vui lòng gõ <b>Xác nhận</b> để đặt sân hoặc <b>Hủy</b> để hủy bỏ.'];
    }

    // LUỒNG 2: User chưa biết cơ sở
    private function handleFlow2AskTime(string $message, array $nluData, Request $request = null): array
    {
        $time = $nluData['entities']['time'] ?? null;

        if (!$time) {
            return ['⏰ Vui lòng cho biết giờ muốn đặt.<br>VD: 18h, 20h, 19:30...'];
        }

        $flow = session('booking_flow');
        $flow['step'] = 'flow2_ask_date';
        $flow['data']['time'] = $time;
        
        if ($request) session(['booking_flow' => $flow]);

        return ['📅 Bạn muốn đặt vào ngày nào?<br>VD: <b>hôm nay</b>, <b>ngày mai</b>, <b>25/12</b>'];
    }

    private function handleFlow2AskDate(string $message, array $nluData, Request $request = null): array
    {
        $date = $nluData['entities']['date'] ?? null;

        if (!$date) {
            // Nếu không extract được, mặc định là hôm nay
            $date = date('Y-m-d');
        }

        $flow = session('booking_flow');
        $time = $flow['data']['time'];
        
        // Tìm các cơ sở còn sân trống
        $result = $this->booking->checkAvailabilityAllFacilities($date, $time);
        
        if (isset($result['error'])) {
            return [$result['error']];
        }

        $facilities = $result['results'] ?? [];

        if (empty($facilities)) {
            return [
                "❌ Rất tiếc, không có cơ sở nào còn sân trống vào " . 
                date('H:i', strtotime($time)) . " ngày " . date('d/m/Y', strtotime($date)) . ".<br><br>" .
                "💡 Vui lòng chọn giờ khác hoặc gõ <b>Hủy</b> để kết thúc."
            ];
        }

        $flow['step'] = 'flow2_show_facilities';
        $flow['data']['date'] = $date;
        $flow['data']['facilities'] = $facilities;
        
        if ($request) session(['booking_flow' => $flow]);

        $msg = "🔍 Tìm thấy <b>" . count($facilities) . " cơ sở</b> còn sân trống:<br><br>";
        
        foreach ($facilities as $index => $facility) {
            $msg .= ($index + 1) . ". <b>{$facility['facility_name']}</b><br>";
            if (!empty($facility['address'])) {
                $msg .= "   📌 {$facility['address']}<br>";
            }
            $msg .= "   ✅ Còn: " . implode(', ', $facility['available_courts']) . "<br><br>";
        }

        $msg .= "📍 Bạn muốn đặt tại cơ sở nào?<br>VD: Nhập tên cơ sở hoặc số thứ tự";

        return [$msg];
    }

    private function handleFlow2ShowFacilities(string $message, Request $request = null): array
    {
        $flow = session('booking_flow');
        $facilities = $flow['data']['facilities'] ?? [];
        
        // Kiểm tra xem user nhập số hay tên
        if (is_numeric(trim($message))) {
            $index = (int)trim($message) - 1;
            if (isset($facilities[$index])) {
                $selectedFacility = $facilities[$index];
            } else {
                return ['❓ Số thứ tự không hợp lệ. Vui lòng chọn từ 1 đến ' . count($facilities)];
            }
        } else {
            $facilityName = $this->extractFacilityNameFromMessage($message);
            $selectedFacility = null;
            
            foreach ($facilities as $facility) {
                if (stripos($facility['facility_name'], $facilityName) !== false) {
                    $selectedFacility = $facility;
                    break;
                }
            }
            
            if (!$selectedFacility) {
                return ['❌ Không tìm thấy cơ sở trong danh sách. Vui lòng chọn lại.'];
            }
        }

        $flow['step'] = 'flow2_select_court';
        $flow['data']['selected_facility'] = $selectedFacility;
        
        if ($request) session(['booking_flow' => $flow]);

        $courtsList = implode(', ', array_map(function($court) {
            return "<b>$court</b>";
        }, $selectedFacility['available_courts']));

        return [
            "✅ Đã chọn: <b>{$selectedFacility['facility_name']}</b><br><br>" .
            "🎾 Các sân còn trống: $courtsList<br><br>" .
            "Bạn muốn đặt sân nào?<br>VD: Sân 1, Sân 3..."
        ];
    }

    private function handleFlow2SelectCourt(string $message, Request $request = null): array
    {
        $flow = session('booking_flow');
        $selectedFacility = $flow['data']['selected_facility'];
        $availableCourts = $selectedFacility['available_courts'];
        
        // Extract số sân
        if (preg_match('/sân\s*(\d+)/iu', $message, $matches)) {
            $courtNumber = $matches[1];
            $courtName = "Sân " . $courtNumber;
            
            if (!in_array($courtName, $availableCourts)) {
                return ["❌ Sân $courtNumber không khả dụng. Vui lòng chọn: " . implode(', ', $availableCourts)];
            }
            
            $flow['step'] = 'flow2_confirm_booking';
            $flow['data']['court_name'] = $courtName;
            
            if ($request) session(['booking_flow' => $flow]);

            $formattedTime = date('H:i', strtotime($flow['data']['time']));
            $formattedDate = date('d/m/Y', strtotime($flow['data']['date']));

            return [
                "📋 <b>Xác nhận thông tin đặt sân:</b><br><br>" .
                "📍 Cơ sở: <b>{$selectedFacility['facility_name']}</b><br>" .
                "🎾 Sân: <b>$courtName</b><br>" .
                "📅 Ngày: <b>$formattedDate</b><br>" .
                "⏰ Giờ: <b>$formattedTime</b><br><br>" .
                "Gõ <b>Xác nhận</b> để đặt sân hoặc <b>Hủy</b> để hủy bỏ."
            ];
        }

        return ['❓ Vui lòng chọn số sân. VD: Sân 1, Sân 2...'];
    }

    private function handleFlow2ConfirmBooking(string $message, Request $request = null): array
    {
        $message = mb_strtolower(trim($message));
        
        if (str_contains($message, 'hủy')) {
            session()->forget('booking_flow');
            return ['❌ Đã hủy đặt sân. Gõ "Đặt sân" để bắt đầu lại.'];
        }

        if (str_contains($message, 'xác nhận') || str_contains($message, 'đồng ý') || str_contains($message, 'ok')) {
            $flow = session('booking_flow');
            $userId = auth()->id();
            $selectedFacility = $flow['data']['selected_facility'];
            
            $result = $this->booking->createBooking(
                $userId,
                $selectedFacility['facility_id'],
                $flow['data']['court_name'],
                $flow['data']['date'],
                $flow['data']['time']
            );

            session()->forget('booking_flow');

            if (isset($result['success']) && $result['success']) {
                // Lưu thông tin booking vào session để trang thanh toán lấy
                session([
                    'chatbot_payment_data' => [
                        'facility_id' => $result['facility_id'],
                        'slots' => $result['slots'],
                        'booking_id' => $result['booking_id'],
                    ]
                ]);

                $paymentUrl = route('chatbot.payment', ['booking_id' => $result['booking_id']]);
                
                return [
                    "✅ <b>Đặt sân thành công!</b><br><br>" .
                    "🎫 Mã đặt sân: <b>{$result['booking_code']}</b><br>" .
                    "💰 Tổng tiền: <b>" . number_format($result['total'], 0, ',', '.') . "đ</b><br><br>" .
                    "💳 Vui lòng thanh toán để hoàn tất:<br>" .
                    "👉 <a href='$paymentUrl' target='_blank' style='color: #667eea; font-weight: bold;'>NHẤN VÀO ĐÂY ĐỂ THANH TOÁN</a>"
                ];
            } else {
                return ["❌ " . ($result['message'] ?? 'Có lỗi xảy ra khi đặt sân.')];
            }
        }

        return ['❓ Vui lòng gõ <b>Xác nhận</b> để đặt sân hoặc <b>Hủy</b> để hủy bỏ.'];
    }

    // ==================== HELPER METHODS ====================

    private function buildAvailabilityResponse(array $nluData): string
    {
        $date = $nluData['entities']['date'] ?? null;
        $time = $nluData['entities']['time'] ?? null;

        if (!$date) {
            $date = date('Y-m-d');
        }

        if (!$time) {
            return '⏰ Bạn vui lòng cung cấp giờ cụ thể để tôi kiểm tra sân trống.<br>VD: "sân trống 18h hôm nay" hoặc "20h ngày mai"';
        }

        $result = $this->booking->checkAvailability($date, $time);

        if (isset($result['error'])) {
            return $result['error'];
        }

        $facilityName = $result['facility_name'] ?? 'Cơ sở này';
        $formattedTime = date('H:i', strtotime($time));
        $formattedDate = date('d/m/Y', strtotime($date));

        if (!empty($result['is_full'])) {
            $slotId = $result['slot_id'] ?? null;
            $suggestions = $slotId ? $this->booking->suggestAlternative($date, $slotId) : [];

            $msg = "❌ Rất tiếc, tại <b>$facilityName</b> lúc $formattedTime ngày $formattedDate đã hết sân.";
            if (!empty($suggestions)) {
                $msg .= "<br><br>💡 <b>Gợi ý giờ trống gần đó:</b> " . implode(', ', $suggestions);
            }

            return $msg;
        }

        $available = $result['available'] ?? [];
        if (empty($available)) {
            return "❌ Tại <b>$facilityName</b> hiện không có sân trống lúc $formattedTime ngày $formattedDate.";
        }

        return "✅ Tại <b>$facilityName</b> còn trống các sân: <b>" . implode(', ', $available) . "</b><br>Lúc $formattedTime ngày $formattedDate";
    }

    private function buildBookingHistoryResponse(): string
    {
        $userId = auth()->id();
        if (!$userId) {
            return '🔒 Bạn cần đăng nhập để xem lịch sử đặt sân.';
        }

        $history = $this->booking->getMyBookings($userId);
        if ($history->isEmpty()) {
            return '📅 Bạn chưa có lịch đặt nào sắp tới.';
        }

        $msg = "📅 <b>Lịch sử đặt sân của bạn:</b><br>";
        foreach ($history as $h) {
            $formattedDate = date('d/m/Y', strtotime($h->booking_date));
            $msg .= "• Ngày $formattedDate: {$h->status}<br>";
        }

        return $msg;
    }

    private function buildOtherFacilitiesResponse(array $nluData): string
    {
        $date = $nluData['entities']['date'] ?? null;
        $time = $nluData['entities']['time'] ?? null;

        if (!$date) {
            $date = date('Y-m-d');
        }

        if (!$time) {
            return '⏰ Bạn vui lòng cung cấp giờ cụ thể để tôi tìm các cơ sở khác có sân trống.<br>VD: "18h" hoặc "20h hôm nay"';
        }

        $result = $this->booking->checkAvailabilityAllFacilities($date, $time);

        if (isset($result['error'])) {
            return $result['error'];
        }

        $facilities = $result['results'] ?? [];
        $formattedTime = date('H:i', strtotime($time));
        $formattedDate = date('d/m/Y', strtotime($date));

        if (empty($facilities)) {
            return "❌ Rất tiếc, không có cơ sở nào còn sân trống lúc $formattedTime ngày $formattedDate.";
        }

        $msg = "🔍 Tìm thấy <b>" . count($facilities) . " cơ sở</b> còn sân trống lúc $formattedTime ngày $formattedDate:<br><br>";

        foreach ($facilities as $facility) {
            $msg .= "📍 <b>" . $facility['facility_name'] . "</b><br>";
            if (!empty($facility['address'])) {
                $msg .= "   📌 Địa chỉ: " . $facility['address'] . "<br>";
            }
            $msg .= "   ✅ Còn trống: <b>" . implode(', ', $facility['available_courts']) . "</b> (" . $facility['count'] . " sân)<br><br>";
        }

        return $msg;
    }

    private function extractFacilityNameFromMessage(string $message): ?string
    {
        $message = preg_replace('/(giá|bao nhiêu|chi phí|xem|tôi muốn|cho tôi|muốn|hỏi|của|ở|tại|sân|cơ\s*sở)/iu', '', $message);
        $message = preg_replace('/\s+/', ' ', $message);
        $message = trim($message);

        if (strlen($message) < 3 || !preg_match('/[a-zA-ZÀ-ỹ]/u', $message)) {
            return null;
        }

        return $message;
    }

    private function clearAllSessions(Request $request = null): void
    {
        if ($request) {
            session()->forget([
                'booking_flow',
                'chatbot_finding_other_facilities',
                'chatbot_checking_price'
            ]);
        }
    }

    // Method hiển thị trang thanh toán cho chatbot booking
    public function showPaymentPage($booking_id)
    {
        $booking = Bookings::with(['facility', 'court', 'timeSlot'])
            ->where('booking_id', $booking_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            abort(404, 'Không tìm thấy đơn đặt sân');
        }

        // Lấy payment data từ session
        $paymentData = session('chatbot_payment_data');
        
        if (!$paymentData) {
            return redirect()->route('home')->with('error', 'Phiên đặt sân đã hết hạn');
        }

        // Chuẩn bị dữ liệu cho view
        $facilities = $booking->facility;
        $customer = auth()->user();
        
        // Format slots data
        $slots = $paymentData['slots'];
        $total = 0;
        foreach ($slots as $slot) {
            $total += $slot['price'];
        }

        // Tính thông tin hiển thị
        $uniqueCourts = implode(', ', array_unique(array_column($slots, 'court')));
        $uniqueDates = implode(', ', array_unique(array_column($slots, 'date')));
        $uniqueTimes = implode(', ', array_map(function($slot) {
            return $slot['start_time'] . ' - ' . $slot['end_time'];
        }, $slots));
        
        // Tính tổng thời gian (giả sử mỗi slot là 1 giờ)
        $totalHours = count($slots);
        $result = $totalHours . ' giờ';

        // Xóa session sau khi lấy xong
        session()->forget('chatbot_payment_data');

        return view('payments_complete', compact(
            'facilities',
            'customer',
            'slots',
            'total',
            'uniqueCourts',
            'uniqueDates',
            'uniqueTimes',
            'result'
        ));
    }
}