@extends('layouts.main')

@section('payment_content')
<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Xác nhận thông tin thanh toán</h2>
    <h1>Thông tin sân: {{ $facility_id }}</h1>
    <h1>Thông tin khách hàng: {{ $user_id }}</h1>
    @if (!empty($slots))
        <table class="table table-bordered">
            <thead>
                <tr class="bg-gray-100 text-center">
                    <th>Sân</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Ngày</th>
                    <th>Giá</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp

                @foreach ($slots as $slot)
                    @php $total += $slot['price']; @endphp
                    <tr class="text-center">
                        <td>{{ $slot['court'] }}</td>
                        <td>{{ $slot['start_time'] }}</td>
                        <td>{{ $slot['end_time'] }}</td>
                        <td>{{ $slot['date'] }}</td>
                        <td>{{ number_format($slot['price']) }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mt-3">
            <h3 class="text-lg font-semibold">Tổng tiền: {{ number_format($total) }} đ</h3>
        </div>

        <button class="btn btn-success mt-4">Xác nhận thanh toán</button>
    @else
        <p>Không có dữ liệu khung giờ nào!</p>
    @endif
</div>
@endsection
