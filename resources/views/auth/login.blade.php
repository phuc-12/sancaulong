@extends('layouts.header')
@section('login')
    <header>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đăng Nhập - DreamSports</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

        <style>
            /* ====== BACKGROUND ANIMATED ====== */
            .body {
                min-height: 100vh;
                margin: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: "Poppins", sans-serif;
                background: linear-gradient(120deg, #0BAE79, #064A43, #0a8b68);
                background-size: 400% 400%;
                animation: gradientBG 15s ease infinite;
            }

            @keyframes gradientBG {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            /* ====== LOGIN CARD ====== */
            .login-card {
                width: 100%;
                max-width: 450px;
                padding: 40px 35px;
                border-radius: 25px;
                background: rgba(255, 255, 255, 0.95);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                border-left: 6px solid #0BAE79;
                animation: fadeInUp 0.7s ease-out;
                position: relative;
                overflow: hidden;
            }

            /* .login-card::before {
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
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* ====== TITLE ====== */
            .login-title {
                font-family: "Poppins", sans-serif;
                font-size: 35px;
                font-weight: 700;
                text-align: center;
                color: #064A43;
                margin-bottom: 25px;
                position: relative;
                z-index: 1;
            }

            /* ====== INPUTS ====== */
            .input-group-text {
                background: #0BAE79;
                color: #fff;
                border: none;
                font-size: 18px;
            }

            .form-control {
                padding: 14px;
                border-radius: 12px;
                border: 1px solid rgba(0, 0, 0, 0.15);
                transition: all 0.3s;
                position: relative;
                z-index: 1;
            }

            .form-control:focus {
                border-color: #0BAE79;
                box-shadow: 0 0 10px rgba(11, 174, 121, 0.4);
                outline: none;
            }

            /* ====== BUTTON ====== */
            .btn-login {
                background: linear-gradient(135deg, #0BAE79, #064A43);
                border: none;
                padding: 12px;
                font-size: 17px;
                border-radius: 12px;
                transition: all 0.25s;
                color: #fff;
                font-weight: 600;
                width: 100%;
                box-shadow: 0 8px 18px rgba(0, 174, 121, 0.3);
            }

            .btn-login:hover {
                background: linear-gradient(135deg, #064A43, #0BAE79);
                transform: translateY(-3px);
                box-shadow: 0 12px 24px rgba(0, 174, 121, 0.4);
            }

            .reg-link {
                color: #064A43;
                text-decoration: none;
                font-weight: 600;
            }

            .reg-link:hover {
                text-decoration: underline;
                color: #0BAE79;
            }

            /* ====== ALERT ====== */
            .alert {
                border-radius: 12px;
                position: relative;
                z-index: 1;
            }
        </style>
    </header>

    <div class="body">
        <div class="login-card">
            <h3 class="login-title">Đăng Nhập DreamSports</h3>
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('postLogin') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope-fill"></i>
                        </span>
                        <input type="email" class="form-control" name="email" placeholder="Nhập email..." required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control" name="password" placeholder="Nhập mật khẩu..." required>
                    </div>
                </div>

                <button type="submit" class="btn-login mt-3">
                    Đăng nhập
                </button>
                <p class="text-center mt-4 text-muted">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="reg-link">Đăng ký ngay</a>
                    <br>
                    <a href="{{ route('forgot-password') }}" class="reg-link" style="font-size: 14px;">
                        Quên mật khẩu?
                    </a>
                </p>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection