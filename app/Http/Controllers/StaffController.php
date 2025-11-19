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
use App\Models\Time_slots;
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
        ]);
    }

    public function invoice_history (Request $request)
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
            'invoice_details.facility_id as facility_id',
        )
        ->orderBy('invoices.invoice_id', 'desc')
        ->paginate(10);

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

        return view('staff.invoice_history', [
            'invoices' => $invoices,
            'mybooking_details' => $mybooking_details,
        ]);
    }

    public function searchHistory(Request $request)
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

        return view('staff.invoice_history', [
            'invoices' => $invoices,
            'mybooking_details' => $mybooking_details,
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
        ->paginate(10)
        ->appends(['user_id' => 'users.user_id']);

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
        ->paginate(10)
        ->appends(['user_id' => 'users.user_id']);

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
        $invoice_id = $request->invoice_id;
        $success = session('success');
        $invoices = DB::table('invoices')->where('invoice_id',$invoice_id)->first();
        // dd($invoices);
        // Truyền sang view thanh toán
        return view('staff.invoice_details', [
            'slots' => $slots,
            'result' => $result,
            'customer' => (object) $tempCustomer,
            'facilities' => $facilities,
            'invoice_detail_id' => $invoice_detail_id,
            'invoices' => $invoices,
            'success' => $success,
            // TRUYỀN CÁC GIÁ TRỊ DUY NHẤT ĐÃ XỬ LÝ
            'uniqueCourts' => $uniqueCourts,
            'uniqueDates' => $uniqueDates,
            'uniqueTimes' => $uniqueTimes,
        ]);
    }

    public function booking_directly(Request $request)
    {
        $idSan = $this->getStaffFacilityId();
        // Lấy thông tin sân
        $thongtinsan = Facilities::where('facility_id', $idSan)->firstOrFail();

        // Lấy danh sách khung giờ
        $timeSlots = Time_slots::all();

        // Lấy danh sách ngày (7 ngày tiếp theo, ví dụ)
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = now()->addDays($i)->format('Y-m-d');
        }

        // Lấy danh sách đặt sân từ DB
        $bookings = Bookings::where('facility_id', $idSan)
            ->whereIn('booking_date', $dates)
            ->get(['booking_date', 'time_slot_id', 'court_id']);

        $bookingsData = [];
        foreach ($bookings as $b) {
            $bookingsData[$b->booking_date][$b->time_slot_id][$b->court_id] = true;
        }


        // Từ điển chuyển đổi thứ sang tiếng Việt
        $thuTiengViet = [
            'Mon' => 'Thứ hai',
            'Tue' => 'Thứ ba',
            'Wed' => 'Thứ tư',
            'Thu' => 'Thứ năm',
            'Fri' => 'Thứ sáu',
            'Sat' => 'Thứ bảy',
            'Sun' => 'Chủ nhật',
        ];

        // Số sân con
        $soLuongSan = $thongtinsan->quantity_court;

        // Tạo danh sách sân con như: San 1, San 2, San 3...
        $dsSanCon = [];
        for ($i = 1; $i <= $soLuongSan; $i++) {
            $dsSanCon[] = [
                'id' => $thongtinsan->facility_id . '-' . $i,   // Ví dụ SAN001-1
                'ten' => 'Sân ' . $i
            ];
        }

        $success_message = $request->input('success_message');
        // dd($customer->toArray());
        return view('staff.booking_directly', compact('thongtinsan', 'timeSlots', 'dates', 'bookingsData', 'thuTiengViet', 'soLuongSan', 'dsSanCon', 'success_message'));
    }

    public function addSlot(Request $request)
    {
        $slots = session('selected_slots', []);

        $slotInfo = [
            'court' => $request->court,
            'date' => $request->date,
            'slot' => $request->slot,
            'price' => $request->price,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ];

        // Toggle: nếu đã tồn tại thì xóa
        $existsKey = null;
        foreach ($slots as $key => $s) {
            if ($s['court'] == $slotInfo['court'] && $s['date'] == $slotInfo['date'] && $s['slot'] == $slotInfo['slot']) {
                $existsKey = $key;
                break;
            }
        }

        if ($existsKey !== null) {
            unset($slots[$existsKey]);
            $slots = array_values($slots);
        } else {
            $slots[] = $slotInfo;
        }

        session(['selected_slots' => $slots]);
        return response()->json($slots);
    }

    public function removeSlot(Request $request)
    {
        $slots = session('selected_slots', []);
        unset($slots[$request->index]);
        session(['selected_slots' => array_values($slots)]);

        return response()->json(array_values($slots));
    }
    
    public function addInvoice(Request $request)
    {
        $slots = json_decode($request->input('slots'), true);
        $invoiceDetailId = $request->input('invoice_detail_id');
        $userId = $request->input('user_id');
        $facility_id = $request->input('facility_id');

        $fullname = $request->input('fullname');
        $phone = $request->input('phone');
        
        $currentUser = Users::where('phone', $phone)
        ->where('fullname', 'like', "%$fullname%")
        ->first();

        if (!$currentUser) {
            $newUser = Users::create([
                'fullname' => $fullname,
                'phone' => $phone,
                'password' => bcrypt('123456789'), // hoặc tạo password mặc định
                'role_id' => '5', // ví dụ
            ]);
            $userId = $newUser->user_id;
        } else {
            $userId = $currentUser->user_id;
        }

        $total = 0;
        foreach ($slots as $slot) {
            $total += $slot['price'];
        }
        $promotion_id = null;
        $payment_method = 1;
        $payment_status = 'Chưa thanh toán';
        DB::table(table: 'invoice_details')->insert([
            'invoice_detail_id' => $invoiceDetailId,
            'sub_total' => $total,
            'facility_id' => $facility_id,
        ]);
        $invoice_details = DB::table('invoice_details')
            ->select('invoice_id')
            ->where('invoice_detail_id', $invoiceDetailId)
            ->first();

        DB::table(table: 'invoices')->insert([
            'invoice_id' => $invoice_details->invoice_id,
            'customer_id' => $userId,
            'issue_date' => now(),
            'total_amount' => $total,
            'promotion_id' => $promotion_id,
            'final_amount' => $total,
            'payment_status' => $payment_status,
            'payment_method' => $payment_method,
        ]);
        if (!$slots || !is_array($slots)) {
            return back()->with('error', 'Không có dữ liệu đặt sân!');
        }

        // dd($slots, $invoiceDetailId, $userId, $facility_id);
        foreach ($slots as $slot) {
            DB::table(table: 'bookings')->insert([
                'invoice_detail_id' => $invoiceDetailId,
                'user_id' => $userId,
                'facility_id' => $facility_id,
                'court_id' => $slot['court'],
                'booking_date' => \Carbon\Carbon::parse($slot['date'])->format('Y-m-d'),
                'time_slot_id' => $slot['time_slot_id'],
                'unit_price' => $slot['price'],
                'status' => 'Đã thanh toán (Online)'
            ]);
        }

        return view('staff.redirect_post', [
            'facility_id' => $facility_id,
            'user_id' => $userId,
            'success_message' => 'Đã lưu lịch đặt của khách hàng!'
        ]);

    }

    public function cancel_invoice(Request $request)
    {
        $invoice_detail_id = $request->invoice_detail_id;
        $user_id = $request->user_id;

        Bookings::where('invoice_detail_id', $invoice_detail_id)->delete();

        $invoice_details = DB::table('invoice_details')
        ->where('invoice_detail_id', $invoice_detail_id)
        ->select('invoice_id')
        ->first();

        DB::table('invoices')->where('invoice_id', $invoice_details->invoice_id)->update([
            'payment_status' => 'Đã Hủy',
        ]);

        return redirect()->route('staff.payment') // hoặc route phù hợp
                     ->with('success_message', 'Hủy lịch thành công!');
    }

}