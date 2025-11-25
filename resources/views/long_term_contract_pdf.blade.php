<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn hợp đồng</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0 auto;
            max-width: 1300px;
            background: #fff;
            color: #000;
            font-size: 13px;
        }
        h2, h3, p { margin: 4px 0; }
        .header_invoice { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #ccc; padding: 6px; text-align: center; font-size: 13px; }
        table th { background: #f3f3f3; font-weight: bold; }
        .total { text-align: right; font-weight: bold; margin-top: 15px; font-size: 14px; }
        .highlight { color: green; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header_invoice">
        <h2>BẢNG BÁO GIÁ HỢP ĐỒNG</h2>
        <h3>{{ $facilities->facility_name }}</h3>
        <p><strong>Địa chỉ:</strong> {{ $facilities->address }}</p>
        <p><strong>Liên hệ:</strong> {{ $facilities->phone }}</p>
    </div>
@php
    // Lấy tất cả ngày từ $slots
    $dates = collect($slots)->pluck('booking_date')->unique();

    // Lấy thứ tương ứng (Thứ Hai = 2, ..., Chủ Nhật = 7)
    $weekdays = $dates->map(function($date){
        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeekIso; // 1 = Thứ Hai ... 7 = Chủ Nhật
        return $dayOfWeek;
    })->unique()->sort()->values();

    // Chuyển số sang tên Thứ nếu muốn
    $weekdayNames = $weekdays->map(function($d){
        return match($d){
            1 => '2',
            2 => '3',
            3 => '4',
            4 => '5',
            5 => '6',
            6 => '7',
            7 => 'CN',
        };
    });

    $weekdaysString = $weekdayNames->implode(', ');
@endphp
    <div class="customer-info">
        <p><strong>Khách hàng:</strong> {{ $customer->fullname }}</p>
        <p><strong>Số điện thoại:</strong> {{ $customer->phone }}</p>
        <p><strong>Thời gian:</strong> 
            Từ {{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} 
            đến {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
        </p>
        <p><strong>Thứ:</strong> {{ $weekdaysString }}</p>
        <p><strong>Sân:</strong> {{ $courts }}</p>
    </div>

    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Sân</th>
                <th>Bắt đầu</th>
                <th>Kết thúc</th>
                <th>Thời lượng</th>
                <th>Giá</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Gom slot theo ngày
                $groupedByDate = [];
                foreach ($slots as $slot) {
                    $date = $slot['booking_date'];
                    if (!isset($groupedByDate[$date])) {
                        $groupedByDate[$date] = [
                            'courts' => [],
                            'start_times' => [],
                            'end_times' => [],
                            'total_minutes' => 0,
                            'total_price' => 0,
                        ];
                    }

                    $groupedByDate[$date]['courts'][] = $slot['courts'];
                    $start = strtotime($slot['start_time']);
                    $end = strtotime($slot['end_time']);
                    $minutes = ($end - $start) / 60;

                    $groupedByDate[$date]['start_times'][] = $start;
                    $groupedByDate[$date]['end_times'][] = $end;
                    $groupedByDate[$date]['total_minutes'] += $minutes;
                    $groupedByDate[$date]['total_price'] += $slot['price'];
                }
            @endphp

            @foreach ($groupedByDate as $date => $data)
                @php
                    $earliest = date('H:i', min($data['start_times']));
                    $latest = date('H:i', max($data['end_times']));
                    $minutes = $data['total_minutes'];
                    $duration = $minutes >= 60 
                        ? floor($minutes/60) . " giờ " . ($minutes%60 > 0 ? ($minutes%60)." phút" : "")
                        : $minutes . " phút";
                    $courtsString = implode(', ', array_unique($data['courts']));
                @endphp
                <tr>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</td>
                    <td>{{ $courtsString }}</td>
                    <td>{{ $earliest }}</td>
                    <td>{{ $latest }}</td>
                    <td>{{ $duration }}</td>
                    <td>{{ number_format($data['total_price'], 0, ',', '.') }} đ</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Thành tiền: <span class="highlight">{{ number_format($contract->total_amount, 0, ',', '.') }} đ</span></p>
    @if (!empty($promotions) && !empty($promotions->description) && !empty($promotions->value))
        <p >Khuyến mãi: <span class="highlight">{{ $promotions->description }}</span></p>
        <p >Giảm: <span class="highlight">{{ $promotions->value*100 .'%' }}</span></p>
    @endif
    <p class="total">Thành tiền: <span class="highlight" style="font-size: 30px;">{{ number_format($contract->final_amount, 0, ',', '.') }} đ</span></p>
</div>

</body>
</html>
