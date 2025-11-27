<!DOCTYPE html>
<html>
<head>
    <title>Hóa Đơn Thanh Toán</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 720px;
            margin: 0 auto;
        }
        h2 { 
            text-align: center; 
            margin: 8px 0 10px; 
            padding: 0;
        }

        .section-title {
            background: #f2f2f2;
            padding: 4px 0;
            text-align: center;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0 6px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-table th, 
        .payment-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 6px;
        }

        .qr-code img {
            width: 250px;
        }

        .qr-code p { margin: 4px 0; }
    </style>
</head>
<body>

<div class="container">

    <h2>HÓA ĐƠN THANH TOÁN</h2>

    <p><strong>Tên sân:</strong> {{ $facilities->facility_name }}</p>
    <p><strong>Địa chỉ:</strong> {{ $facilities->address }}</p>
    <p><strong>Mã hóa đơn:</strong> {{ $invoice_id }}</p>
    <p><strong>Thời gian xuất hóa đơn:</strong> {{ $invoice_time }}</p>
    <!-- ============ THÔNG TIN KHÁCH HÀNG ============ -->
    <table class="info-table" style="margin-top: 5px;">
        <tr>
            <td>

                <div class="section-title">Thông tin khách hàng</div>

                <p><strong>Họ tên:</strong> {{ $customer->fullname }}</p>
                <p><strong>SĐT:</strong> {{ $customer->phone }}</p>
                <p><strong>Email:</strong> {{ $customer->email }}</p>

                <hr>

                @if ($user_id_nv && $fullname_nv)
                    <p><strong>Nhân viên thanh toán:</strong></p>
                    <p>Mã: {{ $user_id_nv }}</p>
                    <p>Tên: {{ $fullname_nv }}</p>
                @endif

            </td>
        </tr>
    </table>

    <!-- ============ CHI TIẾT THANH TOÁN ============ -->
    <h3 style="text-align:center; margin: 12px 0;">Chi tiết thanh toán</h3>

    <table class="payment-table">
        <thead>
            <tr>
                <th>Sân</th>
                <th>Ngày</th>
                <th>Bắt đầu</th>
                <th>Kết thúc</th>
                <th>Thời lượng</th>
                <th>Giá</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Đếm số dòng cho từng ngày
                $dateCounts = [];
                foreach ($slots as $slot) {
                    $dateCounts[$slot['booking_date']] = ($dateCounts[$slot['booking_date']] ?? 0) + 1;
                }

                // Danh sách ngày đã in rowspan
                $printedDates = [];
            @endphp
            @foreach ($slots as $slot)
                @php
                    // Tính số phút cho từng slot
                    $start = strtotime($slot['start_time']);
                    $end = strtotime($slot['end_time']);
                    $minutes = ($end - $start) / 60;
                    $duration = $minutes >= 60 
                        ? floor($minutes/60) . " giờ " . ($minutes%60 > 0 ? ($minutes%60)." phút" : "")
                        : $minutes . " phút";
                @endphp

                <tr>
                    <td>{{ $slot['court_id'] }}</td>
                    @if (!in_array($slot['booking_date'], $printedDates))
                        <td rowspan="{{ $dateCounts[$slot['booking_date']] }}" 
                            style="vertical-align: middle; text-align: center;">
                            {{ $slot['booking_date'] }}
                        </td>
                        @php $printedDates[] = $slot['booking_date']; @endphp
                    @endif
                    <td>{{ $slot['start_time'] }}</td>
                    <td>{{ $slot['end_time'] }}</td>
                    <td>{{ $duration }}</td>
                    <td>{{ number_format($slot['unit_price']) }} đ</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="">Tổng thời gian: {{ $result }}</p>
    <p class="">Thành tiền: {{ number_format($total) }} đ</p>
    <p class="">
        Khuyến mãi: {{ $description->description ?? 'Không có' }}
    </p>
    <h3 class="total">Tổng tiền: {{ number_format($total_final) }} đ</h3>
    
    <!-- ============ QR THANH TOÁN ============ -->
    <div class="qr-code" style="text-align:center; margin-top: 12px;">
        <p><strong>Quét mã QR để thanh toán</strong></p>
        <img src="{{ $qrUrl }}">
    </div>

</div>

</body>
</html>
