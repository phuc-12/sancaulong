<?php

namespace App\Services;

use App\Models\Bookings;
use App\Models\Court_prices;
use App\Models\Courts;
use App\Models\Time_slots;
use App\Models\Facilities;

use Carbon\Carbon;

class BookingService
{
    // Giả sử mặc định lấy Facility đầu tiên hoặc ID cố định cho demo
    protected $facilityId = 1;

    // Lấy TimeSlotID từ giờ (VD: "18:00:00")
    private function getTimeSlotId($timeString)
    {
        // DB lưu start_time là 18:00:00. Cần query đúng
        $slot = Time_slots::where('start_time', $timeString)->first();
        return $slot ? $slot->time_slot_id : null;
    }

    public function checkAvailability($date, $timeString)
    {
        $slotId = $this->getTimeSlotId($timeString);
        if (!$slotId)
            return ['error' => 'Khung giờ không hợp lệ (VD: 17h, 18h)'];

        $allCourts = Courts::where('facility_id', $this->facilityId)->pluck('court_name', 'court_id')->toArray();

        $bookedCourtIds = Bookings::where('facility_id', $this->facilityId)
            ->where('booking_date', $date)
            ->where('time_slot_id', $slotId)
            ->where('status', '!=', 'Đã Hủy')
            ->pluck('court_id')
            ->toArray();

        $available = array_diff_key($allCourts, array_flip($bookedCourtIds));

        $facility = Facilities::find($this->facilityId);
        $facilityName = $facility ? $facility->facility_name : 'Cơ sở #' . $this->facilityId;

        // TẠO DATA ĐỂ POST (bao gồm cả thông tin user)
        $bookingData = [
            'facility_id' => $this->facilityId,
            'facility_name' => $facilityName,
            'date' => $date,
            'time' => $timeString,
            'slot_id' => $slotId,
        ];

        return [
            'available' => array_values($available),
            'is_full' => empty($available),
            'slot_id' => $slotId,
            'facility_name' => $facilityName,
            'facility_id' => $this->facilityId,
            'booking_data' => $bookingData // DATA ĐỂ POST
        ];
    }

    // FEATURE 5: Gợi ý giờ trống
    public function suggestAlternative($date, $originalSlotId)
    {
        // Tìm slot trước và sau (+/- 1 ID)
        $suggestions = [];
        $checkSlots = [$originalSlotId - 1, $originalSlotId + 1];

        foreach ($checkSlots as $sid) {
            if ($sid < 1 || $sid > 38)
                continue; // Giới hạn DB

            // Check xem slot này có sân trống không
            $hasBooking = Bookings::where('booking_date', $date)
                ->where('time_slot_id', $sid)
                ->where('status', '!=', 'Đã Hủy')
                ->count();

            $totalCourts = Courts::where('facility_id', $this->facilityId)->count();

            if ($hasBooking < $totalCourts) {
                $slotInfo = Time_slots::find($sid);
                if ($slotInfo) {
                    $suggestions[] = date('H:i', strtotime($slotInfo->start_time));
                }
            }
        }
        return $suggestions;
    }

    // Lấy thông tin cơ sở theo tên
    public function getFacilityByName($facilityName)
    {
        $facility = Facilities::where('facility_name', 'like', "%$facilityName%")
            ->where('status', 'đã duyệt')
            ->where('is_active', true)
            ->first();

        if (!$facility) {
            return null;
        }

        return [
            'facility_id' => $facility->facility_id,
            'facility_name' => $facility->facility_name,
            'address' => $facility->address,
        ];
    }

    // Kiểm tra sân trống theo facility_id cụ thể
    public function checkAvailabilityByFacility($facilityId, $date, $timeString)
    {
        $slotId = $this->getTimeSlotId($timeString);
        if (!$slotId)
            return ['error' => 'Khung giờ không hợp lệ (VD: 17h, 18h)'];

        $allCourts = Courts::where('facility_id', $facilityId)
            ->pluck('court_name', 'court_id')
            ->toArray();

        $bookedCourtIds = Bookings::where('facility_id', $facilityId)
            ->where('booking_date', $date)
            ->where('time_slot_id', $slotId)
            ->where('status', '!=', 'Đã Hủy')
            ->pluck('court_id')
            ->toArray();

        $available = array_diff_key($allCourts, array_flip($bookedCourtIds));

        return [
            'available' => array_values($available),
            'is_full' => empty($available),
            'slot_id' => $slotId,
        ];
    }

