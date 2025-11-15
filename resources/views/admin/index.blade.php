<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- Favicon -->
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
                    Quản lý Khách Hàng
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

    <main id="main-content">
        <h1 class="h3 mb-4" style="color: black;">Tổng Quan Dashboard</h1>
        <div class="row">

            <!-- Tổng doanh thu toàn hệ thống -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-success h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-success">Doanh Thu Hệ Thống</div>
                                <div class="kpi-value mb-1">{{ number_format($totalSystemRevenue, 0, ',', '.') }}đ</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-cash-coin kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng số chủ sân -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-warning h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-warning">Doanh Nghiệp</div>

                                <div class="kpi-value mb-1">{{ $totalOwners ?? 0 }}</div>

                                <div class="kpi-growth text-muted small">
                                    <span>Tổng số chủ sân</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-building kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng số sân con -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-secondary h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-secondary">Tổng Số Sân Con</div>
                                <div class="kpi-value mb-1">{{ $totalCourts }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-grid-3x3-gap-fill kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng số người dùng -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-info h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-info">Tổng Người Dùng</div>
                                <div class="kpi-value mb-1">{{ $totalUsers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people-fill kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tăng trưởng chủ sân theo tháng -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-warning h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-warning">Tăng Trưởng Chủ Sân</div>
                                <div class="kpi-value mb-1">{{ number_format($ownerGrowth, 1) }}%</div>
                                <div class="small 
                                    @if($ownerGrowthStatus == 'up') text-success
                                    @elseif($ownerGrowthStatus == 'down') text-danger
                                    @else text-muted @endif
                                ">
                                    @if($ownerGrowthStatus == 'up') <i class="bi bi-arrow-up-short"></i>
                                    @elseif($ownerGrowthStatus == 'down') <i class="bi bi-arrow-down-short"></i>
                                    @else <i class="bi bi-dash-lg"></i> @endif
                                    So với tháng trước
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-building-add kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tăng trưởng người dùng theo tháng -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-primary h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-primary">Tăng Trưởng Người Dùng</div>
                                <div class="kpi-value mb-1">{{ number_format($userGrowth, 1) }}%</div>
                                <div class="small 
                                    @if($userGrowthStatus == 'up') text-success
                                    @elseif($userGrowthStatus == 'down') text-danger
                                    @else text-muted @endif
                                ">
                                    @if($userGrowthStatus == 'up') <i class="bi bi-arrow-up-short"></i>
                                    @elseif($userGrowthStatus == 'down') <i class="bi bi-arrow-down-short"></i>
                                    @else <i class="bi bi-dash-lg"></i> @endif
                                    So với tháng trước
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-person-add kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header"><strong>Tăng trưởng chủ sân theo tháng</strong></div>
                    <div class="card-body">
                        <canvas id="ownersChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header"><strong>Tăng trưởng người dùng theo tháng</strong></div>
                    <div class="card-body">
                        <canvas id="usersChart" height="120"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    {{-- Thư viện Chart.js PHẢI được tải trước --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
    <script>
        let ownersMonthly = @json($ownersMonthly);
        let usersMonthly = @json($usersMonthly);

        let months = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        let ownersData = months.map(m => ownersMonthly[m] ?? 0);
        let usersData = months.map(m => usersMonthly[m] ?? 0);

        const ctx1 = document.getElementById('ownersChart');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Chủ sân mới',
                    data: ownersData,
                    borderWidth: 2,
                    borderColor: '#f39c12',
                    tension: 0.3
                }]
            }
        });

        const ctx2 = document.getElementById('usersChart');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Người dùng mới',
                    data: usersData,
                    borderWidth: 2,
                    borderColor: '#3498db',
                    tension: 0.3
                }]
            }
        });

    </script>
</body>

</html>