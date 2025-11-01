@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4">Lịch Đặt Sân Hôm Nay</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Danh sách khách check-in</h5>
        </div>
        <div class="card-body">
            @if($bookingsToday->isEmpty())
                <div class="alert alert-secondary text-center mb-0">Không có lượt đặt nào hôm nay.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Giờ đặt</th>
                                <th>Khách hàng</th>
                                <th>Sân</th>
                                <th class="text-center">Trạng thái (Thanh toán)</th>
                                <th class="text-center">Check-in (Chức năng)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookingsToday as $booking)
                                <tr>
                                    {{-- Giờ đặt--}}
                                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </td>

                                    {{-- HIỂN THỊ KHÁCH HÀNG --}}
                                    <td>
                                        {{-- Truy cập qua relationship: $booking->user->fullname --}}
                                        {{-- Nếu $booking->user là null (vd: user_id không tồn tại), nó sẽ lấy 'Khách vãng lai' --}}
                                        <div>{{ $booking->user->fullname ?? 'Khách vãng lai' }}</div>

                                        {{-- Kiểm tra $booking->user TỒN TẠI VÀ $booking->user->phone CÓ GIÁ TRỊ --}}
                                        @if(isset($booking->user) && $booking->user->phone)
                                            <div class="small text-muted">({{ $booking->user->phone }} )</div>
                                        @else
                                            <div class="small text-muted">(Chưa có SĐT)</div> {{-- Hiển thị nếu SĐT là NULL --}}
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Truy cập qua relationship: $booking->court->court_name --}}
                                        {{ $booking->court->court_name ?? 'N/A' }}
                                    </td>
                                    {{-- Trạng thái --}}
                                    <td class="text-center">
                                        @if($booking->status == 'Đã sử dụng')
                                            <span class="badge bg-secondary">Đã sử dụng</span>
                                        @elseif($booking->status == 'Đã thanh toán' || $booking->status == 'Đã thanh toán (Online)')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @elseif($booking->status == 'Chưa thanh toán' || $booking->status == null)
                                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                        @else
                                            <span class="badge bg-danger">{{ $booking->status == 'Đã hủy'}}</span>
                                        @endif
                                    </td>
                                    {{-- Nút Check-in --}}
                                    <td class="text-center">
                                        @if($booking->status == 'Đã sử dụng')
                                            <button class="btn btn-sm btn-outline-secondary" disabled>Đã check-in</button>
                                        @elseif($booking->status == 'Đã thanh toán' || $booking->status == 'Đã thanh toán (Online)' || $booking->status == 'Chưa thanh toán' || $booking->status == null)
                                            <form class="d-inline" action="{{ route('staff.booking.confirm', $booking->booking_id) }}" method="POST"
                                                onsubmit="return confirm('Xác nhận khách đã đến sân?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Xác nhận đến sân</button>
                                            </form>
                                        @else
                                            {{-- (Không hiển thị nút nếu đã hủy) --}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection