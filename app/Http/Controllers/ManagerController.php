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
        $manager = Auth::user();
        if (!$manager || !$manager->facility_id) {
            abort(403, 'Tài khoản quản lý chưa được gán cơ sở.');
        }
        // Truyền ID sang view để JS dùng
        $facilityId = $manager->facility_id;
        return view('manager.index', compact('facilityId'));
    }

    // 2. API: LẤY DANH SÁCH SÂN (Cho Dropdown)
    public function getCourts(Request $request)
    {
        $manager = Auth::user();
        // Dùng model Courts đơn giản để lấy danh sách
        $courts = Courts::where('facility_id', $manager->facility_id)
            ->orderBy('court_id', 'asc')
            ->get(['court_id', 'court_name']);

        return response()->json(['success' => true, 'courts' => $courts]);
    }

    // 3. API: LẤY KPI (Lượt đặt, Doanh thu, Sân trống...)
    public function getKpiData(Request $request)
    {
        Log::info('getKpiData called', [
            'range' => $request->range,
            'court' => $request->court,
            'facility_id' => Auth::user()->facility_id
        ]);
        $manager = Auth::user();
        $facilityId = $manager->facility_id;
        $range = $this->getDateRange($request);
        $courtId = $request->court;

        // --- QUERY KPI: DOANH THU ---
        $revenueQuery = DB::table('invoices')
            ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
            ->where('invoice_details.facility_id', $facilityId)
            ->whereBetween('invoices.issue_date', [$range['start'], $range['end']])
            ->where('invoices.payment_status', 'like', '%thanh toán%');
        
        // --- QUERY KPI: BOOKING ĐẶT LẺ (từ invoices) ---
        $bookingIndividualQuery = DB::table('bookings')
        ->join('invoice_details', 'bookings.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
        ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.invoice_id')
        ->distinct('bookings.invoice_detail_id')
        ->where('bookings.facility_id', $facilityId)
        ->whereBetween('bookings.booking_date', [$range['start'], $range['end']]);

        // --- QUERY KPI: BOOKING HỢP ĐỒNG (từ long_term_contracts) ---
        $bookingContractQuery = DB::table('bookings')
        ->join('long_term_contracts', 'bookings.invoice_detail_id', '=', 'long_term_contracts.invoice_detail_id')
        ->distinct('bookings.invoice_detail_id')
        ->where('bookings.facility_id', $facilityId)
        ->whereBetween('bookings.booking_date', [$range['start'], $range['end']]);

        // Áp dụng lọc theo sân con
        if ($courtId && $courtId !== 'all') {
            $bookingIndividualQuery->where('bookings.court_id', $courtId);
            $bookingContractQuery->where('bookings.court_id', $courtId);

            $revenueQuery = DB::table('bookings')
                ->where('facility_id', $facilityId)
                ->where('court_id', $courtId)
                ->whereBetween('booking_date', [$range['start'], $range['end']])
                ->where('status', 'like', '%thanh toán%');
        }

        // Tính toán
        $bookingsIndividualCount = $bookingIndividualQuery->clone()->count();
        $bookingsContractCount = $bookingContractQuery->clone()->count();
        $bookingsCount = $bookingsIndividualCount + $bookingsContractCount; // Tổng cả 2 loại
        $cancelCount = DB::table('invoices')
        ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
        ->where('invoice_details.facility_id', $facilityId)
        ->whereBetween('invoices.issue_date', [$range['start'], $range['end']])
        ->where('invoices.payment_status', 'like', 'Đã Hủy')->count();

        if ($courtId && $courtId !== 'all') {
            $revenue = $revenueQuery->sum('unit_price');
        } else {
            $revenue = DB::table(function ($query) use ($facilityId, $range) {
                $query->select('invoice_details.invoice_detail_id', 'invoices.final_amount')
                    ->distinct()
                    ->from('invoices')
                    ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
                    ->where('invoice_details.facility_id', $facilityId)
                    ->whereBetween('invoices.issue_date', [$range['start'], $range['end']])
                    ->where('invoices.payment_status', 'like', '%thanh toán%');
            }, 'distinct_invoices')
            ->sum('final_amount');
        }

        // KPI Realtime (Sân bận)
        $busy = DB::table('courts')
            ->where('facility_id', $facilityId)
            ->where('status', 'Đang sử dụng')
            ->count();

        $totalC = ($courtId && $courtId !== 'all')
            ? 1
            : Courts::where('facility_id', $facilityId)->count();

        // Trả về giá trị trực tiếp thay vì object
        return response()->json([
            'bookings' => (int) $bookingsCount,
            'bookings_individual' => (int) $bookingsIndividualCount, // Đặt lẻ
            'bookings_contract' => (int) $bookingsContractCount, // Hợp đồng
            'revenue' => (float) $revenue,
            'cancel' => (int) $cancelCount,
            'utilization' => "$busy / $totalC",
            'success' => true
        ]);
    }

    // 4. API: BIỂU ĐỒ GIỜ
    public function getBookingsByHour(Request $request)
    {
        $manager = Auth::user();
        $range = $this->getDateRange($request);
        $courtId = $request->court;

        $query = DB::table('bookings')
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.time_slot_id')
            ->where('bookings.facility_id', $manager->facility_id)
            ->whereBetween('bookings.booking_date', [$range['start'], $range['end']])
            ->where('bookings.status', '!=', 'Đã Hủy');

        if ($courtId && $courtId !== 'all') {
            $query->where('bookings.court_id', $courtId);
        }

        // Group by giờ
        $data = $query->select(DB::raw('HOUR(time_slots.start_time) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        // Fill 24h
        $labels = [];
        $counts = [];
        for ($i = 5; $i <= 23; $i++) {
            $labels[] = "$i:00";
            $counts[] = $data[$i] ?? 0;
        }

        return response()->json(['labels' => $labels, 'counts' => $counts]);
    }

    // 5. API: BIỂU ĐỒ SÂN (Doanh thu)
    public function getRevenueByCourt(Request $request)
    {
        $manager = Auth::user();
        $range = $this->getDateRange($request);

        $query = DB::table('bookings')
            ->join('courts', 'bookings.court_id', '=', 'courts.court_id')
            ->where('bookings.facility_id', $manager->facility_id)
            ->where('courts.facility_id', $manager->facility_id)
            ->whereBetween('bookings.booking_date', [$range['start'], $range['end']])
            ->where('bookings.status', 'like', '%thanh toán%')
            ->select('courts.court_name', DB::raw('SUM(bookings.unit_price) as total'))
            ->groupBy('courts.court_name')
            ->get();

        
        return response()->json([
            'labels' => $query->pluck('court_name'),
            'revenues' => $query->pluck('total')->map(fn($val) => (float) $val) // Cast sang float
        ]);

    }

    // Helper ngày tháng
    private function getDateRange($request)
    {
        $type = $request->range ?? 'month';
        $start = Carbon::today();
        $end = Carbon::today();

        switch ($type) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;
            case 'week':
                $start = Carbon::now()->startOfWeek()->startOfDay();
                $end = Carbon::now()->endOfWeek()->endOfDay();
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth()->startOfDay();
                $end = Carbon::now()->endOfMonth()->endOfDay();
                break;
            case 'quarter':
                $start = Carbon::now()->firstOfQuarter()->startOfDay();
                $end = Carbon::now()->lastOfQuarter()->endOfDay();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear()->startOfDay();
                $end = Carbon::now()->endOfYear()->endOfDay();
                break;
            case 'custom':
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                break;
            default:
                $start = Carbon::now()->startOfMonth()->startOfDay();
                $end = Carbon::now()->endOfMonth()->endOfDay();
        }

        return ['start' => $start, 'end' => $end];
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

        $validStatuses = ['Trống', 'Đang sử dụng', 'Đang chờ'];

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

    public function contract_manager(Request $request)
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

        return view('manager.contracts', compact(
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
        $validStatuses = ['Trống', 'Đang sử dụng', 'Đang chờ'];

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
            // Log từng booking đang xử lý 
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

        // Ghi log dữ liệu JSON cuối cùng 
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

    public function promotions(Request $request)
    {
        $manager = Auth::user();
        $facilityId = $manager->facility_id;

        $promotions = DB::table('promotions')->where('facility_id', $facilityId)
            ->orderBy('promotion_id', 'desc')
            ->get();

        return view('manager.promotions', compact('promotions'));
    }

    public function promotions_create(Request $request)
    {
        $request->validate([
        'description'   => 'required|string|max:500',
        'discount_type' => 'required|string|max:50',
        'value'         => 'required|numeric',
        'start_date'    => 'required|date',
        'end_date'      => 'required|date|after_or_equal:start_date',
        'status'        => 'required|in:0,1',
        ], [
            'description.required' => 'Vui lòng nhập mô tả chương trình.',
            'value.numeric'        => 'Giá trị phải là số (0.1 = 10%, 20000 = 20k).',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        // Lấy facility_id của chủ sân
        $manager = Auth::user();
        $facilityId = $manager->facility_id;
        if (!$facilityId) {
            return back()->withErrors(['facility' => 'Không tìm thấy cơ sở của bạn.']);
        }

        DB::table('promotions')->insert([
            'facility_id'   => $facilityId,
            'description'   => $request->description,
            'discount_type' => $request->discount_type,
            'value'         => $request->value,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'status'        => $request->status,
        ]);

        return redirect()->route('manager.promotions')
                        ->with('success', 'Đã thêm chương trình khuyến mãi thành công!');
    }

    public function promotions_update(Request $request, $id)
    {
        $request->validate([
            'description'   => 'required|string|max:500',
            'discount_type' => 'required|string|max:50',
            'value'         => 'required|numeric',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'status'        => 'required|in:0,1',
        ]);
        $manager = Auth::user();
        $facilityId = $manager->facility_id;
        if (!$facilityId) {
            return back()->withErrors(['facility' => 'Không tìm thấy cơ sở của bạn.']);
        }

        DB::table('promotions')
        ->where('promotion_id', $id)
        ->where('facility_id',$facilityId)
        ->update([
            'description'   => $request->description,
            'discount_type' => $request->discount_type,
            'value'         => $request->value,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'status'        => $request->status,
        ]);

        return redirect()->route('manager.promotions')
                        ->with('success', 'Cập nhật chương trình khuyến mãi thành công!');
    }

    public function promotions_delete($id)
    {
        $manager = Auth::user();
        $facilityId = $manager->facility_id;

        if (!$facilityId) {
            return back()->withErrors(['facility' => 'Không tìm thấy cơ sở của bạn.']);
        }
        
        DB::table('promotions')->where('promotion_id', $id)
        ->where('facility_id',$facilityId)->delete();

        return redirect()->route('manager.promotions')
                        ->with('success', 'Đã xoá khuyến mãi thành công!');
    }

}