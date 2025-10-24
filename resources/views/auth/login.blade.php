@extends('layouts.header')
@section('login')
<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Đăng Nhập</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 450px;
            width: 100%;

            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
</header>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card login-card">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="card-title text-center mb-4">Đăng Nhập</h3>
                            @if(session('message'))
                                <div class="alert alert-success">
                                    {{ session('message') }}
                                </div>
                            @endif
                            <form action="{{ route('postLogin') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="Nhập email của bạn" required>
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
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
                                </div>
                            </form>
                            <p class="text-center text-muted mt-4 mb-0">
                                Chưa có tài khoản? <a href="{{ route('register') }}"
                                    class="fw-bold text-decoration-none">Đăng ký</a>
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
@endsection