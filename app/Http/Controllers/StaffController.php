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
        $facilityId = $this->getStaffFacilityId();

        $invoices = DB::table('invoices')
        ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
        ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
        ->join('users', 'users.user_id', '=', 'invoices.customer_id')
        ->where('facilities.facility_id',$facilityId)
        ->select(
            'invoices.*',
            'facilities.facility_name as facility_name',
            'users.fullname as fullname',
            'invoices.issue_date as issue_date',
            'invoices.final_amount as final_amount',
            'invoice_details.invoice_detail_id as invoice_detail_id',
            'invoice_details.facility_id as facility_id'
        )
        ->orderBy('invoices.invoice_id', 'desc')
        ->get();

        $mybooking_details = [];

        foreach ($invoices as $invoice) {
            $details = DB::table('bookings')
                ->join('invoice_details', 'invoice_details.invoice_detail_id', '=', 'bookings.invoice_detail_id')
                ->join('time_slots', 'time_slots.time_slot_id', '=', 'bookings.time_slot_id')
                ->where('invoice_details.invoice_detail_id', $invoice->invoice_detail_id)
                ->select(
                    'bookings.*',
                    'time_slots.start_time',
                    'time_slots.end_time'
                )->get();

            $mybooking_details[$invoice->invoice_detail_id] = $details;
        }

        $long_term_contracts = DB::table('long_term_contracts')
        ->join('invoice_details', 'long_term_contracts.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
        ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
        ->join('users', 'users.user_id', '=', 'long_term_contracts.customer_id')
        ->where('facilities.facility_id',$facilityId)
        ->select(
            'long_term_contracts.*',
            'facilities.facility_name as facility_name',
            'users.fullname as fullname',
            'long_term_contracts.issue_date as issue_date',
            'long_term_contracts.final_amount as final_amount'
        )
        ->orderBy('long_term_contracts.contract_id', 'desc')
        ->get();

        $mycontract_details = [];

        foreach ($long_term_contracts as $ct) {
            $details = DB::table('bookings')
                ->join('long_term_contracts', 'long_term_contracts.invoice_detail_id', '=', 'bookings.invoice_detail_id')
                ->join('time_slots', 'time_slots.time_slot_id', '=', 'bookings.time_slot_id')
                ->where('long_term_contracts.invoice_detail_id', $ct->invoice_detail_id)
                ->select(
                    'bookings.*',
                    'time_slots.start_time',
                    'time_slots.end_time'
                )
                ->get();

            $mycontract_details[$ct->invoice_detail_id] = $details;
        }

        return view('staff.payment', [
            'invoices' => $invoices,
            'mybooking_details' => $mybooking_details,
            'long_term_contracts' => $long_term_contracts,
            'mycontract_details' => $mycontract_details,
            // (Thêm biến $invoice nếu tìm thấy hóa đơn)
        ]);
    }

    /**
     * 3. Tìm kiếm Booking để thanh toán (Func 3)
     */
    public function searchBooking(Request $request)
    {
        $facilityId = $this->getStaffFacilityId();

        $query = DB::table('invoices')
            ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
            ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
            ->join('users', 'users.user_id', '=', 'invoices.customer_id')
            ->where('facilities.facility_id',$facilityId)
            ->select(
                'invoices.*',
                'facilities.facility_name as facility_name',
                'users.fullname as fullname',
                'invoices.issue_date as issue_date',
                'invoices.final_amount as final_amount',
                'invoice_details.invoice_detail_id as invoice_detail_id',
                'invoice_details.facility_id as facility_id',
                'users.phone as phone',
            )
            ->orderBy('invoices.invoice_id', 'desc');

        // ✅ Thêm điều kiện sau khi query còn là builder
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('users.phone', 'like', "%$search%")
                ->orWhere('users.fullname', 'like', "%$search%");
            });
        }

        // ✅ Chỉ gọi get() sau khi thêm hết điều kiện
        $invoices = $query->get();

        $mybooking_details = [];

        foreach ($invoices as $invoice) {
            $details = DB::table('bookings')
                ->join('invoice_details', 'invoice_details.invoice_detail_id', '=', 'bookings.invoice_detail_id')
                ->join('time_slots', 'time_slots.time_slot_id', '=', 'bookings.time_slot_id')
                ->where('invoice_details.invoice_detail_id', $invoice->invoice_detail_id)
                ->select(
                    'bookings.*',
                    'time_slots.start_time',
                    'time_slots.end_time'
                )->get();

            $mybooking_details[$invoice->invoice_detail_id] = $details;
        }

        $query_contract = DB::table('long_term_contracts')
            ->join('invoice_details', 'long_term_contracts.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
            ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
            ->join('users', 'users.user_id', '=', 'long_term_contracts.customer_id')
            ->where('facilities.facility_id',$facilityId)
            ->select(
                'long_term_contracts.*',
                'facilities.facility_name as facility_name',
                'users.fullname as fullname',
                'long_term_contracts.issue_date as issue_date',
                'long_term_contracts.final_amount as final_amount'
            )
            ->orderBy('long_term_contracts.contract_id', 'desc');
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query_contract->where(function($q) use ($search) {
                $q->where('users.phone', 'like', "%$search%")
                ->orWhere('users.fullname', 'like', "%$search%");
            });
        }
        
        $long_term_contracts = $query_contract->get();

        $mycontract_details = [];

        foreach ($long_term_contracts as $ct) {
            $details = DB::table('bookings')
                ->join('long_term_contracts', 'long_term_contracts.invoice_detail_id', '=', 'bookings.invoice_detail_id')
                ->join('time_slots', 'time_slots.time_slot_id', '=', 'bookings.time_slot_id')
                ->where('long_term_contracts.invoice_detail_id', $ct->invoice_detail_id)
                ->select(
                    'bookings.*',
                    'time_slots.start_time',
                    'time_slots.end_time'
                )
                ->get();

            $mycontract_details[$ct->invoice_detail_id] = $details;
        }
        
        return view('staff.payment', [
            'invoices' => $invoices,
            'mybooking_details' => $mybooking_details,
            'long_term_contracts' => $long_term_contracts,
            'mycontract_details' => $mycontract_details,
        ]);
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

        // View này chỉ có HTML của hóa đơn, không có layout
        return view('staff.invoice_print', compact('invoice'));
    }
}