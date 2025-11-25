@extends('layouts.header') 
@section('edit_cus_content')

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <h2 class="mb-4">Hồ Sơ Của Bạn</h2>

            {{-- Hiển thị thông báo thành công --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Hiển thị lỗi validation --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    
                    {{-- enctype="multipart/form-data" để upload file --}}
                    <form action="{{ route('profile.update', ['id' => $user->user_id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') 

                        {{-- PHẦN AVATAR --}}
                        <div class="row align-items-center mb-4">
                            <div class="col-auto">
                                <img id="avatarPreview" 
                                     src="{{ $user->avatar ? asset($user->avatar) : asset('img/profiles/avatar-05.jpg') }}" 
                                     alt="{{ $user->fullname }}" 
                                     class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <div class="col">
                                <label for="avatar" class="form-label">Thay đổi Ảnh đại diện</label>
                                <input class="form-control" type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg">
                                <div class="form-text">Tối đa 2MB. Định dạng JPG, PNG.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- PHẦN THÔNG TIN --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullname" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" 
                                       value="{{ old('fullname', $user->fullname) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email', $user->email) }}" required readonly>
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

                        <hr class="my-4">

                        {{-- PHẦN ĐỔI MẬT KHẨU --}}
                        <h5 class="mb-3">Đổi Mật Khẩu (Bỏ trống nếu không đổi)</h5>
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

                        <div class="mt-4 text-end">
                            <a href="{{ route('trang_chu') }}" class="btn btn-secondary btn-lg me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary btn-lg">Lưu Thay Đổi</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatarPreview');

        if (avatarInput) {
            avatarInput.addEventListener('change', function(event) {
                const [file] = avatarInput.files;
                if (file) {
                    avatarPreview.src = URL.createObjectURL(file); // Tạo URL tạm thời cho ảnh
                }
            });
        }
    });
</script>
@endpush