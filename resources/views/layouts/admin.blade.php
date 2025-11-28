<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | DreamSports</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">


</head>

<body>

    <div id="sidebar" class="d-flex flex-column p-3">
        <a href="{{ route('admin.index') }}" class="sidebar-header text-decoration-none">
            <img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
            <span>DreamSports</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.index') }}"
                    class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    Tổng Quan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.facilities.index') }}"
                    class="nav-link {{ request()->routeIs('admin.facilities.index') ? 'active' : '' }}">
                    <i class="bi bi-building-fill"></i>
                    Quản lý Doanh Nghiệp
                </a>
            </li>
            <li>
                <a href="{{ route('admin.customers.index') }}"
                    class="nav-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    Quản lý Tài Khoản
                </a>
            </li>
            <!-- <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-bell-fill"></i>
                    Yêu cầu & Hỗ trợ
                </a>
            </li> -->
            <!-- <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-receipt-cutoff"></i>
                    Tài chính & Hóa đơn
                </a>
            </li> -->
        </ul>
        <hr>
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

    {{-- =================================== --}}
    <main id="main-content">
        @yield('index_content')
        @yield('facilities_content')
        @yield('customers_content')
        @yield('edit_content')
    </main>
    {{-- =================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>

</html>