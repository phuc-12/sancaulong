<!DOCTYPE html>
<html>
<head>
    <title>Hóa Đơn</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .container { width: 80%; margin: auto; }
        .header { text-align: center; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: center; }
        .total { text-align: right; font-weight: bold; }
        .qr-code { text-align: center;}
        .qr-code img { width: 250px;}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>HÓA ĐƠN THANH TOÁN</h2>
            <p>Địa điểm: {{ $facilities->facility_name }}</p>
            <p>Địa chỉ: {{ $facilities->address }}</p>
        </div>
        
        <div style="float:left;width:48%; border-right: 1px solid black; padding-right: 3px;">
            <h3 style="text-align: center">Thông tin đặt sân</h3>
            <p>Sân số: {{ $uniqueCourts }}</p>
            <p>Ngày: {{ $uniqueDates }}</p>
            <p>Thời gian: <br>
                {{ $uniqueTimes }}</p>
            <p>Tổng thời gian: {{ $result }}</p>
        </div>
        
        <div style="float:right;width: 50%;padding-left: 10px;">
            <h3 style="text-align: center">Thông tin khách hàng</h3>
            <p>Tên: {{ $customer->fullname }}</p>
            <p>SĐT: {{ $customer->phone }}</p>
            <p>Email: {{ $customer->email }}</p>
        </div>

        <div style="clear: both">
            <h3 style="text-align: center">Chi tiết thanh toán</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sân</th>
                        <th>Bắt đầu</th>
                        <th>Kết thúc</th>
                        <th>Ngày</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($slots as $slot)
                        <tr>
                            <td>{{ $slot['court_id'] }}</td>
                            <td>{{ $slot['start_time'] }}</td>
                            <td>{{ $slot['end_time'] }}</td>
                            <td>{{ $slot['booking_date'] }}</td>
                            <td>{{ number_format($slot['unit_price']) }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total">
            <p>Tổng tiền: {{ number_format($total) }} đ</p>
        </div>
        <div class="qr-code">
            <h3>Quét mã QR để thanh toán</h3>
            <img src="{{ $qrUrl }}" alt="QR Code">
        </div>
    </div>
</body>
</html>