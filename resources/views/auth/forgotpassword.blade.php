@extends('layouts.header')
@section('login')
<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quên Mật Khẩu - DreamSports</title>

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
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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

        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* ====== TITLE ====== */
        .login-title {
            font-size: 30px;
            font-weight: 800;
            text-align: center;
            color: #064A43;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
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
            border: 1px solid rgba(0,0,0,0.15);
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

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            padding: 12px;
            font-size: 17px;
            border-radius: 12px;
            transition: all 0.25s;
            color: #fff;
            font-weight: 600;
            width: 100%;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-3px);
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

        /* ====== STEPS ====== */
        .step {
            display: none;
        }

        .step.active {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ====== CODE INPUTS ====== */
        .code-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .code-input {
            width: 50px;
            height: 55px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid rgba(0,0,0,0.15);
            border-radius: 12px;
        }

        .code-input:focus {
            border-color: #0BAE79;
            box-shadow: 0 0 10px rgba(11, 174, 121, 0.4);
            outline: none;
        }

        .resend-link {
            color: #0BAE79;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .resend-link.disabled {
            color: #999;
            cursor: not-allowed;
        }
    </style>
</header>

<div class="body">
    <div class="login-card">
        <h3 class="login-title">Quên Mật Khẩu</h3>
        
        <div id="alert-container"></div>

        <!-- BƯỚC 1: NHẬP EMAIL -->
        <div id="step1" class="step active">
            <p class="subtitle">Nhập email của bạn để nhận mã xác nhận</p>
            <form id="emailForm">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope-fill"></i>
                        </span>
                        <input type="email" class="form-control" id="email"
                            placeholder="Nhập email của bạn..." required>
                    </div>
                </div>

                <button type="submit" class="btn-login mt-3">
                    <span id="btnEmailText">Gửi mã xác nhận</span>
                    <span id="btnEmailSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Đang gửi...
                    </span>
                </button>

                <p class="text-center mt-4 text-muted">
                    <a href="{{ route('login') }}" class="reg-link">
                        <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
                    </a>
                </p>
            </form>
        </div>

        <!-- BƯỚC 2: NHẬP MÃ XÁC NHẬN -->
        <div id="step2" class="step">
            <p class="subtitle">Nhập mã xác nhận 6 số đã được gửi đến email của bạn</p>
            <form id="codeForm">
                <div class="code-inputs">
                    <input type="text" class="code-input" maxlength="1" data-index="0">
                    <input type="text" class="code-input" maxlength="1" data-index="1">
                    <input type="text" class="code-input" maxlength="1" data-index="2">
                    <input type="text" class="code-input" maxlength="1" data-index="3">
                    <input type="text" class="code-input" maxlength="1" data-index="4">
                    <input type="text" class="code-input" maxlength="1" data-index="5">
                </div>

                <div class="text-center mb-3">
                    <small class="text-muted">Chưa nhận được mã? </small>
                    <a class="resend-link" id="resendLink">
                        Gửi lại (<span id="countdown">60</span>s)
                    </a>
                </div>

                <button type="submit" class="btn-login mt-3">
                    <span id="btnCodeText">Xác nhận</span>
                    <span id="btnCodeSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Đang xác nhận...
                    </span>
                </button>

                <button type="button" class="btn-secondary-custom mt-2" onclick="goToStep(1)">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </button>
            </form>
        </div>

        <!-- BƯỚC 3: ĐẶT LẠI MẬT KHẨU -->
        <div id="step3" class="step">
            <p class="subtitle">Nhập mật khẩu mới của bạn</p>
            <form id="passwordForm">
                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control" id="password"
                            placeholder="Nhập mật khẩu mới..." required minlength="8">
                    </div>
                    <small class="text-muted">Tối thiểu 8 ký tự</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control" id="password_confirmation"
                            placeholder="Xác nhận mật khẩu..." required>
                    </div>
                </div>

                <button type="submit" class="btn-login mt-3">
                    <span id="btnPasswordText">Đặt lại mật khẩu</span>
                    <span id="btnPasswordSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Đang xử lý...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let userEmail = '';
    let verificationCode = '';
    let countdownTimer = null;

    // Hàm chuyển bước
    function goToStep(step) {
        document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
        document.getElementById('step' + step).classList.add('active');
    }

    // Hàm hiển thị thông báo
    function showAlert(message, type = 'danger') {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    // BƯỚC 1: Gửi mã xác nhận
    document.getElementById('emailForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const btnText = document.getElementById('btnEmailText');
        const btnSpinner = document.getElementById('btnEmailSpinner');
        
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("password.send-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: email })
            });

            const data = await response.json();

            if (data.success) {
                userEmail = email;
                showAlert(data.message, 'success');
                goToStep(2);
                startCountdown();
            } else {
                showAlert(data.message, 'danger');
            }
        } catch (error) {
            showAlert('Đã có lỗi xảy ra. Vui lòng thử lại!', 'danger');
        } finally {
            btnText.classList.remove('d-none');
            btnSpinner.classList.add('d-none');
        }
    });

    // Xử lý nhập mã OTP
    const codeInputs = document.querySelectorAll('.code-input');
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            if (this.value.length === 1 && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                codeInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').slice(0, 6);
            pastedData.split('').forEach((char, i) => {
                if (codeInputs[i]) {
                    codeInputs[i].value = char;
                }
            });
            if (pastedData.length === 6) {
                codeInputs[5].focus();
            }
        });
    });

    // BƯỚC 2: Xác nhận mã
    document.getElementById('codeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        verificationCode = Array.from(codeInputs).map(input => input.value).join('');
        
        if (verificationCode.length !== 6) {
            showAlert('Vui lòng nhập đủ 6 số', 'danger');
            return;
        }

        const btnText = document.getElementById('btnCodeText');
        const btnSpinner = document.getElementById('btnCodeSpinner');
        
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("password.verify-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    email: userEmail,
                    code: verificationCode 
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                goToStep(3);
                clearInterval(countdownTimer);
            } else {
                showAlert(data.message, 'danger');
                codeInputs.forEach(input => input.value = '');
                codeInputs[0].focus();
            }
        } catch (error) {
            showAlert('Đã có lỗi xảy ra. Vui lòng thử lại!', 'danger');
        } finally {
            btnText.classList.remove('d-none');
            btnSpinner.classList.add('d-none');
        }
    });

    // BƯỚC 3: Đặt lại mật khẩu
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        if (password !== passwordConfirmation) {
            showAlert('Mật khẩu xác nhận không khớp!', 'danger');
            return;
        }

        const btnText = document.getElementById('btnPasswordText');
        const btnSpinner = document.getElementById('btnPasswordSpinner');
        
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("password.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    email: userEmail,
                    code: verificationCode,
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message + ' Đang chuyển hướng...', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
            } else {
                showAlert(data.message, 'danger');
            }
        } catch (error) {
            showAlert('Đã có lỗi xảy ra. Vui lòng thử lại!', 'danger');
        } finally {
            btnText.classList.remove('d-none');
            btnSpinner.classList.add('d-none');
        }
    });

    // Countdown và gửi lại mã
    function startCountdown() {
        let seconds = 60;
        const resendLink = document.getElementById('resendLink');
        const countdownEl = document.getElementById('countdown');
        
        resendLink.classList.add('disabled');
        
        countdownTimer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdownTimer);
                resendLink.classList.remove('disabled');
                resendLink.innerHTML = 'Gửi lại mã';
            }
        }, 1000);
    }

    // Gửi lại mã
    document.getElementById('resendLink').addEventListener('click', async function(e) {
        e.preventDefault();
        
        if (this.classList.contains('disabled')) return;
        
        try {
            const response = await fetch('{{ route("password.send-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: userEmail })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Mã xác nhận mới đã được gửi!', 'success');
                codeInputs.forEach(input => input.value = '');
                codeInputs[0].focus();
                startCountdown();
            } else {
                showAlert(data.message, 'danger');
            }
        } catch (error) {
            showAlert('Đã có lỗi xảy ra. Vui lòng thử lại!', 'danger');
        }
    });
</script>
@endsection