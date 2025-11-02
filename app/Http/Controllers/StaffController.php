<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Users;
use App\Models\Bookings;
use App\Models\Court;
use App\Models\TimeSlot;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    /**
     * Lấy facility_id của nhân viên, nếu không có thì báo lỗi
     */
    private function getStaffFacilityId()
    {
        $staff = Auth::user();
        if (!$staff || !$staff->facility_id) {
            // Báo lỗi 403 nếu nhân viên không được gán cơ sở
            abort(403, 'Tài khoản nhân viên không được gán cơ sở.');
        }
        return $staff->facility_id;
    }

    /**
     * 1. Trang Lịch Đặt Sân & Check-in (Func 1, 2)
     */
    public function index()
    {
        $facilityId = $this->getStaffFacilityId();
        $today = Carbon::today();

        $bookingsToday = Bookings::where('bookings.facility_id', $facilityId)
            ->where('bookings.booking_date', $today)
            ->with(['user', 'court'])
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.time_slot_id')
            ->select(
                'bookings.*', // Lấy tất cả cột từ bảng bookings
                'time_slots.start_time',
                'time_slots.end_time',
                // 'users.fullname', 
                // 'courts.court_name'
            )
            ->orderBy('time_slots.start_time', 'asc') // Sắp xếp
            ->get();
                // dd($bookingsToday->pluck('status'));
        return view('staff.index', compact('bookingsToday'));
    }

    /**
     * 2. Xử lý "Xác nhận khách đến sân" (Func 2)
     */
    public function confirmArrival(Bookings $booking) // Dùng Route Model Binding
    {
        // Kiểm tra xem nhân viên có quyền xác nhận booking này không (đúng cơ sở)
        if ($booking->facility_id !== $this->getStaffFacilityId()) {
            abort(403, 'Không có quyền.');
        }

        // Cập nhật trạng thái
        $booking->update(['status' => 'Đã sử dụng']);

        return redirect()->route('staff.index')->with('success', 'Đã xác nhận khách đến sân!');
    }

    /**
     * 3. Hiển thị trang Thanh Toán (Func 3, 4)
     * Hiển thị form, hoặc hiển thị kết quả tìm kiếm (nếu có)
     */
    public function paymentPage(Request $request)
    {
        // Lấy booking được flash từ session (nếu có)
        $booking = $request->session()->get('found_booking');

        return view('staff.payment', [
            'booking' => $booking,
            // (Thêm biến $invoice nếu tìm thấy hóa đơn)
        ]);
    }

    /**
     * 3. Tìm kiếm Booking để thanh toán (Func 3)
     */
    public function searchBooking(Request $request)
    {
        $facilityId = $this->getStaffFacilityId();
        $searchTerm = $request->input('search_term');

        // Tìm booking tại cơ sở này, CHƯA THANH TOÁN
        // Dựa trên Mã đặt (booking_id) HOẶC SĐT của user
        $booking = Bookings::query()
            ->where('bookings.facility_id', $facilityId)
            ->whereIn('bookings.status', ['Chưa thanh toán', null]) // Chỉ tìm booking chưa trả tiền
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.time_slot_id')
            ->leftJoin('courts', 'bookings.court_id', '=', 'courts.court_id')
            ->leftJoin('users', 'bookings.user_id', '=', 'users.user_id')
            ->where(function ($query) use ($searchTerm) {
                $query->where('bookings.booking_id', $searchTerm) // Tìm theo Mã đặt
                    ->orWhere('users.phone', $searchTerm); // Hoặc tìm theo SĐT
            })
            ->select(
                'bookings.*',
                DB::raw("CONCAT(time_slots.start_time, ' - ', time_slots.end_time) as time_range"), // Ghép giờ
                'users.fullname as user_fullname',
                'courts.court_name'
            )
            ->first(); // Lấy 1 kết quả đầu tiên

        if (!$booking) {
            return redirect()->route('staff.payment')->with('error', 'Không tìm thấy lượt đặt nào chưa thanh toán với thông tin này.');
        }

        // Lưu booking tìm thấy vào session và chuyển hướng về trang payment
        return redirect()->route('staff.payment')->with('found_booking', $booking);
    }

    /**
     * 3. Xử lý "Xác nhận Thanh toán" (Func 3)
     */
    public function processPayment(Request $request, Bookings $booking)
    {
        // Kiểm tra quyền
        if ($booking->facility_id !== $this->getStaffFacilityId()) {
            abort(403, 'Không có quyền.');
        }

        // Validate
        $validated = $request->validate([
            'payment_method' => 'required|string', // vd: 'Tiền mặt (Tại quầy)'
        ]);

        // Bắt đầu Transaction (Đảm bảo tất cả cùng thành công hoặc thất bại)
        DB::beginTransaction();
        try {
            // 1. Tạo Hóa đơn mới (Invoices)
            $invoice = Invoice::create([
                'customer_id' => $booking->user_id,
                'issue_date' => Carbon::today(),
                'total_amount' => $booking->unit_price,
                // 'promotion_id' => null, // Xử lý nếu có khuyến mãi
                'final_amount' => $booking->unit_price,
                'payment_status' => 'Đã thanh toán',
                'payment_method' => $validated['payment_method'],
            ]);

            // 2. Tạo Chi tiết Hóa đơn (InvoiceDetail)
            $detail = InvoiceDetail::create([
                'invoice_id' => $invoice->invoice_id,
                'booking_id' => $booking->booking_id,
                'sub_total' => $booking->unit_price,
                'quantity' => 1,
            ]);

            // 3. Cập nhật Booking (liên kết invoice_detail và đổi status)
            $booking->update([
                'invoice_detail_id' => $detail->invoice_detail_id,
                'status' => 'Đã thanh toán' // Cập nhật trạng thái
            ]);

            // 4. Commit Transaction
            DB::commit();

            // Chuyển hướng về trang payment với thông báo thành công
            // Truyền luôn invoice_id để có thể In
            return redirect()->route('staff.payment')
                ->with('success', 'Thanh toán thành công! Mã hóa đơn: ' . $invoice->invoice_id)
                ->with('last_invoice_id', $invoice->invoice_id);

        } catch (\Exception $e) {
            // 5. Rollback nếu có lỗi
            DB::rollBack();
            Log::error("Lỗi khi thanh toán: " . $e->getMessage());
            return redirect()->route('staff.payment')->with('error', 'Đã xảy ra lỗi khi xử lý thanh toán.');
        }
    }

    /**
     * 4. In Hóa đơn (Func 4)
     */
    public function printInvoice(Invoice $invoice)
    {
        // --- KIỂM TRA QUYỀN (Rất quan trọng) ---
        // Lấy booking đầu tiên liên quan đến hóa đơn này
        $booking = $invoice->invoiceDetails()->first()->booking ?? null; // Cần định nghĩa relationship

        if (!$booking || $booking->facility_id !== $this->getStaffFacilityId()) {
            abort(403, 'Không có quyền xem hóa đơn này.');
        }

        // Bạn cần tạo 1 view riêng cho việc in
        // View này chỉ có HTML của hóa đơn, không có layout
        return view('staff.invoice_print', compact('invoice'));
    }
}