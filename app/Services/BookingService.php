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
    private function getTimeSlotId($timeString) {
        // DB lưu start_time là 18:00:00. Cần query đúng
        $slot = Time_slots::where('start_time', $timeString)->first();
        return $slot ? $slot->time_slot_id : null;
    }

    public function checkAvailability($date, $timeString)
    {
        $slotId = $this->getTimeSlotId($timeString);
        if (!$slotId) return ['error' => 'Khung giờ không hợp lệ (VD: 17h, 18h)'];

        // 1. Lấy tất cả sân của cơ sở
        $allCourts = Courts::where('facility_id', $this->facilityId)->pluck('court_name', 'court_id')->toArray();

        // 2. Lấy các sân đã đặt
        $bookedCourtIds = Bookings::where('facility_id', $this->facilityId)
            ->where('booking_date', $date)
            ->where('time_slot_id', $slotId)
            ->where('status', '!=', 'Đã Hủy') // Quan trọng
            ->pluck('court_id')
            ->toArray();

        // 3. Tính hiệu số
        $available = array_diff_key($allCourts, array_flip($bookedCourtIds));
        
        // 4. Lấy tên cơ sở
        $facility = Facilities::find($this->facilityId);
        $facilityName = $facility ? $facility->facility_name : 'Cơ sở #' . $this->facilityId;
        
        return [
            'available' => array_values($available), // Danh sách tên sân trống
            'is_full' => empty($available),
            'slot_id' => $slotId,
            'facility_name' => $facilityName,
            'facility_id' => $this->facilityId
        ];
    }

    // FEATURE 5: Gợi ý giờ trống
    public function suggestAlternative($date, $originalSlotId)
    {
        // Tìm slot trước và sau (+/- 1 ID)
        $suggestions = [];
        $checkSlots = [$originalSlotId - 1, $originalSlotId + 1];

        foreach ($checkSlots as $sid) {
            if ($sid < 1 || $sid > 38) continue; // Giới hạn DB

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
        if (!$slotId) return ['error' => 'Khung giờ không hợp lệ (VD: 17h, 18h)'];

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

    // Tạo booking mới - CẢI THIỆN để tương thích với trang thanh toán
    public function createBooking($userId, $facilityId, $courtName, $date, $timeString)
    {
        $slotId = $this->getTimeSlotId($timeString);
        
        // Tìm ID sân từ tên
        $court = Courts::where('facility_id', $facilityId)
            ->where('court_name', 'like', "%$courtName%")
            ->first();

        if (!$court || !$slotId) {
            return [
                'success' => false,
                'message' => 'Thông tin sân hoặc giờ không đúng.'
            ];
        }

        // Check lại lần cuối tránh trùng
        $exists = Bookings::where('court_id', $court->court_id)
            ->where('booking_date', $date)
            ->where('time_slot_id', $slotId)
            ->where('status', '!=', 'Đã Hủy')
            ->exists();

        if ($exists) {
            return [
                'success' => false,
                'message' => 'Rất tiếc, sân này vừa bị người khác đặt.'
            ];
        }

        // Lấy thông tin time slot
        $timeSlot = Time_slots::find($slotId);
        
        // Lấy giá từ court_prices (ưu tiên theo court_id, nếu không có thì lấy theo facility_id)
        $price = Court_prices::where('facility_id', $facilityId)
            ->where('court_id', $court->court_id)
            ->orderBy('effective_date', 'desc')
            ->first();

        if (!$price) {
            // Nếu không có giá riêng cho sân, lấy giá chung của facility
            $price = Court_prices::where('facility_id', $facilityId)
                ->whereNull('court_id')
                ->orderBy('effective_date', 'desc')
                ->first();
        }

        // Kiểm tra xem có phải giờ vàng không
        $isSpecialTime = $this->isSpecialTime($timeSlot, $date);
        $unitPrice = $price ? ($isSpecialTime ? $price->special_price : $price->default_price) : 50000;

        $bookingCode = 'BOT_' . time() . '_' . $userId;

        // Tạo booking tạm thời với status "Chờ thanh toán"
        $booking = Bookings::create([
            'user_id' => $userId,
            'facility_id' => $facilityId,
            'court_id' => $court->court_id,
            'time_slot_id' => $slotId,
            'booking_date' => $date,
            'invoice_detail_id' => null, // Sẽ được tạo khi thanh toán thành công
            'status' => 'Chờ thanh toán',
            'unit_price' => $unitPrice
        ]);

        // Chuẩn bị dữ liệu slots cho trang thanh toán
        $slots = [[
            'court' => $court->court_name,
            'start_time' => date('H:i', strtotime($timeSlot->start_time)),
            'end_time' => date('H:i', strtotime($timeSlot->end_time)),
            'date' => date('d/m/Y', strtotime($date)),
            'price' => $unitPrice,
            'court_id' => $court->court_id,
            'time_slot_id' => $slotId,
        ]];

        return [
            'success' => true,
            'booking_id' => $booking->booking_id,
            'booking_code' => $bookingCode,
            'facility_id' => $facilityId,
            'slots' => $slots,
            'total' => $unitPrice,
            'message' => 'Đặt sân thành công!'
        ];
    }

    // Kiểm tra xem có phải giờ vàng không
    private function isSpecialTime($timeSlot, $date)
    {
        if (!$timeSlot) return false;

        // Lấy giờ bắt đầu
        $hour = (int)date('H', strtotime($timeSlot->start_time));
        
        // Giờ vàng thường là 17h-21h (bạn có thể điều chỉnh)
        $isGoldenHour = ($hour >= 17 && $hour <= 21);
        
        // Kiểm tra xem có phải cuối tuần không
        $dayOfWeek = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday
        $isWeekend = ($dayOfWeek >= 6); // 6 = Saturday, 7 = Sunday
        
        return $isGoldenHour || $isWeekend;
    }

    // FEATURE 4: Tra cứu giá - ĐÃ CẢI THIỆN
    public function getPriceInfo($facilityName = null) {
        // Nếu không có tên cơ sở, trả về null để controller xử lý hỏi người dùng
        if (!$facilityName) {
            return null;
        }
        
        // Tìm cơ sở theo tên (tìm kiếm linh hoạt hơn)
        $facility = Facilities::where('facility_name', 'like', "%$facilityName%")
            ->where('status', 'đã duyệt')
            ->where('is_active', true)
            ->first();
        
        if (!$facility) {
            return null; // Trả về null để controller xử lý
        }
        
        $price = Court_prices::where('facility_id', $facility->facility_id)
            ->orderBy('effective_date', 'desc')
            ->first();
        
        // Lấy giá (ưu tiên từ court_prices, nếu không có thì lấy từ facilities)
        $defaultPrice = $price ? $price->default_price : ($facility->default_price ?? 0);
        $specialPrice = $price ? $price->special_price : ($facility->special_price ?? 0);
        
        if ($defaultPrice == 0 && $specialPrice == 0) {
            return "Chưa có thông tin giá cho cơ sở này.";
        }
        
        $msg = "💰 <b>Giá tại {$facility->facility_name}:</b><br>" .
               "Giá sân thường: " . number_format($defaultPrice, 0, ',', '.') . "đ<br>" .
               "Giá giờ vàng/Lễ: " . number_format($specialPrice, 0, ',', '.') . "đ";
        
        // Tìm các cơ sở có giá tương tự
        $similarFacilities = $this->findSimilarPriceFacilities($facility->facility_id, $defaultPrice);
        
        if (!empty($similarFacilities)) {
            $msg .= "<br><br>💡 <b>Các cơ sở có giá tương tự:</b><br>";
            foreach ($similarFacilities as $similar) {
                $msg .= "📍 <b>{$similar['facility_name']}</b> - ";
                $msg .= "Giá thường: " . number_format($similar['default_price'], 0, ',', '.') . "đ";
                if (!empty($similar['address'])) {
                    $msg .= " ({$similar['address']})";
                }
                $msg .= "<br>";
            }
        }
        
        return $msg;
    }

    // Tìm các cơ sở có giá tương tự - ĐÃ CẢI THIỆN
    private function findSimilarPriceFacilities($excludeFacilityId, $targetPrice, $limit = 3) {
        if ($targetPrice == 0) {
            return [];
        }

        // Tính khoảng giá (±25% hoặc tối thiểu ±30,000đ)
        $percentageRange = $targetPrice * 0.25;
        $minimumRange = 30000;
        $priceRange = max($percentageRange, $minimumRange);
        
        $minPrice = $targetPrice - $priceRange;
        $maxPrice = $targetPrice + $priceRange;

        // Lấy tất cả cơ sở đã duyệt và đang hoạt động
        $facilities = Facilities::where('status', 'đã duyệt')
            ->where('is_active', true)
            ->where('facility_id', '!=', $excludeFacilityId)
            ->get();

        $similarFacilities = [];

        foreach ($facilities as $facility) {
            // Lấy giá từ court_prices hoặc facilities
            $price = Court_prices::where('facility_id', $facility->facility_id)
                ->orderBy('effective_date', 'desc')
                ->first();
            
            $facilityPrice = $price ? $price->default_price : ($facility->default_price ?? 0);
            
            // Kiểm tra nếu giá trong khoảng tương tự
            if ($facilityPrice > 0 && $facilityPrice >= $minPrice && $facilityPrice <= $maxPrice) {
                $similarFacilities[] = [
                    'facility_id' => $facility->facility_id,
                    'facility_name' => $facility->facility_name,
                    'address' => $facility->address,
                    'default_price' => $facilityPrice,
                    'special_price' => $price ? $price->special_price : ($facility->special_price ?? 0),
                    'price_diff' => abs($facilityPrice - $targetPrice) // Dùng để sắp xếp
                ];
            }
        }

        // Sắp xếp theo độ chênh lệch giá (gần nhất trước)
        usort($similarFacilities, function($a, $b) {
            return $a['price_diff'] <=> $b['price_diff'];
        });

        // Giới hạn số lượng kết quả
        return array_slice($similarFacilities, 0, $limit);
    }

    // FEATURE 3: Lịch sử
    public function getMyBookings($userId) {
        return Bookings::where('user_id', $userId)
            ->orderBy('booking_date', 'desc')
            ->limit(3)
            ->get();
    }

    // FEATURE 6: Tìm kiếm sân trống ở tất cả các cơ sở
    public function checkAvailabilityAllFacilities($date, $timeString) {
        $slotId = $this->getTimeSlotId($timeString);
        if (!$slotId) return ['error' => 'Khung giờ không hợp lệ (VD: 17h, 18h)'];

        // Lấy tất cả cơ sở đã được duyệt và đang hoạt động
        $facilities = Facilities::where('status', 'đã duyệt')
            ->where('is_active', true)
            ->get();

        $results = [];

        foreach ($facilities as $facility) {
            // Lấy tất cả sân của cơ sở này
            $allCourts = Courts::where('facility_id', $facility->facility_id)
                ->pluck('court_name', 'court_id')
                ->toArray();

            // Lấy các sân đã đặt
            $bookedCourtIds = Bookings::where('facility_id', $facility->facility_id)
                ->where('booking_date', $date)
                ->where('time_slot_id', $slotId)
                ->where('status', '!=', 'Đã Hủy')
                ->pluck('court_id')
                ->toArray();

            // Tính sân trống
            $available = array_diff_key($allCourts, array_flip($bookedCourtIds));
            
            if (!empty($available)) {
                $results[] = [
                    'facility_id' => $facility->facility_id,
                    'facility_name' => $facility->facility_name,
                    'address' => $facility->address,
                    'available_courts' => array_values($available),
                    'count' => count($available)
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