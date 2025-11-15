<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Courts;
use App\Models\Time_slots;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use App\Models\Facilities;

class ManagerController extends Controller
{

    // Trang Tổng quan của Quản lý sân
    public function index()
    {
        // Tương lai: Lấy KPI, biểu đồ doanh thu... CỦA CƠ SỞ NÀY
        return view('manager.index');
    }


    // Trang Quản lý Hợp đồng dài hạn
    public function contracts()
    {
        // Tương lai: Lấy danh sách hợp đồng
        // $contracts = Contract::where('facility_id', ...)->get();
        // return view('manager.contracts', compact('contracts'));
        return view('manager.contracts');
    }


    // Trang Quản lý Sân bãi (trạng thái, lịch)

    public function courts(Request $request)
    {
        // --- LẤY FACILITY ID---
        $manager = Auth::user();
        if (!$manager || !$manager->facility_id) { // Kiểm tra manager có facility_id không
            // Hoặc chuyển hướng hoặc báo lỗi nếu không tìm thấy facility_id
            abort(403, 'Không tìm thấy thông tin cơ sở của quản lý.');
        }
        $idSan = $manager->facility_id; // Giả sử User có cột facility_id

        $courts = Courts::where('facility_id', $idSan)
            ->orderBy('court_id', 'asc')
            ->get();

        $validStatuses = ['Hoạt động', 'Bảo trì', 'Đóng cửa'];

        $thongtinsan = Facilities::where('facility_id', $idSan)->firstOrFail();
        $customer = Auth::check() ? Users::where('user_id', Auth::id())->first() : null;
        $timeSlots = Time_slots::all();
        // Nếu người dùng chọn ngày, dùng giá trị đó
        $dateStart = $request->query('start', now()->format('Y-m-d'));
        $dateEnd = $request->query('end', now()->addDays(31)->format('Y-m-d'));

        // Sinh mảng ngày từ date_start → date_end
        $dates = [];
        $current = \Carbon\Carbon::parse($dateStart);
        $end = \Carbon\Carbon::parse($dateEnd);

        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Lấy danh sách đặt sân
        $bookings = Bookings::where('facility_id', $idSan)
            ->whereBetween('booking_date', [$dateStart, $dateEnd])
            ->get(['booking_date', 'time_slot_id', 'court_id']);

        $bookingsData = [];
        foreach ($bookings as $b) {
            $bookingsData[$b->booking_date][$b->time_slot_id][$b->court_id] = true;
        }

        $thuTiengViet = [
            'Mon' => 'Thứ hai',
            'Tue' => 'Thứ ba',
            'Wed' => 'Thứ tư',
            'Thu' => 'Thứ năm',
            'Fri' => 'Thứ sáu',
            'Sat' => 'Thứ bảy',
            'Sun' => 'Chủ nhật',
        ];

        $soLuongSan = $thongtinsan->quantity_court;
        $dsSanCon = [];
        for ($i = 1; $i <= $soLuongSan; $i++) {
            $dsSanCon[] = [
                'id' => $thongtinsan->facility_id . '-' . $i,
                'ten' => 'Sân ' . $i
            ];
        }

        return view('manager.courts', compact(
            'courts',
            'validStatuses',
            'thongtinsan',
            'customer',
            'timeSlots',
            'dates',
            'bookingsData',
            'thuTiengViet',
            'soLuongSan',
            'dsSanCon',
            'dateStart',
            'dateEnd',
            'dateStart',
            'dateEnd'
        ));
    }

    /**
     * Xử lý cập nhật trạng thái sân con
     */
    public function updateCourtStatus(Request $request, $court_id)
    {
        $manager = Auth::user();

        // Tìm sân
        $court = Courts::where('court_id', $court_id)
            ->where('facility_id', $manager->facility_id)
            ->firstOrFail();

        // Kiểm tra quyền
        $permissions = is_array($manager->permissions) ? $manager->permissions : [];
        $hasPermission = in_array('manage_courts', $permissions);

        if (!$hasPermission) {
            abort(403, 'Bạn không có quyền cập nhật sân này.');
        }

        // Validate
        $validStatuses = ['Hoạt động', 'Bảo trì', 'Đóng cửa'];

        $validatedData = $request->validate([
            'status' => ['required', Rule::in($validStatuses)],
        ]);

        // Cập nhật
        $court->status = $validatedData['status'];
        $court->save();

        return redirect()->route('manager.courts')
            ->with('success', "Đã cập nhật trạng thái cho '{$court->court_name}' thành công!");
    }

