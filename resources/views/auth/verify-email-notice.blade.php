@extends('layouts.header')
@section('register')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email - DreamSports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .verify-page {
            min-height: calc(100vh - 120px);
            width: 100%;
            font-family: "Poppins", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
            background: linear-gradient(120deg, #0BAE79, #064A43, #0a8b68);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .verify-card {
            width: 100%;
            max-width: 550px;
            padding: 40px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border-left: 6px solid #0BAE79;
            text-align: center;
            animation: fadeInUp 0.7s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }

        .verify-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #0BAE79, #064A43);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .verify-card h2 {
            font-size: 28px;
            font-weight: 800;
            color: #064A43;
            margin-bottom: 20px;
        }

        .verify-card p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .email-highlight {
            font-weight: 600;
            color: #0BAE79;
            word-break: break-all;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #0BAE79, #064A43);
            color: #fff;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            transition: all 0.25s;
            box-shadow: 0 8px 18px rgba(0,174,121,0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #064A43, #0BAE79);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,174,121,0.4);
            color: #fff;
        }

        .btn-secondary-custom {
            background: transparent;
            color: #0BAE79;
            font-weight: 600;
            border: 2px solid #0BAE79;
            border-radius: 12px;
            padding: 12px 30px;
            font-size: 16px;
            transition: all 0.25s;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .btn-secondary-custom:hover {
            background: #0BAE79;
            color: #fff;
            transform: translateY(-3px);
        }

        .alert-info {
            background: #e6f7ff;
            border: 1px solid #91d5ff;
            color: #0050b3;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .resend-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>

<div class="verify-page">
    <div class="verify-card">
        <div class="verify-icon">
            <i class="bi bi-envelope-check"></i>
        </div>

        <h2>Vui lòng xác thực email của bạn</h2>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Chúng tôi đã gửi email xác thực đến địa chỉ:
            <div class="email-highlight mt-2">{{ session('email') ?? 'email của bạn' }}</div>
        </div>

        <p>
            Vui lòng kiểm tra hộp thư đến (và cả thư mục spam) của bạn. 
            Nhấp vào liên kết trong email để kích hoạt tài khoản.
        </p>

        <div class="resend-section">
            <p class="text-muted mb-3">Chưa nhận được email?</p>
            <form id="resendForm">
                @csrf
                <input type="hidden" name="email" value="{{ session('email') }}" id="resendEmail">
                <button type="submit" class="btn-secondary-custom" id="resendBtn">
                    <i class="bi bi-arrow-clockwise me-2"></i>Gửi lại email
                </button>
            </form>
            <div id="resendMessage" class="mt-3"></div>
        </div>

        <div class="mt-4">
            <a href="{{ route('login') }}" class="btn-primary-custom">
                <i class="bi bi-box-arrow-in-right me-2"></i>Đi tới trang đăng nhập
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('resendForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('resendBtn');
        const messageDiv = document.getElementById('resendMessage');
        const email = document.getElementById('resendEmail').value;

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang gửi...';

        try {
            const response = await fetch('{{ route("verification.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || 
                                  document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ email: email })
            });

            const data = await response.json();

            if (data.success) {
                messageDiv.innerHTML = '<div class="alert alert-success">Email đã được gửi lại thành công!</div>';
            } else {
                messageDiv.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Có lỗi xảy ra') + '</div>';
            }
        } catch (error) {
            messageDiv.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi gửi email. Vui lòng thử lại sau.</div>';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Gửi lại email';
        }
    });
</script>

@endsection

