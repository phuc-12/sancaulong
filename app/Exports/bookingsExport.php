<?php
namespace App\Exports;

use App\Models\Bookings;
use Maatwebsite\Excel\Facades\Excel;

class BookingsExport
{
    protected $bookings;

    public function __construct($bookings)
    {
        $this->bookings = $bookings;
    }

    public function export($filename = 'export')
    {
        // sử dụng Facade Excel
        Excel::create($filename, function($excel) {
            $excel->sheet('Báo cáo', function($sheet) {
                $sheet->row(1, [
                    'Mã Booking', 'Ngày đặt', 'Giờ bắt đầu', 'Giờ kết thúc',
                    'Tên cơ sở', 'Tên khách hàng', 'Số điện thoại', 'Giá (VND)', 'Trạng thái'
                ]);

                $row = 2;
                foreach ($this->bookings as $b) {
                    $sheet->row($row, [
                        $b->booking_id,
                        \Carbon\Carbon::parse($b->booking_date)->format('d/m/Y'),
                        \Carbon\Carbon::parse($b->start_time)->format('H:i'),
                        \Carbon\Carbon::parse($b->end_time)->format('H:i'),
                        $b->facility_name,
                        $b->fullname,
                        $b->phone,
                        number_format($b->unit_price,0,',','.'),
                        $b->status ?? 'Chưa xác nhận'
                    ]);
                    $row++;
                }
            });
        })->download('xlsx'); // download ngay
    }
}
