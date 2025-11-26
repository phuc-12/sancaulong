@extends('layouts.header')
@section('register')

<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - DreamSports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ====== BACKGROUND ANIMATED ====== */
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(120deg, #0BAE79, #064A43, #0a8b68);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ====== REGISTER CARD ====== */
        .register-card {
            width: 100%;
            max-width: 500px;
            padding: 30px 40px;
            margin-top: 70px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border-left: 6px solid #0BAE79;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.7s ease-out;
        }

        /* .register-card::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(11,174,121,0.2), transparent 70%);
            top: -50%;
            left: -50%;
            animation: rotateBG 12s linear infinite;
        } */

        @keyframes rotateBG {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }

        /* ====== TITLE ====== */
        .register-card h3 {
            font-size: 35px;
            font-weight: 700;
            text-align: center;
            color: #064A43;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        /* ====== INPUT ====== */
        .form-control {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.15);
            transition: all 0.3s;
            position: relative;
            z-index: 1;
        }

        .form-control:focus {
            border-color: #0BAE79;
            box-shadow: 0 0 10px rgba(11,174,121,0.4);
            outline: none;
        }

        .form-check-input:checked {
            background-color: #0BAE79;
            border-color: #0BAE79;
        }

        /* ====== BUTTON ====== */
        .btn-register {
            background: linear-gradient(135deg, #0BAE79, #064A43);
            color: #fff;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px;
            font-size: 17px;
            border: none;
            width: 100%;
            transition: all 0.25s;
            box-shadow: 0 8px 18px rgba(0,174,121,0.3);
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #064A43, #0BAE79);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,174,121,0.4);
        }

        .text-danger {
            font-size: 0.875rem;
        }

        /* ====== ALERT ====== */
        .alert {
            border-radius: 12px;
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <h3>Đăng Ký DreamSports</h3>

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
                        <label class="form-check-label" for="role_customer">Khách hàng</label>
                    </div>
                    <div class="form-check me-1">
                        <input class="form-check-input" type="radio" name="role_type" 
                               id="role_business" value="business" 
                               {{ old('role_type') == 'business' ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_business">Doanh nghiệp cầu lông</label>
                    </div>
                </div>
                @error('role_type')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tên đầy đủ --}}
            <div class="mb-3">
                <label class="form-label">Tên đầy đủ</label>
                <input type="text" class="form-control @error('fullname') is-invalid @enderror"
                       name="fullname" value="{{ old('fullname') }}" placeholder="Nhập tên đầy đủ" required>
                @error('fullname')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" placeholder="Nhập email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Số điện thoại --}}
            <div class="mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                       id="phone" name="phone" value="{{ old('phone') }}"
                       placeholder="09xxx" maxlength="11" required>
                @error('phone')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Mật khẩu --}}
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       name="password" placeholder="Nhập mật khẩu" required>
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nhập lại mật khẩu --}}
            <div class="mb-3">
                <label class="form-label">Nhập lại mật khẩu</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                       name="password_confirmation" placeholder="Xác nhận mật khẩu">
                @error('password_confirmation')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn-register">Đăng ký</button>
            </div>
        </form>

        <p class="text-center text-muted mt-4 mb-0">
            Đã có tài khoản? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Đăng nhập</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Chỉ cho phép nhập số vào field phone
        document.getElementById('phone')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
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

@endsection
