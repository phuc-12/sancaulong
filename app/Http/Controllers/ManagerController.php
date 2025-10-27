<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\TimeSlot;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Auth;
use Illuminate\Validation\Rule;

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
    
    public function courts()
    {
        // --- LẤY FACILITY ID (SỬA LẠI CHO ĐÚNG) ---
        // Cách này an toàn hơn là gán cứng số 1
        $manager = Auth::user(); 
        if (!$manager || !$manager->facility_id) { // Kiểm tra manager có facility_id không
             // Hoặc chuyển hướng hoặc báo lỗi nếu không tìm thấy facility_id
             abort(403, 'Không tìm thấy thông tin cơ sở của quản lý.'); 
        }
        $facilityId = $manager->facility_id; // Giả sử User có cột facility_id

        $courts = Court::where('facility_id', $facilityId)
                       ->orderBy('court_name', 'asc') 
                       ->get();
        
        $validStatuses = ['Hoạt động', 'Bảo trì', 'Đóng cửa'];

        return view('manager.courts', compact('courts', 'validStatuses'));
    }

    /**
     * Xử lý cập nhật trạng thái sân con
     */
    public function updateCourtStatus(Request $request, Court $court)
    {
        // --- KIỂM TRA QUYỀN (NÊN BẬT LÊN) ---
        $manager = Auth::user();
         if (!$manager || !$manager->facility_id || $court->facility_id !== $manager->facility_id) {
             abort(403, 'Bạn không có quyền cập nhật sân này.'); 
         }

        // --- VALIDATE STATUS ---
        $validStatuses = ['Hoạt động', 'Bảo trì', 'Đóng cửa'];
        $validated = $request->validate([
            'status' => ['required', Rule::in($validStatuses)],
        ]);

        // --- CẬP NHẬT CSDL ---
        $court->update(['status' => $validated['status']]);

        // --- PHẢN HỒI ---
        return redirect()->route('manager.courts')
                         ->with('success', "Đã cập nhật trạng thái cho '{$court->court_name}' thành công!");
    }

    /**
     * Cung cấp dữ liệu Bookings cho FullCalendar (JSON)
     */
    public function getBookingsData(Request $request)
    {
        // --- LẤY FACILITY ID (SỬA LẠI CHO ĐÚNG) ---
         $manager = Auth::user(); 
         if (!$manager || !$manager->facility_id) { 
             return response()->json(['error' => 'Không tìm thấy cơ sở của quản lý.'], 403); 
         }
         $facilityId = $manager->facility_id;

        // --- LẤY START/END TỪ FULLCALENDAR ---
        // Carbon::parse để đảm bảo chuyển đổi múi giờ đúng (nếu cần)
        $start = Carbon::parse($request->input('start'))->toDateTimeString();
        $end = Carbon::parse($request->input('end'))->toDateTimeString();

        // --- TRUY VẤN BOOKINGS ---
        // SỬA LẠI LOGIC WHERE CHO ĐÚNG VỚI START/END DATETIME
        $bookings = Bookings::where('facility_id', $facilityId)
                            // Giả định bạn có start_datetime và end_datetime hoặc tính toán được
                            // Query này lấy các sự kiện GIAO NHAU với khoảng thời gian hiển thị
                           ->where(function ($query) use ($start, $end) {
                                // Nếu LƯU start_time, end_time riêng (kiểu TIME) và booking_date (kiểu DATE)
                                // Cách này phức tạp hơn, cần CONCAT hoặc tính toán
                                // Tạm thời dùng booking_date (sẽ không chính xác hoàn toàn cho timeGrid)
                                $query->whereBetween('booking_date', [
                                    Carbon::parse($start)->toDateString(), 
                                    Carbon::parse($end)->subDay()->toDateString() // Trừ 1 ngày vì whereBetween bao gồm cả ngày kết thúc
                                ]);
                                // LƯU Ý: Để chính xác với timeGrid, bạn nên có cột start_datetime và end_datetime
                                // hoặc join với time_slots và so sánh datetime đầy đủ.
                           })
                           ->with(['user', 'court']) // Eager load
                           ->get();

        // --- ĐỊNH DẠNG EVENT OBJECTS ---
        $events = $bookings->map(function ($booking) {
            // KIỂM TRA CÁCH LẤY START/END TIME CỦA BẠN
            // Cách 1: Nếu lưu start_time, end_time (kiểu TIME) trong bookings
             $startTimeStr = $booking->booking_date . ' ' . $booking->start_time; // vd: '2025-10-28 18:00:00'
             $endTimeStr = $booking->booking_date . ' ' . $booking->end_time;   // vd: '2025-10-28 19:00:00'

            // Cách 2: Nếu dùng time_slot_id và bảng time_slots
            // $timeSlot = TimeSlot::find($booking->time_slot_id);
            // $startTimeStr = $booking->booking_date . ' ' . $timeSlot->start_time;
            // $endTimeStr = $booking->booking_date . ' ' . $timeSlot->end_time;

            // Chuyển đổi an toàn sang Carbon
             try {
                 $start = Carbon::parse($startTimeStr)->toIso8601String();
                 $end = Carbon::parse($endTimeStr)->toIso8601String();
             } catch (\Exception $e) {
                 // Xử lý nếu ngày giờ không hợp lệ, trả về null hoặc báo lỗi
                 \Log::error("Lỗi parse ngày giờ booking ID {$booking->booking_id}: " . $e->getMessage());
                 return null; // Bỏ qua sự kiện lỗi này
             }
            
            return [
                'id'    => $booking->booking_id,
                'title' => ($booking->user->fullname ?? 'Khách vãng lai') . ' - ' . ($booking->court->court_name ?? 'N/A'), 
                'start' => $start, 
                'end'   => $end,
                // Thêm màu sắc dựa trên trạng thái (ví dụ)
                'color' => $booking->status === 'đã xác nhận' ? '#28a745' : ($booking->status === 'chờ thanh toán' ? '#ffc107' : '#6c757d'),
            ];
        })->filter(); // filter() để loại bỏ các giá trị null (do lỗi parse)

        return response()->json($events);
    }

    /**
     * Xử lý cập nhật khi kéo-thả trên Calendar
     */
    public function updateBookingTime(Request $request, Bookings $booking) // Đảm bảo đã use App\Models\Booking (hoặc Bookings)
    {
        // --- KIỂM TRA QUYỀN (NÊN BẬT LÊN) ---
         $manager = Auth::user();
         if (!$manager || !$manager->facility_id || $booking->facility_id !== $manager->facility_id) {
             abort(403, 'Bạn không có quyền cập nhật lịch đặt này.'); 
         }

        // --- VALIDATE DỮ LIỆU ---
        $validated = $request->validate([
            'start' => 'required|date', 
            'end'   => 'required|date|after:start', 
        ]);

        // --- CẬP NHẬT CSDL ---
        // SỬA LẠI LOGIC CẬP NHẬT CHO ĐÚNG VỚI CSDL CỦA BẠN
        $newStart = Carbon::parse($validated['start']);
        $newEnd = Carbon::parse($validated['end']);
        
        // Cách 1: Nếu lưu start_time, end_time trực tiếp
         $booking->update([
             'booking_date' => $newStart->toDateString(),
             'start_time'   => $newStart->format('H:i:s'), // Hoặc H:i nếu CSDL là TIME(0)
             'end_time'     => $newEnd->format('H:i:s'),   // Hoặc H:i
         ]);

        // Cách 2: Nếu dùng time_slot_id (Phức tạp hơn)
        // $newStartTime = $newStart->format('H:i:s');
        // $newEndTime = $newEnd->format('H:i:s');
        // // Tìm time_slot_id mới dựa trên giờ bắt đầu/kết thúc
        // $newTimeSlot = TimeSlot::where('start_time', $newStartTime)->where('end_time', $newEndTime)->first();
        // if ($newTimeSlot) {
        //     $booking->update([
        //         'booking_date' => $newStart->toDateString(),
        //         'time_slot_id' => $newTimeSlot->time_slot_id,
        //     ]);
        // } else {
        //     // Xử lý lỗi nếu khung giờ mới không tồn tại
        //     return response()->json(['message' => 'Khung giờ mới không hợp lệ.'], 422); 
        // }

        // --- PHẢN HỒI ---
        return response()->json(['message' => 'Lịch đặt đã được cập nhật.']);
    }
}