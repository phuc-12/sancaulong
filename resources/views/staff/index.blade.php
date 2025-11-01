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
                                <th>Trạng thái (Thanh toán)</th>
                                <th>Check-in (Chức năng)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookingsToday as $booking)
                            <tr>
                                {{-- Giờ đặt --}}
                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                                {{-- Khách hàng --}}
                                <td>
                                    <div>{{ $booking->user_fullname ?? 'Khách vãng lai' }}</div>
                                    @if($booking->user_phone)
                                        <div class="small text-muted">{{ $booking->user_phone }}</div>
                                    @endif
                                </td>
                                {{-- Sân --}}
                                <td>{{ $booking->court_name ?? 'N/A' }}</td>
                                {{-- Trạng thái --}}
                                <td>
                                    @if($booking->status == 'Đã sử dụng')
                                        <span class="badge bg-secondary">Đã sử dụng</span>
                                    @elseif($booking->status == 'Đã thanh toán' || $booking->status == 'Đã thanh toán (Online)')
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @elseif($booking->status == 'Chưa thanh toán' || $booking->status == null)
                                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                    @else
                                        <span class="badge bg-danger">{{ $booking->status }}</span> {{-- (vd: Đã hủy) --}}
                                    @endif
                                </td>
                                {{-- Nút Check-in --}}
                                <td class="text-center">
                                    @if($booking->status == 'Đã sử dụng')
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Đã check-in</button>
                                    @elseif($booking->status == 'Đã thanh toán' || $booking->status == 'Chưa thanh toán' || $booking->status == null)
                                        <form action="{{ route('staff.booking.confirm', $booking->booking_id) }}" method="POST" onsubmit="return confirm('Xác nhận khách đã đến sân?')">
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