@extends('layouts.header')
@section('register')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Đăng Ký</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card register-card">
                        <div class="card-body p-3 p-md-6">
                            <h3 class="card-title text-center mb-4">Đăng Ký</h3>
                            
                            {{-- Thông báo thành công --}}
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Thông báo lỗi chung --}}
                            @if($errors->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ $errors->first('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('postRegister') }}" method="POST" novalidate>
                                @csrf
                                
                                {{-- Chọn vai trò --}}
                                <div class="mb-3">
                                    <label class="form-label">Bạn là:</label>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="role_type" 
                                                   id="role_customer" value="customer" 
                                                   {{ old('role_type', 'customer') == 'customer' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_customer">
                                                Khách hàng
                                            </label>
                                        </div>
                                        <div class="form-check me-1">
                                            <input class="form-check-input" type="radio" name="role_type" 
                                                   id="role_business" value="business" 
                                                   {{ old('role_type') == 'business' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_business">
                                                Doanh nghiệp cầu lông
                                            </label>
                                        </div>
                                    </div>
                                    @error('role_type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tên đầy đủ --}}
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Tên đầy đủ</label>
                                    <input type="text" class="form-control @error('fullname') is-invalid @enderror" 
                                           name="fullname" value="{{ old('fullname') }}"
                                           placeholder="Nhập tên đầy đủ" required>
                                    @error('fullname')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" placeholder="Nhập email của bạn" value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Số điện thoại --}}
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}"
                                           placeholder="09xxx" maxlength="11" required>
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mật khẩu --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           name="password" placeholder="Nhập mật khẩu" required>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Nhập lại mật khẩu --}}
                                <div class="mb-3">
                                    <label for="re-password" class="form-label">Nhập lại mật khẩu</label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                           name="password_confirmation" placeholder="Xác nhận mật khẩu">
                                    @error('password_confirmation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Đăng ký</button>
                                </div>
                            </form>
                            <p class="text-center text-muted mt-4 mb-0">
                                Đã có tài khoản? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Đăng nhập</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        // Chỉ cho phép nhập số vào field phone
        document.getElementById('phone')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Chỉ giữ số
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            e.target.value = value;
        });

        // Tự động dismiss alert sau 5 giây
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
@endsection