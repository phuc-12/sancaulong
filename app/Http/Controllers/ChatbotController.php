<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Facilities;
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

        // XỬ LÝ "CÒN SÂN KHÁC KHÔNG"
        // Kiểm tra nếu user hỏi "còn sân khác", "cơ sở khác"
        if ($intent === 'find_other_facilities') {
            // Nếu KHÔNG CÓ thời gian trong câu hỏi hiện tại
            if (!$nluData['entities']['time'] || !$nluData['entities']['date']) {
                // Lấy context từ session (từ lần hỏi trước)
                $lastContext = session('chatbot_last_query_context');

                if ($lastContext && isset($lastContext['time']) && isset($lastContext['date'])) {
                    // Tự động dùng lại thời gian và ngày từ context
                    $nluData['entities']['time'] = $lastContext['time'];
                    $nluData['entities']['date'] = $lastContext['date'];

                    // KHÔNG GỬI MESSAGE RIÊNG - Sẽ được xử lý trong buildOtherFacilitiesResponse
                } else {
                    // Không có context trước đó
                    if ($request) {
                        session(['chatbot_finding_other_facilities' => true]);
                    }
                    $responses[] = '⏰ Bạn muốn tìm sân vào khung giờ nào?<br>VD: "18h hôm nay", "20h ngày mai"';
                    return $responses;
                }
            }
        }

        // Nếu đang trong flow tìm cơ sở khác (đã set flag trước đó)
        if (
            $isFindingOtherFacilities && $nluData['entities']['time'] &&
            ($intent === 'check_availability' || $intent === 'unknown')
        ) {
            $intent = 'find_other_facilities';
            $nluData['intent'] = 'find_other_facilities';
        }

        switch ($intent) {
            case 'greeting':
                $responses[] = 'Xin chào 👋! Tôi là AI hỗ trợ đặt sân. Tôi có thể giúp bạn:<br>• Đặt sân<br>• Kiểm tra giờ trống<br>• Xem giá<br>• Tìm cơ sở khác';
                $this->clearAllSessions($request);
                break;

            case 'booking_request':
                $responses[] = $this->startBookingFlow($nluData, $request);
                break;

            case 'check_price':
                $facilityName = $nluData['entities']['facility_name'] ?? null;

                if (!$facilityName) {
                    session(['chatbot_checking_price' => true]);
                    $responses[] = 'Bạn muốn xem giá sân ở cơ sở nào? Vui lòng cho tôi biết tên cơ sở.<br>VD: Thủ Đức, Quận 1, Hóc môn...';
                } else {
                    $priceInfo = $this->booking->getPriceInfo($facilityName);

                    if ($priceInfo === null) {
                        session(['chatbot_checking_price' => true]);
                        $responses[] = '❌ Không tìm thấy cơ sở "<b>' . htmlspecialchars($facilityName) . '</b>".<br>Vui lòng kiểm tra lại tên cơ sở hoặc thử tên khác.<br>VD: Thủ Đức, Quận 1, Hóc môn...';
                    } else {
                        if (is_array($priceInfo)) {
                            $responses[] = $priceInfo['message'] . $this->generateBookingButton($priceInfo['booking_data']);

                            if (!empty($priceInfo['similar_facilities'])) {
                                $similarMsg = "<br>💡 <b>Các cơ sở có giá tương tự:</b><br>";
                                foreach ($priceInfo['similar_facilities'] as $similar) {
                                    $similarMsg .= "📍 <b>{$similar['facility_name']}</b> - ";
                                    $similarMsg .= "Giá: " . number_format($similar['default_price'], 0, ',', '.') . "đ";
                                    if (!empty($similar['address'])) {
                                        $similarMsg .= " ({$similar['address']})";
                                    }
                                    $similarMsg .= $this->generateBookingButton([
                                        'facility_id' => $similar['facility_id'],
                                        'facility_name' => $similar['facility_name']
                                    ]);
                                    $similarMsg .= "<br>";
                                }
                                $responses[] = $similarMsg;
                            }
                        } else {
                            $responses[] = $priceInfo;
                        }
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
                $responses[] = $this->buildAvailabilityResponse($nluData, $request);
                break;

            case 'find_other_facilities':
                if ($request && !$nluData['entities']['time']) {
                    session(['chatbot_finding_other_facilities' => true]);
                } else if ($request) {
                    session()->forget('chatbot_finding_other_facilities');
                }
                $responses[] = $this->buildOtherFacilitiesResponse($nluData, $request);

                if ($request) {
                    session()->forget('chatbot_checking_price');
                }
                break;

            default:
                if ($isFindingOtherFacilities && $nluData['entities']['time']) {
                    $nluData['intent'] = 'find_other_facilities';
                    $responses[] = $this->buildOtherFacilitiesResponse($nluData, $request);
                    if ($request) {
                        session()->forget('chatbot_finding_other_facilities');
                    }
                } else if ($isCheckingPrice) {
                    $facilityName = $this->extractFacilityNameFromMessage($message);

                    if ($facilityName) {
                        $priceInfo = $this->booking->getPriceInfo($facilityName);

                        if ($priceInfo === null) {
                            $responses[] = '❌ Không tìm thấy cơ sở "<b>' . htmlspecialchars($facilityName) . '</b>".<br>Vui lòng nhập tên cơ sở khác.<br>VD: Thủ Đức, Quận 1, Hóc môn...';
                        } else {
                            if (is_array($priceInfo)) {
                                $responses[] = $priceInfo['message'] . $this->generateBookingButton($priceInfo['booking_data']);
                            } else {
                                $responses[] = $priceInfo;
                            }
                            if ($request) {
                                session()->forget('chatbot_checking_price');
                            }
                        }
                    } else {
                        $responses[] = '❓ Tôi không nhận diện được tên cơ sở trong tin nhắn của bạn.<br>Vui lòng nhập lại tên cơ sở rõ ràng hơn.<br>VD: Thủ Đức, Quận 1, Hóc môn...';
                    }
                } else {
                    $responses[] = '😅 Xin lỗi, tôi chưa hiểu ý bạn.<br>Hãy thử:<br>• "Kiểm tra sân trống hôm nay 18h"<br>• "Giá sân bao nhiêu"';
                }
                break;
        }

        return $responses;
    }


    private function generateBookingButton(array $bookingData): string
    {
        $facilityId = $bookingData['facility_id'] ?? '';
        $facilityName = $bookingData['facility_name'] ?? '';
        $date = $bookingData['date'] ?? '';
        $time = $bookingData['time'] ?? '';
        $slotId = $bookingData['slot_id'] ?? '';

        $user = auth()->user();
        $userName = $user ? $user->fullname : '';
        $userPhone = $user ? $user->phone : '';
        $userEmail = $user ? $user->email : '';

        $csrfToken = csrf_token();
        $formId = 'booking-form-' . uniqid();

        return <<<HTML
        <br><br>
        <form id="$formId" action="/venue" method="POST" style="display: inline;">
            <input type="hidden" name="_token" value="$csrfToken">
            <input type="hidden" name="facility_id" value="$facilityId">
            <input type="hidden" name="facility_name" value="$facilityName">
            <input type="hidden" name="date" value="$date">
            <input type="hidden" name="time" value="$time">
            <input type="hidden" name="slot_id" value="$slotId">
            <input type="hidden" name="customer_name" value="$userName">
            <input type="hidden" name="customer_phone" value="$userPhone">
            <input type="hidden" name="customer_email" value="$userEmail">
            <button type="submit" style="
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: bold;
                cursor: pointer;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                transition: all 0.3s;
            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.15)';" 
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';">
                🎾 ĐẶT SÂN NGAY
            </button>
        </form>
HTML;
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

    // ==================== BOOKING FLOW ====================

    private function startBookingFlow(array $nluData, Request $request = null): string
    {
        if (!auth()->id()) {
            return '🔒 Bạn cần đăng nhập để đặt sân.';
        }

        $flow = [
            'step' => 'ask_flow_choice',
            'data' => []
        ];

        if ($request) {
            session(['booking_flow' => $flow]);
        }

        return "🎾 Bạn muốn đặt sân như thế nào?<br><br>" .
            "1️⃣ Tôi biết cơ sở muốn đặt<br>" .
            "2️⃣ Giúp tôi tìm cơ sở phù hợp<br><br>" .
            "Vui lòng nhập 1 hoặc 2";
    }

    private function handleBookingFlow(string $message, array $nluData, Request $request = null): array
    {
        $flow = session('booking_flow');
        $step = $flow['step'] ?? null;
        $data = $flow['data'] ?? [];

        switch ($step) {
            case 'ask_flow_choice':
                return $this->handleFlowChoice($message, $request);

            // ================= LUỒNG 1: BIẾT CƠ SỞ =================
            case 'flow1_ask_facility':
                return $this->handleFlow1AskFacility($message, $nluData, $request);

            case 'flow1_select_time_date':
                return $this->handleFlow1SelectTimeDate($message, $nluData, $request);

            // --- BƯỚC HỎI THỜI LƯỢNG ---
            case 'flow1_ask_duration':
                return $this->handleFlow1AskDuration($message, $request);
            // -------------------------------------------

            case 'flow1_select_court':
                return $this->handleFlow1SelectCourt($message, $request);

            case 'flow1_confirm_booking':
                return $this->handleFlow1ConfirmBooking($message, $request);


            // ================= LUỒNG 2: TÌM CƠ SỞ =================
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
                // Nếu không khớp bước nào, xóa session để tránh kẹt và báo lỗi
                session()->forget('booking_flow');
                return ['❌ Có lỗi xảy ra (Lỗi Flow). Vui lòng thử lại bằng cách gõ "Đặt sân"'];
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
            if ($request)
                session(['booking_flow' => $flow]);

            return ['📍 Bạn muốn đặt sân tại cơ sở nào?<br>VD: Thủ Đức, Quận 1, Hóc môn...'];
        } else if ($choice === '2') {
            $flow = [
                'step' => 'flow2_ask_time',
                'data' => ['flow_type' => 2]
            ];
            if ($request)
                session(['booking_flow' => $flow]);

            return ['⏰ Bạn muốn đặt sân vào khung giờ nào?<br>VD: 18h, 20h, 19:30...'];
        } else {
            return ['❓ Vui lòng chọn <b>1</b> hoặc <b>2</b>'];
        }
    }

    private function handleFlow1AskFacility(string $message, array $nluData, Request $request = null): array
    {
        $facilityName = $this->extractFacilityNameFromMessage($message);

        if (!$facilityName) {
            return ['❓ Tôi không nhận diện được tên cơ sở. Vui lòng nhập lại.<br>VD: Thủ Đức, Quận 1, Hóc môn...'];
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

        if ($request)
            session(['booking_flow' => $flow]);

        return [
            "✅ Đã chọn cơ sở: <b>{$facility['facility_name']}</b><br><br>" .
            "📅 Bạn muốn đặt vào ngày nào và giờ nào?<br>" .
            "VD: <b>18h hôm nay</b>, <b>20h ngày mai</b>, <b>19:30 ngày 25/12</b>"
        ];
    }

    private function handleFlow1SelectTimeDate(string $message, array $nluData, Request $request = null): array
    {
        $time = $nluData['entities']['time'] ?? null;
        // Giữ lại ngày đã chọn hoặc lấy ngày hôm nay
        $flow = session('booking_flow');
        $date = $nluData['entities']['date'] ?? ($flow['data']['date'] ?? date('Y-m-d'));

        if (!$time) {
            return ['⏰ Bạn muốn bắt đầu đánh từ mấy giờ?<br>VD: 18h, 19:30...'];
        }

        // Cập nhật flow
        $flow['step'] = 'flow1_ask_duration'; // CHUYỂN SANG BƯỚC MỚI
        $flow['data']['date'] = $date;
        $flow['data']['time'] = $time; // Giờ bắt đầu

        if ($request)
            session(['booking_flow' => $flow]);

        return [
            "🕒 Bạn muốn đặt sân trong bao lâu?<br>" .
            "VD: <b>1 tiếng</b>, <b>1.5 giờ</b>, hoặc <b>2 tiếng</b>..."
        ];
    }
    // Hàm xử lý chọn sân (Sau khi đã chọn thời lượng)
    private function handleFlow1SelectCourt(string $message, Request $request = null): array
    {
        $flow = session('booking_flow');
        $availableCourts = $flow['data']['available_courts'] ?? [];

        // 1. Xử lý input: User có thể nhập "Sân 1" hoặc chỉ nhập "1"
        // Regex bắt số: "sân 1", "san 1", "1"
        if (preg_match('/(sân\s*)?(\d+)/iu', $message, $matches)) {
            $courtNumber = $matches[2]; // Lấy con số
            $courtName = "Sân " . $courtNumber; // Format chuẩn tên sân trong DB

            // 2. Kiểm tra xem sân này có trong danh sách sân trống không
            // Lưu ý: Cần so sánh tương đối hoặc chính xác tùy dữ liệu DB
            // Ở đây ta so sánh string đơn giản
            $isValid = false;
            foreach ($availableCourts as $avCourt) {
                if (stripos($avCourt, $courtName) !== false) {
                    $isValid = true;
                    // Lấy đúng tên trong danh sách để lưu (tránh hoa thường)
                    $courtName = $avCourt;
                    break;
                }
            }

            if (!$isValid) {
                return [
                    "❌ Sân <b>$courtNumber</b> không khả dụng hoặc đã có người đặt.<br>" .
                    "Vui lòng chọn trong danh sách: <b>" . implode(', ', $availableCourts) . "</b>"
                ];
            }

            // 3. Hợp lệ -> Chuyển sang bước xác nhận
            $flow['step'] = 'flow1_confirm_booking';
            $flow['data']['court_name'] = $courtName;

            if ($request)
                session(['booking_flow' => $flow]);

            // Format lại hiển thị
            $time = $flow['data']['time'];
            $date = date('d/m/Y', strtotime($flow['data']['date']));
            $duration = $flow['data']['duration'] ?? 1; // Mặc định 1 tiếng nếu thiếu

            return [
                "📋 <b>XÁC NHẬN THÔNG TIN:</b><br><br>" .
                "📍 Cơ sở: <b>{$flow['data']['facility_name']}</b><br>" .
                "🎾 Sân: <b>$courtName</b><br>" .
                "📅 Ngày: <b>$date</b><br>" .
                "⏰ Bắt đầu: <b>$time</b><br>" .
                "⏳ Thời lượng: <b>$duration tiếng</b><br><br>" .
                "Gõ <b>Xác nhận</b> để đặt sân hoặc <b>Hủy</b> để chọn lại."
            ];
        }

        return ['❓ Vui lòng chọn số sân. VD: Sân 1, Sân 2...'];
    }
    private function handleFlow1AskDuration(string $message, Request $request = null): array
    {
        $duration = $this->nlu->extractDuration($message);

        // Nếu user nhập số không (VD: "2"), ta hiểu ngầm là giờ
        if (!$duration && is_numeric(trim($message))) {
            $duration = (float) trim($message);
        }

        if (!$duration || $duration < 0.5) {
            return ['❓ Vui lòng nhập thời gian tối thiểu 0.5 tiếng (30 phút).<br>VD: 1 tiếng, 1.5 giờ...'];
        }

        $flow = session('booking_flow');
        $flow['data']['duration'] = $duration;

        // Kiểm tra sân trống dựa trên (Cơ sở + Ngày + Giờ Bắt Đầu + Thời Lượng)
        $availability = $this->booking->checkAvailabilityForDuration(
            $flow['data']['facility_id'],
            $flow['data']['date'],
            $flow['data']['time'],
            $duration
        );

        if (isset($availability['error'])) {
            return [$availability['error']];
        }

        if (empty($availability['available'])) {
            return ["❌ Không có sân nào trống liên tục trong {$duration} tiếng bắt đầu từ {$flow['data']['time']}."];
        }

        $flow['step'] = 'flow1_select_court';
        $flow['data']['available_courts'] = $availability['available'];
        if ($request)
            session(['booking_flow' => $flow]);

        $courtsList = implode(', ', array_map(fn($c) => "<b>$c</b>", $availability['available']));

        return [
            "✅ Tìm thấy sân trống cho <b>{$duration} giờ</b>:<br>$courtsList<br><br>" .
            "🎾 Bạn chọn sân nào? (VD: Sân 1)"
        ];
    }

    private function handleFlow1ConfirmBooking(string $message, Request $request = null): array
    {
        $message = mb_strtolower(trim($message));
        if (str_contains($message, 'hủy')) {
            session()->forget('booking_flow');
            return ['❌ Đã hủy.'];
        }

        if (str_contains($message, 'xác nhận') || str_contains($message, 'ok')) {
            $flow = session('booking_flow');
            $userId = auth()->id();

            try {
                // GỌI SERVICE VỚI THAM SỐ MỚI (Duration)
                $result = $this->booking->createBookingMultiSlots(
                    $userId,
                    $flow['data']['facility_id'],
                    $flow['data']['court_name'],
                    $flow['data']['date'],
                    $flow['data']['time'],
                    $flow['data']['duration'] ?? 1 // Mặc định 1 tiếng nếu thiếu
                );

                if ($result['success']) {
                    session()->forget('booking_flow');
                    // ... (Code session payment cũ giữ nguyên) ...
                    $paymentUrl = route('chatbot.payment', ['booking_id' => $result['booking_id']]);
                    return [
                        "✅ <b>Đặt sân thành công!</b> (Đã đặt {$result['slot_count']} khung giờ)<br>" .
                        "💰 Tổng tiền: " . number_format($result['total'], 0, ',', '.') . "đ<br>" .
                        "👉 <a href='$paymentUrl' target='_blank'>THANH TOÁN NGAY</a>"
                    ];
                } else {
                    return ["❌ Lỗi: " . $result['message']];
                }

            } catch (\Exception $e) {
                // --- QUAN TRỌNG: IN LỖI RA MÀN HÌNH CHAT ĐỂ BẠN THẤY ---
                return [
                    "❌ <b>Đã xảy ra lỗi hệ thống (Debug):</b><br>" .
                    "<i>" . $e->getMessage() . "</i><br>" .
                    "Tại dòng: " . $e->getLine()
                ];
            }
        }
        return ['❓ Gõ "Xác nhận" để đặt hoặc "Hủy".'];
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

        if ($request)
            session(['booking_flow' => $flow]);

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

        if ($request)
            session(['booking_flow' => $flow]);

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
            $index = (int) trim($message) - 1;
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

        if ($request)
            session(['booking_flow' => $flow]);

        $courtsList = implode(', ', array_map(function ($court) {
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

            if ($request)
                session(['booking_flow' => $flow]);

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

    private function buildAvailabilityResponse(array $nluData, Request $request = null): string
    {
        $date = $nluData['entities']['date'] ?? null;
        $time = $nluData['entities']['time'] ?? null;

        if (!$date) {
            $date = date('Y-m-d');
        }

        if (!$time) {
            return '⏰ Bạn vui lòng cung cấp giờ cụ thể để tôi kiểm tra sân trống.<br>VD: "sân trống 18h hôm nay" hoặc "20h ngày mai"';
        }

        //LƯU CONTEXT VÀO SESSION
        if ($request) {
            session([
                'chatbot_last_query_context' => [
                    'time' => $time,
                    'date' => $date,
                    'intent' => 'check_availability',
                    'timestamp' => now()
                ]
            ]);
        }

        $result = $this->booking->checkAvailability($date, $time);

        if (isset($result['error'])) {
            return $result['error'];
        }

        $facilityName = $result['facility_name'] ?? 'Cơ sở này';
        $bookingData = $result['booking_data'] ?? [];
        $formattedTime = date('H:i', strtotime($time));
        $formattedDate = date('d/m/Y', strtotime($date));

        if (!empty($result['is_full'])) {
            $slotId = $result['slot_id'] ?? null;
            $suggestions = $slotId ? $this->booking->suggestAlternative($date, $slotId) : [];

            $msg = "❌ Rất tiếc, tại <b>$facilityName</b> lúc $formattedTime ngày $formattedDate đã hết sân.";
            if (!empty($suggestions)) {
                $msg .= "<br><br>💡 <b>Gợi ý giờ trống gần đó:</b> " . implode(', ', $suggestions);
            }

            // THÊM GỢI Ý TÌM CƠ SỞ KHÁC
            $msg .= "<br><br>💬 Bạn có thể hỏi: <i>\"Còn sân khác không?\"</i> để tôi tìm các cơ sở khác.";

            if (!empty($bookingData)) {
                $msg .= $this->generateBookingButton($bookingData);
            }

            return $msg;
        }

        $available = $result['available'] ?? [];
        if (empty($available)) {
            $msg = "❌ Tại <b>$facilityName</b> hiện không có sân trống lúc $formattedTime ngày $formattedDate.";
            $msg .= "<br><br>💬 Bạn có thể hỏi: <i>\"Còn sân khác không?\"</i> để tôi tìm các cơ sở khác.";
            return $msg;
        }

        $msg = "✅ Tại <b>$facilityName</b> còn trống các sân: <b>" . implode(', ', $available) . "</b><br>Lúc $formattedTime ngày $formattedDate";

        if (!empty($bookingData)) {
            $msg .= $this->generateBookingButton($bookingData);
        }

        // THÊM GỢI Ý TÌM CƠ SỞ KHÁC
        $msg .= "<br><br>💬 Hoặc hỏi: <i>\"Còn cơ sở khác không?\"</i>";

        return $msg;
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

    private function buildOtherFacilitiesResponse(array $nluData, Request $request = null): string
    {
        $date = $nluData['entities']['date'] ?? null;
        $time = $nluData['entities']['time'] ?? null;

        if (!$date) {
            $date = date('Y-m-d');
        }

        if (!$time) {
            return '⏰ Bạn vui lòng cung cấp giờ cụ thể để tôi tìm các cơ sở khác có sân trống.<br>VD: "18h" hoặc "20h hôm nay"';
        }

        // ============ LƯU CONTEXT VÀO SESSION ============
        if ($request) {
            session([
                'chatbot_last_query_context' => [
                    'time' => $time,
                    'date' => $date,
                    'intent' => 'find_other_facilities',
                    'timestamp' => now()
                ]
            ]);
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

        // THÊM HEADER THÔNG BÁO (Gộp vào cùng message)
        $msg = "🔍 <b>Đang tìm các cơ sở khác còn sân trống lúc $formattedTime ngày $formattedDate...</b><br><br>";

        $msg .= "✅ Tìm thấy <b>" . count($facilities) . " cơ sở</b>:<br><br>";

        foreach ($facilities as $facility) {
            $msg .= "📍 <b>" . $facility['facility_name'] . "</b><br>";
            if (!empty($facility['address'])) {
                $msg .= "   📌 Địa chỉ: " . $facility['address'] . "<br>";
            }
            $msg .= "   ✅ Còn trống: <b>" . implode(', ', $facility['available_courts']) . "</b> (" . $facility['count'] . " sân)<br>";

            if (!empty($facility['booking_data'])) {
                $msg .= "   " . $this->generateBookingButton($facility['booking_data']);
            }

            $msg .= "<br>";
        }

        return $msg;
    }

    private function clearAllSessions(Request $request = null): void
    {
        if ($request) {
            session()->forget([
                'booking_flow',
                'chatbot_finding_other_facilities',
                'chatbot_checking_price',
                'chatbot_last_query_context' // XÓA CONTEXT KHI RESET
            ]);
        }
    }

    // Method hiển thị trang thanh toán cho chatbot booking
    public function showPaymentPage($booking_id)
    {
        // 1. Kiểm tra đăng nhập
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán');
        }

        $userId = auth()->id();

        // 2. Tìm Booking gốc dựa trên ID và User
        $mainBooking = Bookings::where('booking_id', $booking_id)
            ->where('user_id', $userId)
            ->first();

        if (!$mainBooking) {
            return redirect()->route('trang_chu')->with('error', 'Không tìm thấy đơn đặt sân hợp lệ.');
        }

        // 3. Lấy tất cả các slot thuộc cùng một mã đặt (invoice_detail_id)
        // Vì bạn đặt 2 tiếng -> có 4 dòng booking cùng mã BOT_xxx
        $relatedBookings = Bookings::where('invoice_detail_id', $mainBooking->invoice_detail_id)
            ->get();

        // 4. Tái tạo lại mảng $slots để view hiển thị
        $slots = [];
        $total = 0;

        foreach ($relatedBookings as $b) {
            // Lấy thông tin giờ
            $ts = \App\Models\Time_slots::where('time_slot_id', $b->time_slot_id)->first();
            // Lấy thông tin sân
            $ct = \App\Models\Courts::where('court_id', $b->court_id)
                ->where('facility_id', $b->facility_id)
                ->first();

            $slots[] = [
                'court' => $ct ? $ct->court_name : 'Sân ?',
                'start_time' => $ts ? date('H:i', strtotime($ts->start_time)) : '--:--',
                'end_time' => $ts ? date('H:i', strtotime($ts->end_time)) : '--:--',
                'date' => date('d-m-Y', strtotime($b->booking_date)),
                'price' => $b->unit_price,
                'time_slot_id' => $b->time_slot_id,
                'court_id' => $b->court_id,
            ];

            $total += $b->unit_price;
        }

        // 5. Lấy thông tin cơ sở vật chất
        $facilities = Facilities::find($mainBooking->facility_id);

        // 6. Lấy thông tin khách hàng
        $customer = \App\Models\Users::find($userId);

        // 7. Tính toán các thông tin hiển thị phụ
        $uniqueCourts = implode(', ', array_unique(array_column($slots, 'court')));
        $uniqueDates = implode(' / ', array_unique(array_column($slots, 'date')));

        // Format chuỗi giờ: 05:00 - 07:00 (Lấy min start và max end nếu liên tục, hoặc liệt kê)
        // Để đơn giản hiển thị slot đầu đến slot cuối
        $startTime = $slots[0]['start_time'];
        $endTime = $slots[count($slots) - 1]['end_time'];
        $uniqueTimes = "$startTime đến $endTime";

        // Tính tổng thời gian
        $countSlots = count($slots);
        $hours = $countSlots * 0.5; // Mỗi slot 30p
        $result = $hours . ' tiếng';

        // Customer info variables
        $customer_name = $customer->fullname ?? '';
        $customer_phone = $customer->phone ?? '';
        $customer_email = $customer->email ?? '';

        // 8. Trả về View (Chắc chắn sẽ hiện trang thanh toán)
        return view('payment', compact(
            'facilities',
            'customer',
            'customer_name',
            'customer_phone',
            'customer_email',
            'slots',
            'total',
            'uniqueCourts',
            'uniqueDates',
            'uniqueTimes',
            'result'
        ));
    }
}