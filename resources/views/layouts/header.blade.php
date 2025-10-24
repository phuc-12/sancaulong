@yield('login')
@yield('register')
<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamSports</title>
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/aos/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('css/chatbox.css') }}"> -->

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
</head>

<body>

    <div class="main-wrapper">
        <!-- Header -->
        <!-- Header -->
        <header class="header header-trans" id="site-header">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg header-nav">
                    <!-- Logo và Mobile menu -->
                    <div class="navbar-header">
                        <a id="mobile_btn" href="javascript:void(0);" class="text-white">
                            <span class="bar-icon"><span></span><span></span><span></span></span>
                        </a>
                        <a href="{{ route('trang_chu') }}" class="navbar-brand logo">
                            <img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
                        </a>
                    </div>
                    <!-- Menu -->
                </nav>
            </div>
        </header>
        <!-- /Header -->

        <!-- CSS Fix Dropdown -->
        <style>
            .header-trans {
                background: rgba(40, 40, 40, 0.92) !important;
                backdrop-filter: blur(8px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                transition: background 0.3s ease;
            }

            .header-trans.scrolled {
                background: rgba(30, 30, 30, 0.98) !important;
            }

            /* Không ép toàn bộ * thành màu trắng nữa */
            .header-trans .navbar-nav .nav-link,
            .header-trans .navbar-brand,
            .header-trans .main-nav>li>a {
                color: #ffffff !important;
            }

            /* Dropdown sửa màu */
            .dropdown-menu {
                background-color: #ffffff;
                color: #333 !important;
                border-radius: 10px;
                min-width: 220px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                z-index: 1055;
            }

            .dropdown-menu .dropdown-item {
                color: #333 !important;
                font-size: 0.95rem;
                padding: 0.5rem 1rem;
            }

            .dropdown-menu .dropdown-item:hover {
                background-color: #f1f1f1;
            }

            .dropdown-item.text-danger:hover {
                background: rgba(220, 53, 69, 0.1);
            }

            .register-container {
                max-width: 500px;
                margin: 50px auto;
                padding: 30px;
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
    </div> <!-- /.main-wrapper -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        // CHẠY SAU KHI DOM LOAD XONG
        document.addEventListener("DOMContentLoaded", function () {
            feather.replace();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const header = document.getElementById('site-header');
            let ticking = false;

            function updateHeader() {
                header.classList.toggle('scrolled', window.scrollY > 30);
                ticking = false;
            }

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(updateHeader);
                    ticking = true;
                }
            });
        });
    </script>