    // Tạo booking mới 
    public function createBooking($userId, $facilityId, $courtName, $date, $timeString)
    {
        // 1. Chuẩn bị dữ liệu
        $slotId = $this->getTimeSlotId($timeString);

        // Tìm ID sân từ tên
        $court = \App\Models\Courts::where('facility_id', $facilityId)
            ->where('court_name', 'like', "%$courtName%")
            ->first();

        if (!$court || !$slotId) {
            return ['success' => false, 'message' => 'Thông tin sân hoặc giờ không đúng.'];
        }

        // Lấy thông tin time slot
        $timeSlot = \App\Models\Time_slots::find($slotId);

        // Lấy giá tiền (Logic lấy giá chung cơ sở)
        $price = \App\Models\Court_prices::where('facility_id', $facilityId)
            ->orderBy('effective_date', 'desc')
            ->first();

        // Kiểm tra giờ vàng và tính giá cuối cùng
        $isSpecialTime = $this->isSpecialTime($timeSlot, $date);
        $unitPrice = $price ? ($isSpecialTime ? $price->special_price : $price->default_price) : 50000;

        // Tạo mã hóa đơn
        $bookingCode = 'BOT_' . time() . '_' . $userId;

        // 2. BẮT ĐẦU TRANSACTION
        // Giúp đảm bảo cả Hóa đơn và Booking cùng tạo thành công, nếu 1 cái lỗi thì hủy cả 2
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // A. Check trùng (Có sử dụng lockForUpdate để tránh xung đột khi nhiều người bấm cùng lúc)
            $exists = \App\Models\Bookings::where('court_id', $court->court_id)
                ->where('booking_date', $date)
                ->where('time_slot_id', $slotId)
                ->where('status', '!=', 'Đã Hủy')
                ->lockForUpdate() // Khóa dòng dữ liệu
                ->exists();

            if ($exists) {
                \Illuminate\Support\Facades\DB::rollBack(); // Hủy transaction
                return ['success' => false, 'message' => 'Rất tiếc, sân này vừa bị người khác đặt.'];
            }

            // B. Tạo Hóa Đơn (Bảng Cha) Trước
            \App\Models\InvoiceDetail::create([
                'invoice_detail_id' => $bookingCode,
                'invoice_id'        => 0,
                'facility_id'       => $facilityId,
                'sub_total'         => $unitPrice,
            ]);

            // C. Tạo Booking (Bảng Con)
            $booking = \App\Models\Bookings::create([
                'user_id' => $userId,
                'facility_id' => $facilityId,
                'court_id' => $court->court_id,
                'time_slot_id' => $slotId,
                'booking_date' => $date,
                'invoice_detail_id' => $bookingCode, // ID tham chiếu hợp lệ
                'status' => 'Chờ thanh toán',
                'unit_price' => $unitPrice
            ]);

            // Mọi thứ thành công -> Lưu vào DB
            \Illuminate\Support\Facades\DB::commit();

            // Chuẩn bị dữ liệu trả về cho View
            $slots = [
                [
                    'court' => $court->court_name,
                    'start_time' => date('H:i', strtotime($timeSlot->start_time)),
                    'end_time' => date('H:i', strtotime($timeSlot->end_time)),
                    'date' => date('d/m/Y', strtotime($date)),
                    'price' => $unitPrice,
                    'court_id' => $court->court_id,
                    'time_slot_id' => $slotId,
                ]
            ];

            return [
                'success' => true,
                'booking_id' => $booking->booking_id,
                'booking_code' => $bookingCode,
                'facility_id' => $facilityId,
                'slots' => $slots,
                'total' => $unitPrice,
                'message' => 'Đặt sân thành công!'
            ];

        } catch (\Exception $e) {
            // Có lỗi -> Hủy toàn bộ thao tác DB nãy giờ
            \Illuminate\Support\Facades\DB::rollBack();

            \Illuminate\Support\Facades\Log::error("Single Booking Error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    // 1. Hàm kiểm tra sân trống cho khoảng thời gian (Duration)
    public function checkAvailabilityForDuration($facilityId, $date, $startTime, $duration)
    {
        $startSlotId = $this->getTimeSlotId($startTime);
        if (!$startSlotId)
            return ['error' => 'Giờ bắt đầu không hợp lệ.'];

        // Tính số lượng slot cần thiết (1 tiếng = 2 slots)
        $slotsNeeded = ceil($duration * 2);

        // Lấy tất cả sân
        $allCourts = Courts::where('facility_id', $facilityId)->pluck('court_name', 'court_id')->toArray();
        $availableCourts = $allCourts;

        // Duyệt qua từng sân, kiểm tra xem sân đó có trống HẾT các slot liên tiếp không
        foreach ($allCourts as $courtId => $courtName) {
            for ($i = 0; $i < $slotsNeeded; $i++) {
                $currentSlotId = $startSlotId + $i;

                // Kiểm tra nếu slot vượt quá giới hạn (VD: quá 24h)
                if ($currentSlotId > 38) {
                    unset($availableCourts[$courtId]);
                    break;
                }

                // Kiểm tra xem slot này của sân này đã bị đặt chưa
                $isBooked = Bookings::where('facility_id', $facilityId)
                    ->where('court_id', $courtId)
                    ->where('booking_date', $date)
                    ->where('time_slot_id', $currentSlotId)
                    ->where('status', '!=', 'Đã Hủy')
                    ->exists();

                if ($isBooked) {
                    unset($availableCourts[$courtId]); // Loại bỏ sân này nếu vướng 1 slot bất kỳ
                    break; // Dừng kiểm tra sân này, chuyển sang sân sau
                }
            }
        }

        return [
            'available' => array_values($availableCourts),
            'slot_id' => $startSlotId
        ];
    }

    // 2. Hàm tạo Booking nhiều Slot
    public function createBookingMultiSlots($userId, $facilityId, $courtName, $date, $startTime, $duration)
    {
        $startSlotId = $this->getTimeSlotId($startTime);
        $slotsNeeded = ceil($duration * 2);

        $court = Courts::where('facility_id', $facilityId)
            ->where('court_name', 'like', "%$courtName%")
            ->first();

        if (!$court)
            return ['success' => false, 'message' => 'Không tìm thấy sân.'];

        // Lấy giá tiền
        $priceObj = Court_prices::where('facility_id', $facilityId)->first();
        $basePrice = $priceObj ? $priceObj->default_price : 50000;

        $bookingCode = 'BOT_' . time() . '_' . $userId;
        $totalAmount = $basePrice * $slotsNeeded; // Tính tổng tiền trước
        $firstBookingId = null;

        // BẮT ĐẦU TRANSACTION
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // BƯỚC 1: TẠO INVOICE DETAIL TRƯỚC
            // 'invoice_id' để tạm là 0 hoặc 1 vì chưa thanh toán xong
            \Illuminate\Support\Facades\DB::table('invoice_details')->insert([
                'invoice_detail_id' => $bookingCode,
                'invoice_id' => 0, // Giá trị tạm (Pending)
                'sub_total' => $totalAmount,
                'facility_id' => $facilityId
            ]);

            // BƯỚC 2: TẠO CÁC SLOT BOOKING
            for ($i = 0; $i < $slotsNeeded; $i++) {
                $currentSlotId = $startSlotId + $i;

                // Check trùng
                $isLocked = Bookings::where('court_id', $court->court_id)
                    ->where('booking_date', $date)
                    ->where('time_slot_id', $currentSlotId)
                    ->where('status', '!=', 'Đã Hủy')
                    ->exists();

                if ($isLocked) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    return ['success' => false, 'message' => "Sân bị kẹt ở khung giờ thứ " . ($i + 1)];
                }

                // Tạo booking
                $booking = Bookings::create([
                    'user_id' => $userId,
                    'facility_id' => $facilityId,
                    'court_id' => $court->court_id,
                    'time_slot_id' => $currentSlotId,
                    'booking_date' => $date,
                    'invoice_detail_id' => $bookingCode, // Mã này giờ đã tồn tại ở bảng cha
                    'status' => 'Chờ thanh toán',
                    'unit_price' => $basePrice
                ]);

                if ($i === 0)
                    $firstBookingId = $booking->booking_id;
            }

            \Illuminate\Support\Facades\DB::commit();

            return [
                'success' => true,
                'booking_id' => $firstBookingId,
                'booking_code' => $bookingCode,
                'total' => $totalAmount,
                'slot_count' => $slotsNeeded,
                'facility_id' => $facilityId,
                'slots' => []
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            // Ném lỗi ra để Controller bắt và hiện lên chat
            throw $e;
        }
    }

    // Kiểm tra xem có phải giờ vàng không
    private function isSpecialTime($timeSlot, $date)
    {
        if (!$timeSlot)
            return false;

        // Lấy giờ bắt đầu
        $hour = (int) date('H', strtotime($timeSlot->start_time));

        // Giờ vàng thường là 17h-21h
        $isGoldenHour = ($hour >= 17 && $hour <= 21);

        // Kiểm tra xem có phải cuối tuần không
        $dayOfWeek = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday
        $isWeekend = ($dayOfWeek >= 6); // 6 = Saturday, 7 = Sunday

        return $isGoldenHour || $isWeekend;
    }

    // FEATURE 4: Tra cứu giá
    public function getPriceInfo($facilityName = null)
    {
        if (!$facilityName) {
            return null;
        }

        $facility = Facilities::where('facility_name', 'like', "%$facilityName%")
            ->where('status', 'đã duyệt')
            ->where('is_active', true)
            ->first();

        if (!$facility) {
            return null;
        }

        $price = Court_prices::where('facility_id', $facility->facility_id)
            ->orderBy('effective_date', 'desc')
            ->first();

        $defaultPrice = $price ? $price->default_price : ($facility->default_price ?? 0);
        $specialPrice = $price ? $price->special_price : ($facility->special_price ?? 0);

        if ($defaultPrice == 0 && $specialPrice == 0) {
            return "Chưa có thông tin giá cho cơ sở này.";
        }

        // DATA ĐỂ POST
        $bookingData = [
            'facility_id' => $facility->facility_id,
            'facility_name' => $facility->facility_name,
        ];

        $msg = "💰 <b>Giá tại {$facility->facility_name}:</b><br>" .
            "Giá sân thường: " . number_format($defaultPrice, 0, ',', '.') . "đ<br>" .
            "Giá giờ vàng/Lễ: " . number_format($specialPrice, 0, ',', '.') . "đ<br><br>";

        // Trả về array để controller xử lý
        return [
            'message' => $msg,
            'booking_data' => $bookingData,
            'similar_facilities' => $this->findSimilarPriceFacilities($facility->facility_id, $defaultPrice)
        ];
    }

    // Tìm các cơ sở có giá tương tự
    private function findSimilarPriceFacilities($excludeFacilityId, $targetPrice, $limit = 3)
    {
        if ($targetPrice == 0) {
            return [];
        }

        $percentageRange = $targetPrice * 0.25;
        $minimumRange = 30000;
        $priceRange = max($percentageRange, $minimumRange);

        $minPrice = $targetPrice - $priceRange;
        $maxPrice = $targetPrice + $priceRange;

        $facilities = Facilities::where('status', 'đã duyệt')
            ->where('is_active', true)
            ->where('facility_id', '!=', $excludeFacilityId)
            ->get();

        $similarFacilities = [];

        foreach ($facilities as $facility) {
            $price = Court_prices::where('facility_id', $facility->facility_id)
                ->orderBy('effective_date', 'desc')
                ->first();

            $facilityPrice = $price ? $price->default_price : ($facility->default_price ?? 0);

            if ($facilityPrice > 0 && $facilityPrice >= $minPrice && $facilityPrice <= $maxPrice) {
                $similarFacilities[] = [
                    'facility_id' => $facility->facility_id,
                    'facility_name' => $facility->facility_name,
                    'address' => $facility->address,
                    'default_price' => $facilityPrice,
                    'special_price' => $price ? $price->special_price : ($facility->special_price ?? 0),
                    'price_diff' => abs($facilityPrice - $targetPrice)
                ];
            }
        }

        usort($similarFacilities, function ($a, $b) {
            return $a['price_diff'] <=> $b['price_diff'];
        });

        return array_slice($similarFacilities, 0, $limit);
    }

    // FEATURE 3: Lịch sử
    public function getMyBookings($userId)
    {
        return Bookings::where('user_id', $userId)
            ->orderBy('booking_date', 'desc')
            ->limit(3)
            ->get();
    }

    // FEATURE 6: Tìm kiếm sân trống ở tất cả các cơ sở
    public function checkAvailabilityAllFacilities($date, $timeString)
    {
        $slotId = $this->getTimeSlotId($timeString);
        if (!$slotId)
            return ['error' => 'Khung giờ không hợp lệ (VD: 17h, 18h)'];

        $facilities = Facilities::where('status', 'đã duyệt')
            ->where('is_active', true)
            ->get();

        $results = [];

        foreach ($facilities as $facility) {
            $allCourts = Courts::where('facility_id', $facility->facility_id)
                ->pluck('court_name', 'court_id')
                ->toArray();

            $bookedCourtIds = Bookings::where('facility_id', $facility->facility_id)
                ->where('booking_date', $date)
                ->where('time_slot_id', $slotId)
                ->where('status', '!=', 'Đã Hủy')
                ->pluck('court_id')
                ->toArray();

            $available = array_diff_key($allCourts, array_flip($bookedCourtIds));

            if (!empty($available)) {
                $results[] = [
                    'facility_id' => $facility->facility_id,
                    'facility_name' => $facility->facility_name,
                    'address' => $facility->address,
                    'available_courts' => array_values($available),
                    'count' => count($available),
                    'booking_data' => [
                        'facility_id' => $facility->facility_id,
                        'facility_name' => $facility->facility_name,
                        'date' => $date,
                        'time' => $timeString,
                        'slot_id' => $slotId,
                    ]
                ];
            }
        }

        return [
            'results' => $results,
            'total_facilities' => count($results),
            'slot_id' => $slotId
        ];
    }
}