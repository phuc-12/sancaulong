@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4">Lịch Đặt Sân Hôm Nay</h1>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Danh sách khách check-in</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Giờ đặt</th>
                        <th>Khách hàng</th>
                        <th>Sân</th>
                        <th>Trạng thái (Thanh toán)</th>
                        <th>Check-in (Chức năng)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- (Dùng @foreach để lặp qua $bookingsToday) --}}
                    <tr>
                        <td>18:00 - 19:00</td>
                        <td>Nguyễn Văn A (090xxxx123)</td>
                        <td>Sân 2</td>
                        <td><span class="badge bg-warning">Chưa thanh toán</span></td>
                        <td>
                            <button class="btn btn-sm btn-success">Xác nhận đến sân</button>
                        </td>
                    </tr>
                     <tr>
                        <td>19:00 - 20:00</td>
                        <td>Trần Thị B (091xxxx456)</td>
                        <td>Sân 3</td>
                        <td><span class="badge bg-success">Đã thanh toán (Online)</span></td>
                        <td>
                            <button class="btn btn-sm btn-success">Xác nhận đến sân</button>
                        </td>
                    </tr>
                    <tr>
                        <td>19:00 - 20:00</td>
                        <td>Lê Văn C (098xxxx789)</td>
                        <td>Sân 1</td>
                        <td><span class="badge bg-secondary">Đã sử dụng</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" disabled>Đã check-in</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection