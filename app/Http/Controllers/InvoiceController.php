<?php

namespace App\Http\Controllers;

use App\Models\Invoice; // Điều chỉnh theo model của bạn
use App\Models\Users;
use App\Models\Facilities;
use App\Models\InvoiceDetail; // Điều chỉnh theo model của bạn
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class InvoiceController extends Controller
{
    public function exportInvoice(Request $request)
    {
        // Lấy dữ liệu từ request
        $slots = json_decode($request->slots, true);
        $total = $request->input('total');
        $slotCollection = collect($slots);
        
        $user_id_nv = $request->user_id_nv;
        $fullname_nv = $request->fullname_nv;
        $invoice_time = now()->format('d/m/Y H:i:s');
        // dd($user_id_nv,$fullname_nv);
        $uniqueCourts = $slotCollection->pluck('court_id')->unique()->implode(' , ');
        $uniqueDates = $slotCollection->pluck('booking_date')->unique()->implode(' / ');
        $uniqueTimes = $slotCollection->map(function ($slot) {
            return $slot['start_time'] . ' đến ' . $slot['end_time'];
        })->unique()->implode(' / ');
        // dd($uniqueCourts,$uniqueDates,$uniqueTimes);
        $customer = Users::find($request->input('user_id'));
        $facilities = Facilities::find($request->input('facility_id'));
        $countSlots = count($slots);

        if ($countSlots % 2 === 0) {
            $result = ($countSlots / 2) . ' tiếng';
        } else {
            $result = (($countSlots - 1) / 2) . ' tiếng rưỡi';
        }

        $invoice_detail_id = $request->invoice_detail_id;
        $invoices = DB::table('invoice_details')->where('invoice_detail_id',$invoice_detail_id)
        ->first();
        // Tạo URL QR code
        $bank = $facilities->account_bank ?? 'VCB';
        $account = $facilities->account_no ?? '9704366899999';
        $accountName = $facilities->account_name ?? 'SAN CAU LONG DEMO';
        $qrUrl = "https://img.vietqr.io/image/{$bank}-{$account}-compact2.png?amount={$total}&addInfo=Thanh%20toan%20dat%20san&accountName=" . urlencode($accountName);

        // Chuẩn bị dữ liệu cho view PDF
        $data = [
            'slots' => $slots,
            'result' => $result,
            'customer' => $customer,
            'facilities' => $facilities,
            'invoice_detail_id' => $invoice_detail_id,
            'total' => $total,
            'user_id_nv' => $user_id_nv,
            'fullname_nv' => $fullname_nv,
            'invoice_time' => $invoice_time,
            'invoice_id' => $invoices->invoice_id,

            'uniqueCourts' => $uniqueCourts,
            'uniqueDates' => $uniqueDates,
            'uniqueTimes' => $uniqueTimes,

            'qrUrl' => $qrUrl,
        ];
        // dd($data);
        // Tải view PDF
        $pdf = Pdf::setOptions([
            'isRemoteEnabled' => true,
        ])->loadView('staff.pdf', $data);

        // Tải xuống PDF
        return $pdf->download('hoa_don_' . $invoice_detail_id . '.pdf');
    }

    public function exportInvoice_cus(Request $request)
    {
        // Lấy dữ liệu từ request
        $slots = json_decode($request->slots, true);
        $total = $request->input('total');
        $slotCollection = collect($slots);
        
        $user_id_nv = $request->user_id_nv;
        $fullname_nv = $request->fullname_nv;
        $invoice_time = now()->format('d/m/Y H:i:s');
        // dd($user_id_nv,$fullname_nv);
        $uniqueCourts = $slotCollection->pluck('court_id')->unique()->implode(' , ');
        $uniqueDates = $slotCollection->pluck('booking_date')->unique()->implode(' / ');
        $uniqueTimes = $slotCollection->map(function ($slot) {
            return $slot['start_time'] . ' đến ' . $slot['end_time'];
        })->unique()->implode(' / ');
        // dd($uniqueCourts,$uniqueDates,$uniqueTimes);
        $customer = Users::find($request->input('user_id'));
        $facilities = Facilities::find($request->input('facility_id'));
        $countSlots = count($slots);

        if ($countSlots % 2 === 0) {
            $result = ($countSlots / 2) . ' tiếng';
        } else {
            $result = (($countSlots - 1) / 2) . ' tiếng rưỡi';
        }

        $invoice_detail_id = $request->invoice_detail_id;
        $invoices = DB::table('invoice_details')->where('invoice_detail_id',$invoice_detail_id)
        ->first();
        // Tạo URL QR code
        $bank = $facilities->account_bank ?? 'VCB';
        $account = $facilities->account_no ?? '9704366899999';
        $accountName = $facilities->account_name ?? 'SAN CAU LONG DEMO';
        $qrUrl = "https://img.vietqr.io/image/{$bank}-{$account}-compact2.png?amount={$total}&addInfo=Thanh%20toan%20dat%20san&accountName=" . urlencode($accountName);

        $slots = array_map(function($item) {
            return [
                "court"        => $item["court_id"],
                "date"         => Carbon::parse($item["booking_date"])->format('d-m-Y'),
                "start_time"   => Carbon::parse($item["start_time"])->format('H:i'),
                "end_time"     => Carbon::parse($item["end_time"])->format('H:i'),
                "time_slot_id" => $item["time_slot_id"],
                "price"        => (int) $item["unit_price"],
                "btnElement"   => [],
            ];
        }, $slots);
        // Chuẩn bị dữ liệu cho view PDF
        $data = [
            'slots' => $slots,
            'result' => $result,
            'customer' => $customer,
            'facilities' => $facilities,
            'invoice_detail_id' => $invoice_detail_id,
            'total' => $total,
            'user_id_nv' => $user_id_nv,
            'fullname_nv' => $fullname_nv,
            'invoice_time' => $invoice_time,
            'invoice_id' => $invoices->invoice_id,

            'uniqueCourts' => $uniqueCourts,
            'uniqueDates' => $uniqueDates,
            'uniqueTimes' => $uniqueTimes,

            'qrUrl' => $qrUrl,
        ];
        // dd($data);
        // Tải view PDF
        $pdf = Pdf::setOptions([
            'isRemoteEnabled' => true,
        ])->loadView('pdf', $data);

        // Tải xuống PDF
        return $pdf->download('hoa_don_' . $invoice_detail_id . '.pdf');
    }

    public function confirm_payment(Request $request)
    {
        $request->validate([
            'payment_status' => 'required|in:paid,unpaid',
            'invoice_detail_id' => 'required',
        ]);

        $invoice_detail_id = $request->invoice_detail_id;
        $payment_status = $request->payment_status;

        $status = $payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';

        DB::table('invoices')
            ->join('invoice_details','invoice_details.invoice_id','=','invoices.invoice_id')
            ->where('invoice_details.invoice_detail_id', $invoice_detail_id)
            ->update([
                'payment_status' => $status,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thanh toán thành công!',
            'payment_status' => $status
        ]);
    }
}