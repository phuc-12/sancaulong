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
                            @if(session('message'))
                                <div class="alert alert-success">
                                    {{ session('message') }}
                                </div>
                            @endif
                            <form action="{{ route('postRegister') }}" method="POST" novalidate>
                                @csrf
                                <div class="mb-3">
                                <label class="form-label">Bạn là:</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="role_type" id="role_customer" 
                                               value="customer" {{ old('role_type', 'customer') == 'customer' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_customer">
                                            Khách hàng
                                        </label>
                                    </div>
                                    <div class="form-check me-1">
                                        <input class="form-check-input" type="radio" name="role_type" id="role_business" 
                                               value="business" {{ old('role_type') == 'business' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_business">
                                            Doanh nghiệp cầu lông
                                        </label>
                                    </div>
                                </div>
                                @error('role_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Tên đầy đủ</label>
                                    <input type="text" class="form-control" name="fullname" value="{{ old('fullname') }}"
                                        placeholder="Nhập tên đầy đủ" required>
                                    @error('fullname')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Nhập email của bạn" value="{{ old('email') }}"
                                        required>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Nhập mật khẩu" required>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="re-password" class="form-label">Nhập lại mật khẩu</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Xác nhận mật khẩu">
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
</body>

</html>
@endsection