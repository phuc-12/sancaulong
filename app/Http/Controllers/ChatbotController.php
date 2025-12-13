<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\ChatHistory;
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

    public function chat(Request $request): JsonResponse
    {
        // Thêm Try-Catch ở đây để bắt lỗi 500 và hiện ra chat
        try {
            $validated = $request->validate([
                'message' => 'required|string',
            ]);

            // 1. Xử lý tin nhắn
            $responses = $this->processMessage($validated['message'], $request);

            // 2. Lưu lịch sử
            $this->saveChatHistory($validated['message'], $responses, $request);

            return response()->json([
                'reply' => $responses[0] ?? '😅 Xin lỗi, tôi chưa hiểu ý bạn.',
                'replies' => $responses,
            ]);

        } catch (\Exception $e) {
            \Log::error('Chatbot Controller Error: ' . $e->getMessage());
            $errorMsg = "❌ <b>Lỗi hệ thống (Debug):</b><br>" . $e->getMessage() . "<br>Line: " . $e->getLine();
            return response()->json([
                'reply' => $errorMsg,
                'replies' => [$errorMsg],
            ]);
        }
    }

    private function saveChatHistory(string $message, $responses, Request $request = null): void
    {
        try {
            $nluData = $this->nlu->analyze($message);
            $userId = auth()->id();

            if (!is_array($responses)) {
                $responses = [$responses];
            }

            ChatHistory::create([
                'user_id' => $userId,
                'message' => $message,
                'reply' => $responses,
                'intent' => $nluData['intent'] ?? 'unknown',
                'entities' => $nluData['entities'] ?? [],
                'session_key' => session()->getId(),
                'ip' => $request ? $request->ip() : null,
                'user_agent' => $request ? $request->userAgent() : null,
            ]);

        } catch (\Exception $e) {
            \Log::error('Lỗi lưu lịch sử chat: ' . $e->getMessage());
        }
    }

    public function getChatHistory(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
        }
        try {
            $histories = ChatHistory::forUser(auth()->id())->recent(50)->get()->reverse()->values();
            return response()->json(['success' => true, 'data' => $histories]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi tải lịch sử.'], 500);
        }
    }

    public function clearChatHistory(Request $request): JsonResponse
    {
        if (!auth()->check())
            return response()->json(['success' => false], 401);
        ChatHistory::where('user_id', auth()->id())->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa lịch sử chat.']);
    }

    public function showChatHistory()
    {
        if (!auth()->check())
            return redirect()->route('login');
        $histories = ChatHistory::forUser(auth()->id())->recent(50)->get()->reverse()->values();
        return view('chat.history', compact('histories'));
    }

    // ================== CORE PROCESS MESSAGE ==================

    private function processMessage(string $message, Request $request = null): array
    {
        $responses = [];
        $nluData = $this->nlu->analyze($message);
        $intent = $nluData['intent'] ?? null;

        // Contexts
        $bookingFlow = session('booking_flow', null);
        $isFindingOtherFacilities = session('chatbot_finding_other_facilities', false);
        $isCheckingPrice = session('chatbot_checking_price', false);
        $isWaitingLocation = session('chatbot_waiting_location_check', false);

        // 1. Xử lý Booking Flow
        if ($bookingFlow) {
            return $this->handleBookingFlow($message, $nluData, $request);
        }

        // 2. Xử lý khi đang đợi nhập vị trí (cho chức năng Kiểm tra giờ trống)
        if ($isWaitingLocation) {
            $responses[] = $this->finishAvailabilityCheckWithLocation($message, $request);
            return $responses;
        }

        // 3. Xử lý Find Other Facilities context
        if ($intent === 'find_other_facilities') {
            if (!$nluData['entities']['time'] || !$nluData['entities']['date']) {
                $lastContext = session('chatbot_last_query_context');
                if ($lastContext && isset($lastContext['time']) && isset($lastContext['date'])) {
                    $nluData['entities']['time'] = $lastContext['time'];
                    $nluData['entities']['date'] = $lastContext['date'];
                } else {
                    if ($request)
                        session(['chatbot_finding_other_facilities' => true]);
                    $responses[] = '⏰ Bạn muốn tìm sân vào khung giờ nào?<br>VD: "18h hôm nay", "20h ngày mai"';
                    return $responses;
                }
            }
        }

        if ($isFindingOtherFacilities && $nluData['entities']['time'] && ($intent === 'check_availability' || $intent === 'unknown')) {
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
                $responses[] = $this->handleCheckPrice($nluData, $request); // Tách hàm cho gọn
                break;

            case 'view_booking':
                $responses[] = $this->buildBookingHistoryResponse();
                $this->clearAllSessions($request);
                break;

            case 'check_availability':
                // Gọi hàm check mới có hỏi vị trí
                $responses[] = $this->startAvailabilityCheckFlow($nluData, $request);
                break;

            case 'find_other_facilities':
                if ($request && !$nluData['entities']['time'])
                    session(['chatbot_finding_other_facilities' => true]);
                else if ($request)
                    session()->forget('chatbot_finding_other_facilities');

                $responses[] = $this->buildOtherFacilitiesResponse($nluData, $request);
                if ($request)
                    session()->forget('chatbot_checking_price');
                break;

            default:
                if ($isFindingOtherFacilities && $nluData['entities']['time']) {
                    $nluData['intent'] = 'find_other_facilities';
                    $responses[] = $this->buildOtherFacilitiesResponse($nluData, $request);
                    if ($request)
                        session()->forget('chatbot_finding_other_facilities');
                } else if ($isCheckingPrice) {
                    $responses[] = $this->handleCheckPriceContext($message, $request);
                } else {
                    $responses[] = '😅 Xin lỗi, tôi chưa hiểu ý bạn.<br>Hãy thử:<br>• "Kiểm tra sân trống hôm nay 18h"<br>• "Giá sân bao nhiêu"';
                }
                break;
        }

        return $responses;
    }

    // ==================== CHECK PRICE LOGIC ====================
    private function handleCheckPrice($nluData, $request)
    {
        $facilityName = $nluData['entities']['facility_name'] ?? null;
        if (!$facilityName) {
            session(['chatbot_checking_price' => true]);
            return 'Bạn muốn xem giá sân ở cơ sở nào? Vui lòng cho tôi biết tên cơ sở.<br>VD: Thủ Đức, Quận 1...';
        }
        return $this->processPriceInfo($facilityName);
    }

    private function handleCheckPriceContext($message, $request)
    {
        $facilityName = $this->extractFacilityNameFromMessage($message);
        if ($facilityName) {
            if ($request)
                session()->forget('chatbot_checking_price');
            return $this->processPriceInfo($facilityName);
        }
        return '❓ Tôi không nhận diện được tên cơ sở. Vui lòng nhập rõ hơn.';
    }

    private function processPriceInfo($facilityName)
    {
        $priceInfo = $this->booking->getPriceInfo($facilityName);
        if ($priceInfo === null) {
            session(['chatbot_checking_price' => true]);
            return '❌ Không tìm thấy cơ sở "<b>' . htmlspecialchars($facilityName) . '</b>".<br>Vui lòng thử tên khác.';
        }
        if (is_array($priceInfo)) {
            $msg = $priceInfo['message'] . $this->generateBookingButton($priceInfo['booking_data']);
            if (!empty($priceInfo['similar_facilities'])) {
                $msg .= "<br>💡 <b>Các cơ sở giá tương tự:</b><br>";
                foreach ($priceInfo['similar_facilities'] as $sim) {
                    $msg .= "📍 <b>{$sim['facility_name']}</b> - " . number_format($sim['default_price']) . "đ";
                    $msg .= $this->generateBookingButton(['facility_id' => $sim['facility_id'], 'facility_name' => $sim['facility_name']]) . "<br>";
                }
            }
            return $msg;
        }
        return $priceInfo;
    }

    // ==================== BOOKING FLOW (CORE) ====================

    private function startBookingFlow(array $nluData, Request $request = null): string
    {
        if (!auth()->id())
            return '🔒 Bạn cần đăng nhập để đặt sân.';

        $flow = ['step' => 'ask_flow_choice', 'data' => []];
        if ($request)
            session(['booking_flow' => $flow]);

        return "🎾 Bạn muốn đặt sân như thế nào?<br><br>1️⃣ Tôi biết cơ sở muốn đặt<br>2️⃣ Giúp tôi tìm cơ sở phù hợp<br><br>Vui lòng nhập 1 hoặc 2";
    }

    private function handleBookingFlow(string $message, array $nluData, Request $request = null): array
    {
        $flow = session('booking_flow');
        $step = $flow['step'] ?? null;

        switch ($step) {
            case 'ask_flow_choice':
                return $this->handleFlowChoice($message, $request);

            // --- LUỒNG 1: BIẾT CƠ SỞ ---
            case 'flow1_ask_facility':
                return $this->handleFlow1AskFacility($message, $nluData, $request);
            case 'flow1_select_time_date':
                return $this->handleFlow1SelectTimeDate($message, $nluData, $request);
            case 'flow1_ask_duration':
                return $this->handleFlow1AskDuration($message, $request);
            case 'flow1_select_court':
                return $this->handleFlow1SelectCourt($message, $request);
            case 'flow1_confirm_booking':
                return $this->handleFlow1ConfirmBooking($message, $request);

            // --- LUỒNG 2: TÌM CƠ SỞ ---
            case 'flow2_ask_time':
                return $this->handleFlow2AskTime($message, $nluData, $request);
            case 'flow2_ask_date':
                return $this->handleFlow2AskDate($message, $nluData, $request);

            // [MỚI] Bước hỏi thời lượng cho Flow 2
            case 'flow2_ask_duration':
                return $this->handleFlow2AskDuration($message, $request);

            // [MỚI] Bước hỏi vị trí cho Flow 2
            case 'flow2_ask_location':
                return $this->handleFlow2AskLocation($message, $request);

            case 'flow2_show_facilities':
                return $this->handleFlow2ShowFacilities($message, $request);
            case 'flow2_select_court':
                return $this->handleFlow2SelectCourt($message, $request);
            case 'flow2_confirm_booking':
                return $this->handleFlow2ConfirmBooking($message, $request);

            default:
                session()->forget('booking_flow');
                return ['❌ Lỗi Flow. Gõ "Đặt sân" để thử lại.'];
        }
    }

    private function handleFlowChoice(string $message, Request $request = null): array
    {
        $choice = trim($message);
        if ($choice === '1') {
            if ($request)
                session(['booking_flow' => ['step' => 'flow1_ask_facility', 'data' => ['flow_type' => 1]]]);
            return ['📍 Bạn muốn đặt sân tại cơ sở nào?<br>VD: Thủ Đức, Quận 1...'];
        } else if ($choice === '2') {
            if ($request)
                session(['booking_flow' => ['step' => 'flow2_ask_time', 'data' => ['flow_type' => 2]]]);
            return ['⏰ Bạn muốn đặt sân vào khung giờ nào?<br>VD: 18h, 20h...'];
        }
        return ['❓ Vui lòng chọn <b>1</b> hoặc <b>2</b>'];
    }

    // --- LOGIC FLOW 1 ---
    private function handleFlow1AskFacility(string $message, array $nluData, Request $request = null): array
    {
        $facilityName = $this->extractFacilityNameFromMessage($message);
        if (!$facilityName)
            return ['❓ Vui lòng nhập tên cơ sở rõ ràng hơn.'];

        $facility = $this->booking->getFacilityByName($facilityName);
        if (!$facility)
            return ['❌ Không tìm thấy cơ sở "<b>' . htmlspecialchars($facilityName) . '</b>".'];

        $flow = session('booking_flow');
        $flow['step'] = 'flow1_select_time_date';
        $flow['data']['facility_id'] = $facility['facility_id'];
        $flow['data']['facility_name'] = $facility['facility_name'];
        if ($request)
            session(['booking_flow' => $flow]);

        return ["✅ Đã chọn: <b>{$facility['facility_name']}</b><br>📅 Bạn muốn đặt ngày giờ nào?<br>VD: 18h hôm nay, 20h ngày mai..."];
    }

    private function handleFlow1SelectTimeDate(string $message, array $nluData, Request $request = null): array
    {
        $time = $nluData['entities']['time'] ?? null;
        $flow = session('booking_flow');
        $date = $nluData['entities']['date'] ?? ($flow['data']['date'] ?? date('Y-m-d'));

        if (!$time)
            return ['⏰ Bạn muốn bắt đầu lúc mấy giờ?'];

        $flow['step'] = 'flow1_ask_duration';
        $flow['data']['date'] = $date;
        $flow['data']['time'] = $time;
        if ($request)
            session(['booking_flow' => $flow]);

        return ["🕒 Bạn muốn đặt trong bao lâu?<br>VD: 1 tiếng, 1.5 giờ..."];
    }

    private function handleFlow1AskDuration(string $message, Request $request = null): array
    {
        $duration = $this->nlu->extractDuration($message);
        if (!$duration && is_numeric(trim($message)))
            $duration = (float) trim($message);

        if (!$duration || $duration < 0.5)
            return ['❓ Vui lòng nhập thời gian tối thiểu 0.5 tiếng.'];

        $flow = session('booking_flow');
        $flow['data']['duration'] = $duration;

        $availability = $this->booking->checkAvailabilityForDuration(
            $flow['data']['facility_id'],
            $flow['data']['date'],
            $flow['data']['time'],
            $duration
        );

        if (isset($availability['error']))
            return [$availability['error']];
        if (empty($availability['available']))
            return ["❌ Không có sân trống $duration tiếng từ {$flow['data']['time']}."];

        $flow['step'] = 'flow1_select_court';
        $flow['data']['available_courts'] = $availability['available'];
        if ($request)
            session(['booking_flow' => $flow]);

        $courtsList = implode(', ', array_map(fn($c) => "<b>$c</b>", $availability['available']));
        return ["✅ Sân trống cho <b>$duration giờ</b>:<br>$courtsList<br><br>🎾 Bạn chọn sân nào?"];
    }

    private function handleFlow1SelectCourt(string $message, Request $request = null): array
    {
        $flow = session('booking_flow');
        $availableCourts = $flow['data']['available_courts'] ?? [];

        if (preg_match('/(sân\s*)?(\d+)/iu', $message, $matches)) {
            $courtName = "Sân " . $matches[2];

            // Check logic
            $isValid = false;
            foreach ($availableCourts as $avCourt) {
                if (stripos($avCourt, $courtName) !== false) {
                    $courtName = $avCourt;
                    $isValid = true;
                    break;
                }
            }

            if (!$isValid)
                return ["❌ Sân $courtName không khả dụng. Vui lòng chọn: " . implode(', ', $availableCourts)];

            $flow['step'] = 'flow1_confirm_booking';
            $flow['data']['court_name'] = $courtName;
            if ($request)
                session(['booking_flow' => $flow]);

            $date = date('d/m/Y', strtotime($flow['data']['date']));
            $duration = $flow['data']['duration'] ?? 1;

            return [
                "📋 <b>XÁC NHẬN:</b><br>" .
                "📍 Cơ sở: <b>{$flow['data']['facility_name']}</b><br>🎾 Sân: <b>$courtName</b><br>" .
                "📅 Ngày: <b>$date</b><br>⏰ Giờ: <b>{$flow['data']['time']}</b><br>⏳ Thời lượng: <b>$duration tiếng</b><br><br>" .
                "Gõ <b>Xác nhận</b> để đặt."
            ];
        }
        return ['❓ Vui lòng chọn số sân. VD: Sân 1, Sân 2...'];
    }

    private function handleFlow1ConfirmBooking(string $message, Request $request = null): array
    {
        return $this->finalizeBooking($message, $request); // Gom chung logic confirm
    }

    // --- LOGIC FLOW 2 ---
    private function handleFlow2AskTime(string $message, array $nluData, Request $request = null): array
    {
        $time = $nluData['entities']['time'] ?? null;
        if (!$time)
            return ['⏰ Vui lòng cho biết giờ muốn đặt.<br>VD: 18h, 20h...'];

        $flow = session('booking_flow');
        $flow['step'] = 'flow2_ask_date';
        $flow['data']['time'] = $time;
        if ($request)
            session(['booking_flow' => $flow]);

        return ['📅 Bạn muốn đặt vào ngày nào?<br>VD: hôm nay, ngày mai...'];
    }

    private function handleFlow2AskDate(string $message, array $nluData, Request $request = null): array
    {
        $date = $nluData['entities']['date'] ?? date('Y-m-d');
        $flow = session('booking_flow');
        $flow['data']['date'] = $date;

        // [THAY ĐỔI] Hỏi thời lượng thay vì tìm ngay
        $flow['step'] = 'flow2_ask_duration';
        if ($request)
            session(['booking_flow' => $flow]);

        return ['⏳ Bạn muốn đặt sân trong bao lâu?<br>VD: 1 tiếng, 2 tiếng...'];
    }

    private function handleFlow2AskDuration(string $message, Request $request = null): array
    {
        $duration = $this->nlu->extractDuration($message);
        if (!$duration && is_numeric(trim($message)))
            $duration = (float) trim($message);
        if (!$duration || $duration < 0.5)
            return ['❓ Tối thiểu 0.5 tiếng. Nhập lại nhé.'];

        $flow = session('booking_flow');
        $flow['data']['duration'] = $duration;

        // [THAY ĐỔI] Hỏi vị trí thay vì tìm ngay
        $flow['step'] = 'flow2_ask_location';
        if ($request)
            session(['booking_flow' => $flow]);

        return ['📍 Bạn muốn tìm sân ở khu vực nào?<br>VD: Thủ Đức, Quận 9, hoặc gõ "Tất cả"'];
    }

    private function handleFlow2AskLocation(string $message, Request $request = null): array
    {
        $location = trim($message);
        $flow = session('booking_flow');
        $time = $flow['data']['time'];
        $date = $flow['data']['date'];

        // Tìm tất cả sân trống
        $result = $this->booking->checkAvailabilityAllFacilities($date, $time);
        if (isset($result['error']))
            return [$result['error']];

        $facilities = $result['results'] ?? [];
        if (empty($facilities)) {
            session()->forget('booking_flow');
            return ["❌ Không có cơ sở nào còn sân trống giờ này."];
        }

        // Lọc theo vị trí
        $filtered = $this->filterFacilitiesByLocation($facilities, $location);
        $note = "";
        if (empty($filtered) && !empty($facilities)) {
            $note = "⚠️ Không tìm thấy sân ở <b>$location</b>. Dưới đây là các sân khác:<br><br>";
            $filtered = $facilities;
        }

        $flow['step'] = 'flow2_show_facilities';
        $flow['data']['facilities'] = array_values($filtered);
        if ($request)
            session(['booking_flow' => $flow]);

        $msg = $note . "🔍 Tìm thấy <b>" . count($filtered) . " cơ sở</b> phù hợp:<br><br>";
        foreach ($filtered as $idx => $fac) {
            $msg .= ($idx + 1) . ". <b>{$fac['facility_name']}</b><br>";
            if ($fac['address'])
                $msg .= "   📌 {$fac['address']}<br>";
            $msg .= "   ✅ Còn: " . implode(', ', $fac['available_courts']) . "<br><br>";
        }
        $msg .= "📍 Bạn muốn đặt tại cơ sở nào? (Nhập tên hoặc số)";
        return [$msg];
    }

    private function handleFlow2ShowFacilities(string $message, Request $request = null): array
    {
        $flow = session('booking_flow');
        $facilities = $flow['data']['facilities'] ?? [];

        if (is_numeric(trim($message))) {
            $idx = (int) trim($message) - 1;
            $selected = $facilities[$idx] ?? null;
        } else {
            $name = $this->extractFacilityNameFromMessage($message);
            $selected = null;
            foreach ($facilities as $fac) {
                if (stripos($fac['facility_name'], $name) !== false) {
                    $selected = $fac;
                    break;
                }
            }
        }

        if (!$selected)
            return ['❌ Vui lòng chọn đúng cơ sở trong danh sách.'];

        $flow['step'] = 'flow2_select_court';
        $flow['data']['selected_facility'] = $selected;
        if ($request)
            session(['booking_flow' => $flow]);

        $courtsList = implode(', ', array_map(fn($c) => "<b>$c</b>", $selected['available_courts']));
        return ["✅ Đã chọn: <b>{$selected['facility_name']}</b><br>🎾 Các sân trống: $courtsList<br><br>Bạn chọn sân nào?"];
    }

    private function handleFlow2SelectCourt(string $message, Request $request = null): array
    {
        $flow = session('booking_flow');
        $avCourts = $flow['data']['selected_facility']['available_courts'];

        if (preg_match('/(sân\s*)?(\d+)/iu', $message, $matches)) {
            $courtName = "Sân " . $matches[2];
            if (!in_array($courtName, $avCourts))
                return ["❌ Sân $courtName không khả dụng. Chọn: " . implode(', ', $avCourts)];

            $flow['step'] = 'flow2_confirm_booking';
            $flow['data']['court_name'] = $courtName;
            if ($request)
                session(['booking_flow' => $flow]);

            $date = date('d/m/Y', strtotime($flow['data']['date']));
            $duration = $flow['data']['duration'] ?? 1;

            return [
                "📋 <b>XÁC NHẬN:</b><br>" .
                "📍 Cơ sở: <b>{$flow['data']['selected_facility']['facility_name']}</b><br>🎾 Sân: <b>$courtName</b><br>" .
                "📅 Ngày: <b>$date</b><br>⏰ Giờ: <b>{$flow['data']['time']}</b><br>⏳ Thời lượng: <b>$duration tiếng</b><br><br>" .
                "Gõ <b>Xác nhận</b> để đặt."
            ];
        }
        return ['❓ Vui lòng chọn số sân.'];
    }

    private function handleFlow2ConfirmBooking(string $message, Request $request = null): array
    {
        return $this->finalizeBooking($message, $request);
    }

    // --- HÀM CHUNG ĐỂ XỬ LÝ CONFIRM & TẠO BOOKING ---
    private function finalizeBooking(string $message, Request $request = null): array
    {
        $message = mb_strtolower(trim($message));
        if (str_contains($message, 'hủy')) {
            session()->forget('booking_flow');
            return ['❌ Đã hủy đặt sân.'];
        }

        if (str_contains($message, 'xác nhận') || str_contains($message, 'ok')) {
            $flow = session('booking_flow');
            $userId = auth()->id();

            // Xác định các biến tùy theo Flow 1 hay Flow 2
            $facilityId = $flow['data']['facility_id'] ?? $flow['data']['selected_facility']['facility_id'];
            $courtName = $flow['data']['court_name'];
            $date = $flow['data']['date'];
            $time = $flow['data']['time'];
            $duration = $flow['data']['duration'] ?? 1;

            // DÙNG HÀM MULTI SLOTS ĐỂ ĐẢM BẢO TẠO ĐỦ GIỜ VÀ TÍNH TIỀN ĐÚNG
            $result = $this->booking->createBookingMultiSlots($userId, $facilityId, $courtName, $date, $time, $duration);

            session()->forget('booking_flow');

            if ($result['success']) {
                $paymentUrl = route('chatbot.payment', ['booking_id' => $result['booking_id']]);
                $totalFormatted = number_format($result['total'], 0, ',', '.');

                return [
                    "✅ <b>Đặt sân thành công!</b><br>" .
                    "🎫 Mã: <b>{$result['booking_code']}</b><br>" .
                    "⏳ Thời gian: <b>{$duration} tiếng</b><br>" .
                    "💰 Tổng tiền: <b>{$totalFormatted}đ</b><br>" .
                    "👉 <b><a href='$paymentUrl' target='_blank' style='color: #007bff; text-decoration: none;'>THANH TOÁN NGAY</a></b>"
                ];
            } else {
                return ["❌ " . ($result['message'] ?? 'Lỗi không xác định.')];
            }
        }
        return ['❓ Gõ "Xác nhận" để đặt hoặc "Hủy".'];
    }


    // ==================== AVAILABILITY CHECK FLOW ====================

    private function startAvailabilityCheckFlow(array $nluData, Request $request = null): string
    {
        $time = $nluData['entities']['time'] ?? null;
        $date = $nluData['entities']['date'] ?? date('Y-m-d');
        $facName = $nluData['entities']['facility_name'] ?? null;

        if (!$time)
            return '⏰ Bạn muốn kiểm tra lúc mấy giờ?<br>VD: "18h hôm nay"';

        if ($facName) {
            // Có tên cơ sở -> check trực tiếp
            return $this->buildAvailabilityResponse($nluData, $request);
        }

        // Chưa có tên -> hỏi vị trí
        if ($request) {
            session([
                'chatbot_waiting_location_check' => true,
                'chatbot_check_context' => ['time' => $time, 'date' => $date]
            ]);
        }
        return "📍 Để tìm sân gần nhất, bạn đang ở khu vực nào?<br>VD: Thủ Đức, Quận 9... (hoặc gõ 'Tất cả')";
    }

    private function finishAvailabilityCheckWithLocation(string $locationMsg, Request $request = null): string
    {
        $ctx = session('chatbot_check_context');
        if ($request) {
            session()->forget('chatbot_waiting_location_check');
            session()->forget('chatbot_check_context');
        }

        $result = $this->booking->checkAvailabilityAllFacilities($ctx['date'], $ctx['time']);
        if (isset($result['error']))
            return $result['error'];

        $all = $result['results'] ?? [];
        if (empty($all))
            return "❌ Không có sân nào trống giờ này.";

        $filtered = $this->filterFacilitiesByLocation($all, $locationMsg);

        $msg = "🔍 Kết quả tại <b>\"$locationMsg\"</b>:<br><i>(Lúc {$ctx['time']} {$ctx['date']})</i><br><br>";
        if (empty($filtered)) {
            $msg .= "⚠️ Không có sân ở khu vực này. Đây là các sân khác:<br><br>";
            $filtered = $all;
        }

        foreach ($filtered as $fac) {
            $msg .= "🏟️ <b>{$fac['facility_name']}</b><br>";
            if ($fac['address'])
                $msg .= "📍 {$fac['address']}<br>";
            $msg .= "✅ Trống: " . implode(', ', $fac['available_courts']) . "<br>";
            if (!empty($fac['booking_data']))
                $msg .= $this->generateBookingButton($fac['booking_data']);
            $msg .= "<br><hr><br>";
        }
        return $msg;
    }

    // ==================== HELPER METHODS ====================

    private function buildAvailabilityResponse(array $nluData, Request $request = null): string
    {
        // Logic cũ cho check 1 cơ sở cụ thể
        $date = $nluData['entities']['date'] ?? date('Y-m-d');
        $time = $nluData['entities']['time'];

        $result = $this->booking->checkAvailability($date, $time);
        if (isset($result['error']))
            return $result['error'];

        $facName = $result['facility_name'] ?? 'Cơ sở này';
        $formattedDate = date('d/m/Y', strtotime($date));
        $formattedTime = date('H:i', strtotime($time));

        if (!empty($result['is_full'])) {
            $msg = "❌ <b>$facName</b> đã hết sân lúc $formattedTime ngày $formattedDate.";
            if ($result['slot_id']) {
                $suggestions = $this->booking->suggestAlternative($date, $result['slot_id']);
                if ($suggestions)
                    $msg .= "<br>💡 Gợi ý giờ khác: " . implode(', ', $suggestions);
            }
            return $msg . "<br><br>💬 Hỏi: 'Còn sân khác không?' để tìm cơ sở khác.";
        }

        $avail = $result['available'] ?? [];
        if (empty($avail))
            return "❌ Không có sân trống.<br>💬 Hỏi: 'Còn sân khác không?'";

        $msg = "✅ <b>$facName</b> còn trống: <b>" . implode(', ', $avail) . "</b><br>Lúc $formattedTime ngày $formattedDate";
        if (!empty($result['booking_data']))
            $msg .= $this->generateBookingButton($result['booking_data']);
        return $msg;
    }

    private function buildOtherFacilitiesResponse(array $nluData, Request $request = null): string
    {
        $date = $nluData['entities']['date'] ?? date('Y-m-d');
        $time = $nluData['entities']['time'];

        if ($request)
            session(['chatbot_last_query_context' => ['time' => $time, 'date' => $date]]);

        $result = $this->booking->checkAvailabilityAllFacilities($date, $time);
        if (isset($result['error']))
            return $result['error'];

        $facilities = $result['results'] ?? [];
        if (empty($facilities))
            return "❌ Không có cơ sở nào còn sân trống.";

        $msg = "🔍 <b>Các cơ sở còn sân trống lúc $time $date:</b><br><br>";
        foreach ($facilities as $fac) {
            $msg .= "📍 <b>{$fac['facility_name']}</b><br>";
            $msg .= "   ✅ Còn: <b>" . implode(', ', $fac['available_courts']) . "</b><br>";
            if (!empty($fac['booking_data']))
                $msg .= "   " . $this->generateBookingButton($fac['booking_data']);
            $msg .= "<br>";
        }
        return $msg;
    }

    private function generateBookingButton(array $bookingData): string
    {
        $facilityId = $bookingData['facility_id'] ?? '';
        $facilityName = $bookingData['facility_name'] ?? '';
        $date = $bookingData['date'] ?? '';
        $time = $bookingData['time'] ?? '';
        $slotId = $bookingData['slot_id'] ?? '';

        $csrfToken = csrf_token();
        $formId = 'booking-form-' . uniqid();

        // Nút đặt nhanh (ẩn các input)
        return <<<HTML
        <br>
        <form id="$formId" action="/venue" method="POST" style="display: inline;">
            <input type="hidden" name="_token" value="$csrfToken">
            <input type="hidden" name="facility_id" value="$facilityId">
            <input type="hidden" name="facility_name" value="$facilityName">
            <input type="hidden" name="date" value="$date">
            <input type="hidden" name="time" value="$time">
            <input type="hidden" name="slot_id" value="$slotId">
            <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
                👉 Đặt nhanh
            </button>
        </form>
HTML;
    }

    private function extractFacilityNameFromMessage(string $message): ?string
    {
        $message = preg_replace('/(giá|bao nhiêu|chi phí|xem|tôi muốn|cho tôi|muốn|hỏi|của|ở|tại|sân|cơ\s*sở)/iu', '', $message);
        $message = preg_replace('/\s+/', ' ', $message);
        $message = trim($message);
        return (strlen($message) >= 3 && preg_match('/[a-zA-ZÀ-ỹ]/u', $message)) ? $message : null;
    }

    private function filterFacilitiesByLocation(array $facilities, string $userLocation): array
    {
        $userLocation = mb_strtolower(trim($userLocation));
        if (in_array($userLocation, ['tất cả', 'không', 'khong', 'all']))
            return $facilities;

        $filtered = array_filter($facilities, function ($facility) use ($userLocation) {
            $address = mb_strtolower($facility['address'] ?? '');
            $name = mb_strtolower($facility['facility_name'] ?? '');
            return str_contains($address, $userLocation) || str_contains($name, $userLocation);
        });
        return empty($filtered) ? [] : array_values($filtered);
    }

    private function clearAllSessions(Request $request = null): void
    {
        if ($request) {
            session()->forget([
                'booking_flow',
                'chatbot_finding_other_facilities',
                'chatbot_checking_price',
                'chatbot_last_query_context',
                'chatbot_waiting_location_check',
                'chatbot_check_context'
            ]);
        }
    }

    // --- PAYMENT PAGE ---
    public function showPaymentPage($booking_id)
    {
        if (!auth()->check())
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập');

        $userId = auth()->id();
        $mainBooking = Bookings::where('booking_id', $booking_id)->where('user_id', $userId)->first();
        if (!$mainBooking)
            return redirect()->route('trang_chu')->with('error', 'Không tìm thấy đơn.');

        $relatedBookings = Bookings::where('invoice_detail_id', $mainBooking->invoice_detail_id)->get();
        $invoice_detail_id = $mainBooking->invoice_detail_id;
        $slots = [];
        $total = 0;
        foreach ($relatedBookings as $b) {
            $ts = \App\Models\Time_slots::where('time_slot_id', $b->time_slot_id)->first();
            $ct = \App\Models\Courts::where('court_id', $b->court_id)->where('facility_id', $b->facility_id)->first();

            $slots[] = [
                'court' => $ct ? $ct->court_name : 'Sân ?',
                'start_time' => $ts ? date('H:i', strtotime($ts->start_time)) : '--:--',
                'end_time' => $ts ? date('H:i', strtotime($ts->end_time)) : '--:--',
                'date' => date('d-m-Y', strtotime($b->booking_date)),
                'price' => $b->unit_price,
            ];
            $total += $b->unit_price;
        }

        $facilities = Facilities::find($mainBooking->facility_id);
        $customer = \App\Models\Users::find($userId);

        $startTime = $slots[0]['start_time'];
        $endTime = $slots[count($slots) - 1]['end_time'];
        $uniqueTimes = "$startTime đến $endTime";
        $result = (count($slots) * 0.5) . ' tiếng';

        // Compact vars for view
        $customer_name = $customer->fullname ?? '';
        $customer_phone = $customer->phone ?? '';
        $customer_email = $customer->email ?? '';
        $uniqueCourts = implode(', ', array_unique(array_column($slots, 'court')));
        $uniqueDates = implode(' / ', array_unique(array_column($slots, 'date')));

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
            'result',
            'invoice_detail_id'
        ));
    }

    private function buildBookingHistoryResponse(): string
    {
        if (!auth()->id())
            return '🔒 Đăng nhập để xem lịch sử.';
        $history = $this->booking->getMyBookings(auth()->id());
        if ($history->isEmpty())
            return '📅 Bạn chưa có lịch đặt nào.';

        $msg = "📅 <b>Lịch sử:</b><br>";
        foreach ($history as $h) {
            $msg .= "• " . date('d/m/Y', strtotime($h->booking_date)) . ": {$h->status}<br>";
        }
        return $msg;
    }
}