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
use App\Models\Facilities;
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

        $invoices = DB::table('invoices')
        ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
        ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
        ->join('users', 'users.user_id', '=', 'invoices.customer_id')
        ->join('bookings','bookings.invoice_detail_id','=','invoice_details.invoice_detail_id')
        ->where('facilities.facility_id',$facilityId)
        ->where('bookings.booking_date',$today)
        ->select(
            'invoices.*',
            'facilities.facility_name as facility_name',
            'users.fullname as fullname',
            'invoices.issue_date as issue_date',
            'invoices.final_amount as final_amount',
            'invoice_details.invoice_detail_id as invoice_detail_id',
            'invoice_details.facility_id as facility_id',
        )
        ->distinct()
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
        ->join('bookings','bookings.invoice_detail_id','=','invoice_details.invoice_detail_id')
        ->where('facilities.facility_id',$facilityId)
        ->where('bookings.booking_date',$today)
        ->select(
            'long_term_contracts.*',
            'facilities.facility_name as facility_name',
            'users.fullname as fullname',
            'long_term_contracts.issue_date as issue_date',
            'long_term_contracts.final_amount as final_amount'
        )
        ->distinct()
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

        return view('staff.index', [
            'invoices' => $invoices,
            'mybooking_details' => $mybooking_details,
            'long_term_contracts' => $long_term_contracts,
            'mycontract_details' => $mycontract_details,
            // (Thêm biến $invoice nếu tìm thấy hóa đơn)
        ]);
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
        ->where('invoices.payment_status','Chưa thanh toán')
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
        $today = Carbon::today();

        $query = DB::table('invoices')
            ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
            ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
            ->join('users', 'users.user_id', '=', 'invoices.customer_id')
            ->join('bookings','bookings.invoice_detail_id','=','invoice_details.invoice_detail_id')
            ->where('facilities.facility_id',$facilityId)
            ->where('bookings.booking_date',$today)
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
            ->distinct()
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
            ->join('bookings','bookings.invoice_detail_id','=','invoice_details.invoice_detail_id')
            ->where('facilities.facility_id',$facilityId)
            ->where('bookings.booking_date',$today)
            ->select(
                'long_term_contracts.*',
                'facilities.facility_name as facility_name',
                'users.fullname as fullname',
                'long_term_contracts.issue_date as issue_date',
                'long_term_contracts.final_amount as final_amount'
            )
            ->distinct()
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
        
        return view('staff.index', [
            'invoices' => $invoices,
            'mybooking_details' => $mybooking_details,
            'long_term_contracts' => $long_term_contracts,
            'mycontract_details' => $mycontract_details,
        ]);
    }

    public function invoice_details(Request $request)
    {
        $slots = json_decode($request->slots, true);
        $slotCollection = collect($slots);
        
        $uniqueCourts = $slotCollection->pluck('court_id')->unique()->implode(' , ');
        $uniqueDates = $slotCollection->pluck('booking_date')->unique()->implode(' / ');
        $uniqueTimes = $slotCollection->map(function ($slot) {
            return $slot['start_time'] . ' đến ' . $slot['end_time'];
        })->unique()->implode(' / ');
        // dd($uniqueCourts,$uniqueDates,$uniqueTimes);
        $customer = Users::find($request->input('user_id'));
        $facilities = Facilities::find($request->input('facility_id'));
        $countSlots = count($slots);

        $tempCustomer = [
            'user_id' => $customer->user_id, // thêm user_id để không lỗi
            'fullname' => $request->input('fullname') ?: $customer->fullname,
            'phone' => $request->input('phone') ?: $customer->phone,
            'email' => $request->input('email') ?: $customer->email,
        ];
        if ($countSlots % 2 === 0) {
            $result = ($countSlots / 2) . ' tiếng';
        } else {
            $result = (($countSlots - 1) / 2) . ' tiếng rưỡi';
        }

        $invoice_detail_id = $request->invoice_detail_id;
        $invoices = $request->invoices;
        // Truyền sang view thanh toán
        return view('staff.invoice_details', [
            'slots' => $slots,
            'result' => $result,
            'customer' => (object) $tempCustomer,
            'facilities' => $facilities,
            'invoice_detail_id' => $invoice_detail_id,
            'invoices' => $invoices,
            // TRUYỀN CÁC GIÁ TRỊ DUY NHẤT ĐÃ XỬ LÝ
            'uniqueCourts' => $uniqueCourts,
            'uniqueDates' => $uniqueDates,
            'uniqueTimes' => $uniqueTimes,
        ]);
    }
    
}