    /**
     * Cung cấp dữ liệu Bookings cho FullCalendar (JSON)
     */
    public function getBookingsData(Request $request)
    {
        // --- LẤY FACILITY ID ---
        $manager = Auth::user();
        if (!$manager || !$manager->facility_id) {
            return response()->json(['error' => 'Không tìm thấy cơ sở của quản lý.'], 403);
        }
        $facilityId = $manager->facility_id;
        Log::info("getBookingsData: Querying for Facility ID: " . $facilityId); // Log facility ID

        // --- LẤY START/END TỪ FULLCALENDAR ---
        try {
            $start = Carbon::parse($request->input('start'))->startOfDay();
            $end = Carbon::parse($request->input('end'))->startOfDay();
            Log::info("FullCalendar requested range: Start={$start->toDateString()}, End={$end->toDateString()}");
        } catch (\Exception $e) {
            Log::error("Lỗi parse start/end date từ FullCalendar: " . $e->getMessage());
            return response()->json(['error' => 'Định dạng ngày không hợp lệ.'], 400);
        }

        // --- XÂY DỰNG CÂU TRUY VẤN BOOKINGS ---
        // Tạo đối tượng Query Builder
        $query = Bookings::query() // Sử dụng model Bookings
            ->where('bookings.facility_id', $facilityId)
            ->whereBetween('bookings.booking_date', [$start->toDateString(), $end->copy()->subDay()->toDateString()])
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.time_slot_id')
            ->leftJoin('courts', 'bookings.court_id', '=', 'courts.court_id')
            ->leftJoin('users', 'bookings.user_id', '=', 'users.user_id')
            ->select(
                'bookings.booking_id',
                'bookings.booking_date',
                'time_slots.start_time',
                'time_slots.end_time',
                'courts.court_name',
                'users.fullname as user_fullname',
                'bookings.status'
            ); // <-- Bỏ Log ở đây

        // --- Log câu SQL TRƯỚC KHI thực thi ---
        Log::info('SQL Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // --- THỰC THI TRUY VẤN ---
        $bookings = $query->get(); // <-- THÊM ->get() ĐỂ LẤY KẾT QUẢ

        // --- Log dữ liệu thô SAU KHI lấy ---
        Log::info('Raw Bookings Data Fetched:', $bookings->toArray());

        // --- ĐỊNH DẠNG EVENT OBJECTS (Giữ nguyên logic map) ---
        $events = $bookings->map(function ($booking) {
            // Log từng booking đang xử lý (Giữ nguyên)
            Log::info('Processing Booking:', $booking->toArray());

            $startTimeStr = $booking->booking_date . ' ' . $booking->start_time;
            $endTimeStr = $booking->booking_date . ' ' . $booking->end_time;

            try {
                $start = Carbon::parse($startTimeStr)->toIso8601String();
                $end = Carbon::parse($endTimeStr)->toIso8601String();
                Log::info("Parsed Booking ID {$booking->booking_id}: Start={$start}, End={$end}");
            } catch (\Exception $e) {
                Log::error("Lỗi parse ngày giờ booking ID {$booking->booking_id} (...): " . $e->getMessage());
                return null;
            }

            return [
                'id' => $booking->booking_id,
                'title' => ($booking->user_fullname ?? 'Khách') . ' - ' . ($booking->court_name ?? 'N/A'),
                'start' => $start,
                'end' => $end,
                'color' => ($booking->status === 'đã xác nhận' || $booking->status === 'đã thanh toán') ? '#28a745' : ($booking->status === 'chờ thanh toán' ? '#ffc107' : '#6c757d'),
                'extendedProps' => ['status' => $booking->status]
            ];
        })->filter()->values();

        // Ghi log dữ liệu JSON cuối cùng (Giữ nguyên)
        Log::info('Final Events JSON:', $events->toArray());

        return response()->json($events);
    }

    /**
     * Xử lý cập nhật khi kéo-thả trên Calendar
     */
    public function updateBookingTime(Request $request, Bookings $booking)
    {
        // --- 1. KIỂM TRA QUYỀN ---
        $manager = Auth::users();
        if (!$manager || !$manager->facility_id || $booking->facility_id !== $manager->facility_id) {
            // Trả về lỗi JSON thay vì abort() vì đây là AJAX request
            return response()->json(['message' => 'Bạn không có quyền cập nhật lịch đặt này.'], 403);
        }

        // --- 2. VALIDATE DỮ LIỆU ---
        $validated = $request->validate([
            'start' => 'required|date', // Ngày giờ bắt đầu mới (ISO 8601)
            'end' => 'required|date|after:start', // Ngày giờ kết thúc mới
        ]);

        // --- 3. XỬ LÝ THỜI GIAN MỚI ---
        $newStart = Carbon::parse($validated['start']);
        $newEnd = Carbon::parse($validated['end']);
        $newDate = $newStart->toDateString(); // Ngày mới
        $newStartTime = $newStart->format('H:i:s'); // Giờ bắt đầu mới (HH:mm:ss)
        $newEndTime = $newEnd->format('H:i:s');     // Giờ kết thúc mới (HH:mm:ss)

        // --- 4. TÌM TIME_SLOT_ID MỚI ---
        // Tìm khung giờ trong bảng time_slots khớp với giờ bắt đầu và kết thúc mới
        $newTimeSlot = Time_slots::where('start_time', $newStartTime)
            ->where('end_time', $newEndTime)
            ->first();

        if (!$newTimeSlot) {
            // Nếu không tìm thấy khung giờ phù hợp (ví dụ: kéo thả vào giờ lẻ)
            return response()->json(['message' => 'Khung giờ mới không hợp lệ hoặc không tồn tại.'], 422); // 422 Unprocessable Entity
        }
        $newTimeSlotId = $newTimeSlot->time_slot_id;

        // --- 5. KIỂM TRA XUNG ĐỘT LỊCH (QUAN TRỌNG) ---
        // Kiểm tra xem có booking nào khác ĐÃ TỒN TẠI trên cùng sân (court_id),
        // cùng ngày (booking_date) VÀ cùng khung giờ mới (time_slot_id) không?
        // Loại trừ chính booking đang sửa ($booking->booking_id).
        $conflictExists = Bookings::where('court_id', $booking->court_id) // Cùng sân
            ->where('booking_date', $newDate)       // Cùng ngày mới
            ->where('time_slot_id', $newTimeSlotId) // Cùng khung giờ mới
            ->where('booking_id', '!=', $booking->booking_id) // Loại trừ chính nó
            // ->whereIn('status', ['đã xác nhận', 'chờ thanh toán']) // Chỉ kiểm tra các trạng thái hợp lệ (tùy chọn)
            ->exists(); // Chỉ cần biết có tồn tại hay không

        if ($conflictExists) {
            // Nếu có xung đột
            return response()->json(['message' => 'Lịch đặt bị trùng! Khung giờ này trên sân đã có người khác đặt.'], 409); // 409 Conflict
        }

        // --- 6. CẬP NHẬT CSDL ---
        try {
            $booking->update([
                'booking_date' => $newDate,
                'time_slot_id' => $newTimeSlotId,
                // Cập nhật lại unit_price nếu cần (ví dụ: giá giờ mới khác giờ cũ)
                // 'unit_price' => $newPrice, 
            ]);
        } catch (\Exception $e) {
            Log::error("Lỗi cập nhật booking ID {$booking->booking_id}: " . $e->getMessage());
            return response()->json(['message' => 'Đã xảy ra lỗi khi cập nhật lịch đặt.'], 500); // 500 Internal Server Error
        }

        // --- 7. PHẢN HỒI THÀNH CÔNG ---
        return response()->json(['message' => 'Lịch đặt đã được cập nhật thành công.']);
    }
}