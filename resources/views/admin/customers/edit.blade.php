@extends('layouts.admin')

@section('edit_content')
    <h1 class="h3 mb-4">Cập nhật thông tin Khách hàng: {{ $user->fullname }}</h1>

    {{-- Hiển thị lỗi validation (nếu có) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h6>Có lỗi xảy ra:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.customers.update', $user->user_id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Giả lập phương thức PUT --}}

                <h5 class="card-title mb-3 border-bottom pb-2">Thông tin Cá nhân</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fullname" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fullname" name="fullname" 
                               value="{{ old('fullname', $user->fullname) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                         <label for="CCCD" class="form-label">Số CCCD</label>
                        <input type="text" class="form-control" id="CCCD" name="CCCD"
                               value="{{ old('CCCD', $user->CCCD) }}">
                    </div>
                </div>

                 <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="address" name="address"
                           value="{{ old('address', $user->address) }}">
                </div>

                <div class="mb-3">
                     <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                     <div>
                         <div class="form-check form-check-inline">
                             <input class="form-check-input" type="radio" name="status" id="statusActive" value="1" 
                                    {{ old('status', $user->status) == 1 ? 'checked' : '' }}>
                             <label class="form-check-label" for="statusActive">Hoạt động</label>
                         </div>
                         <div class="form-check form-check-inline">
                             <input class="form-check-input" type="radio" name="status" id="statusInactive" value="0"
                                    {{ old('status', $user->status) == 0 ? 'checked' : '' }}>
                             <label class="form-check-label" for="statusInactive">Tạm khóa</label>
                         </div>
                     </div>
                </div>

                <hr class="my-4">
                <h5 class="card-title mb-3 border-bottom pb-2">Đặt Lại Mật Khẩu (Tùy chọn)</h5>
                 <p class="text-muted small">Chỉ điền vào nếu bạn muốn đặt lại mật khẩu cho khách hàng này.</p>
                
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>


                <div class="mt-4">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary me-2">Hủy Bỏ</a>
                    <button type="submit" class="btn btn-primary">Lưu Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
@endsection