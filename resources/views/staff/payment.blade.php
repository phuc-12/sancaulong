@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4">Thanh Toán Tại Quầy & In Hóa Đơn</h1>

    {{-- Hiển thị thông báo (Thành công/Lỗi) --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
     @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
     @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
        </div>
     @endif

    <div class="row">
        {{-- Cột trái: Thông tin & Thanh toán --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin Hóa đơn</h5>
                </div>
                <div class="card-body">
                    {{-- Form Tìm Kiếm --}}
                    <form action="{{ route('staff.payment.search') }}" method="POST">
                        @csrf
                        <label for="search_term" class="form-label">Tìm kiếm SĐT khách hàng / Mã đặt</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="search_term" name="search_term" 
                                   placeholder="Nhập SĐT hoặc Mã đặt..." 
                                   value="{{ $booking->booking_id ?? old('search_term') }}">
                            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                        </div>
                    </form>

                    {{-- Chỉ hiển thị nếu đã tìm thấy booking --}}
                    @if(isset($booking) && $booking)
                        <hr class="my-4">
                        <h5 class="mb-3">Thông tin thanh toán:</h5>
                        
                        <dl class="row">
                            <dt class="col-sm-3">Khách hàng:</dt>
                            <dd class="col-sm-9">{{ $booking->user_fullname ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Mã đặt:</dt>
                            <dd class="col-sm-9">#{{ $booking->booking_id }}</dd>

                            <dt class="col-sm-3">Sân:</dt>
                            <dd class="col-sm-9">{{ $booking->court_name ?? 'N/A' }} ({{ $booking->time_range ?? 'N/A' }})</dd>

                            <dt class="col-sm-3">Tổng tiền:</dt>
                            <dd class="col-sm-9"><strong class="fs-4 text-danger">{{ number_format($booking->unit_price, 0, ',', '.') }}đ</strong></dd>
                        </dl>
                        
                        {{-- Form Xác Nhận Thanh Toán --}}
                        <form action="{{ route('staff.booking.pay', $booking->booking_id) }}" method="POST" onsubmit="return confirm('Xác nhận thanh toán?')">
                            @csrf
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="Tiền mặt (Tại quầy)">Tiền mặt (Tại quầy)</option>
                                    <option value="Chuyển khoản (Tại quầy)">Chuyển khoản (Tại quầy)</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Xác nhận Thanh toán</button>
                            
                            {{-- Nút In Hóa Đơn (nếu đã thanh toán) --}}
                            @if(session('last_invoice_id'))
                                <a href="{{ route('staff.invoice.print', session('last_invoice_id')) }}" 
                                   class="btn btn-outline-secondary ms-2" target="_blank"> {{-- target="_blank" để mở tab mới --}}
                                    <i class="bi bi-printer me-1"></i> In Hóa đơn
                                </a>
                            @endif
                        </form>
                    @endif
                    
                </div>
            </div>
        </div>

        {{-- Cột phải: Tìm Hóa đơn cũ --}}
        <div class="col-lg-5">
            <div class="card shadow-sm">
                 <div class="card-header"><h5 class="mb-0">Tìm Hóa đơn cũ</h5></div>
                <div class="card-body">
                    <form action="#" method="GET"> {{-- Cần 1 route khác để tìm HĐ cũ --}}
                        <label for="old_invoice_id" class="form-label">Nhập mã hóa đơn để in lại:</label>
                        <input type="text" class="form-control" id="old_invoice_id" name="old_invoice_id" placeholder="Mã HĐ: 1256...">
                        <button type="submit" class="btn btn-info mt-2"><i class="bi bi-search me-1"></i> Tìm & In lại</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection