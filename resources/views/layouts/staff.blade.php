<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | DreamSports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.theme.default.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/aos/aos.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/feather.css') }}">
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	{{--
	<link rel="stylesheet" href="{{ asset('css/chatbox.css') }}"> --}}

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #212529;
            --sidebar-text: #adb5bd;
            --sidebar-text-active: #ffffff;
            --sidebar-active-bg: #198754;
        }

        body {
            background-color: #f8f9fa;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 1rem;
        }

        #sidebar .nav-link {
            color: var(--sidebar-text);
            font-weight: 500;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.5rem;
        }

        #sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        #sidebar .nav-link.active {
            color: var(--sidebar-text-active);
            background-color: var(--sidebar-active-bg);
        }

        #sidebar .nav-link:hover:not(.active) {
            background-color: #198754;
            color: var(--sidebar-text-active);
        }

        #sidebar .sidebar-header {
            padding: 1rem 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
        }

        #sidebar .sidebar-header i {
            margin-right: 0.5rem;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        /* CSS cho Card (giống admin) */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>

    <div id="sidebar" class="d-flex flex-column">
        <a href="{{ route('staff.index') }}" class="sidebar-header text-decoration-none">
            <img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
            <span>DreamSports</span>
        </a>
        <hr style="border-color: #495057;">

        {{-- MENU CỦA NHÂN VIEN --}}
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('staff.index') }}"
                    class="nav-link {{ request()->routeIs('staff.index','staff.customer.search') ? 'active' : '' }}">
                    <i class="bi bi-calendar-day"></i>
                    Lịch Đặt Sân (Hôm nay)
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('staff.payment') }}"
                    class="nav-link {{ request()->routeIs('staff.payment') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    Thanh Toán & Hóa Đơn
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('staff.bookDirectly') }}"
                    class="nav-link {{ request()->routeIs('staff.bookDirectly') ? 'active' : '' }}">
                    <i class="feather-clock me-2"></i>
                    Đặt trực tiếp
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('staff.invoiceHistory') }}"
                    class="nav-link {{ request()->routeIs('staff.invoiceHistory','staff.history.search') ? 'active' : '' }}">
                    <i class="feather-file-text me-2"></i>
                    Lịch sử hóa đơn
                </a>
            </li>
        </ul>
        <hr style="border-color: #495057;">
        <ul class="nav header-navbar-rht">
            @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="userDropdown"
                        role="button" data-bs-toggle="dropdown">

                        <img src="{{ asset(auth()->user()->avatar ?? 'img/profiles/avatar-05.jpg') }}"
                            alt="{{ auth()->user()->fullname ?? 'Avatar' }}" class="rounded-circle me-2" width="32">

                        <span class="d-none d-md-inline">{{ auth()->user()->fullname }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center">

                                <img src="{{ asset(auth()->user()->avatar ?? 'img/profiles/avatar-05.jpg') }}"
                                    alt="{{ auth()->user()->fullname ?? 'Avatar' }}" class="rounded-circle me-2" width="40">

                                <div>
                                    <div class="fw-semibold">{{ auth()->user()->fullname }}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.profile', ['id' => auth()->id()]) }}">
                                <i class="fas fa-user me-2"></i> Hồ sơ
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <div class="nav-link btn btn-outline-light log-register">
                        <a href="{{ route('login') }}"><i class="feather-users"></i> Đăng Nhập</a> /
                        <a href="{{ route('register') }}">Đăng Ký</a>
                    </div>
                </li>
            @endauth
        </ul>
    </div>
    <!-- --------------------------------------------- -->
    <!-- Noi dung chinh  -->
    <main id="main-content">
        @yield('staff_content')
    </main>
    <!-- --------------------------------------------- -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>


    @stack('scripts')
</body>

</html>