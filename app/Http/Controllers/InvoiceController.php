<?php

namespace App\Http\Controllers;

use App\Models\Invoice; // Điều chỉnh theo model của bạn
use App\Models\Users;
use App\Models\Facilities;
use App\Models\InvoiceDetail; // Điều chỉnh theo model của bạn
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class InvoiceController extends Controller
{
    public function exportInvoice(Request $request)
    {
        // Lấy dữ liệu từ request
        $slots = json_decode($request->slots, true);
        $total = $request->input('total');
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

        if ($countSlots % 2 === 0) {
            $result = ($countSlots / 2) . ' tiếng';
        } else {
            $result = (($countSlots - 1) / 2) . ' tiếng rưỡi';
        }

        $invoice_detail_id = $request->invoice_detail_id;
        $invoices = $request->invoices;
        
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
            // 'invoices' => $invoices,
            // TRUYỀN CÁC GIÁ TRỊ DUY NHẤT ĐÃ XỬ LÝ
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
}