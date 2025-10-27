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
        <a href="#" class="sidebar-header text-decoration-none">
            <img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
            <span>DreamSports</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="#" class="nav-link active" aria-current="page">
                    <i class="bi bi-grid-fill"></i>
                    Tổng Quan
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-calendar-check-fill"></i>
                    Duyệt yêu cầu
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-building-fill"></i>
                    Quản lý Doanh Nghiệp
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
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
            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-receipt-cutoff"></i>
                    Tài chính & Hóa đơn
                </a>
            </li>
        </ul>
        <hr>
        <ul class="nav header-navbar-rht">
            @auth
                <!-- Dropdown User -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="userDropdown"
                        role="button" data-bs-toggle="dropdown">
                        <img src="{{ asset('img/profiles/' . (auth()->user()->avatar ?? 'avatar-05.jpg')) }}"
                            alt="{{ auth()->user()->fullname ?? 'Avatar' }}" class="rounded-circle me-2" width="32"
                            alt="Avatar">
                        <span class="d-none d-md-inline">{{ auth()->user()->fullname }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Header -->
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('img/profiles/' . (auth()->user()->avatar ?? 'avatar-05.jpg')) }}"
                                    alt="{{ auth()->user()->fullname ?? 'Avatar' }}" class="rounded-circle me-2" width="40"
                                    alt="">
                                <div>
                                    <div class="fw-semibold">{{ auth()->user()->fullname }}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.profile', auth()->id()) }}">
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
            <!-- Doanh Thu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-success h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-success">Doanh Thu (Tháng)</div>

                                <div class="kpi-value mb-1">{{ @number_format($totalRevenueThisMonth ?? 0, 0, ',', '.')
                                    }}đ</div>

                                {{-- Logic tự động đổi màu/icon --}}
                                <div class="kpi-growth 
                                    @if(($revenueGrowthStatus ?? 'neutral') == 'up')
                                        text-success
                                    @elseif(($revenueGrowthStatus ?? 'neutral') == 'down')
                                        text-danger
                                    @else
                                        text-muted
                                    @endif
                                ">
                                    @if(($revenueGrowthStatus ?? 'neutral') == 'up')
                                        <i class="bi bi-arrow-up-short"></i>
                                    @elseif(($revenueGrowthStatus ?? 'neutral') == 'down')
                                        <i class="bi bi-arrow-down-short"></i>
                                    @else
                                        <i class="bi bi-dash-lg"></i>
                                    @endif

                                    <span>{{ $revenuePercentageChange ?? 0 }}%</span>
                                    <span class="text-muted small"> so với tháng trước</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-cash-stack kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Số sân đặt -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-primary h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-primary">Đặt Sân (Hôm nay)</div>
                                {{-- Hiển thị tổng số lượt đặt --}}
                                <div class="kpi-value mb-1">{{ $totalBookingsToday ?? 0 }}</div>
                                <div class="kpi-growth text-muted small">
                                    <span>Tổng lượt đặt trong ngày</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar2-check kpi-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Số lượng khách hàng -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card border-start border-4 border-info h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col">
                                <div class="kpi-label text-info">Khách Hàng Mới (Ngày)</div>
                                <!-- Dữ liệu mới từ Controller -->
                                <div class="kpi-value mb-1">+{{ $newCustomersCount ?? 0 }}</div>
                                <div class="kpi-growth 
                                    @if(($customerGrowthStatus ?? 'neutral') == 'up')
                                        text-success
                                    @elseif(($customerGrowthStatus ?? 'neutral') == 'down')
                                        text-danger
                                    @else
                                        text-muted
                                    @endif
                                ">
                                    @if(($customerGrowthStatus ?? 'neutral') == 'up')
                                        <i class="bi bi-arrow-up-short"></i>
                                    @elseif(($customerGrowthStatus ?? 'neutral') == 'down')
                                        <i class="bi bi-arrow-down-short"></i>
                                    @else
                                        <i class="bi bi-dash-lg"></i>
                                    @endif
                                    <span>{{ $customerPercentageChange ?? 0 }}%</span>

                                    <!-- Hôm qua -->
                                    <span class="text-muted small"> so với hôm qua</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-person-plus kpi-icon"></i>
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

                                {{-- Sửa dòng "5" này thành biến: --}}
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
        </div>

        <div class="row">
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card chart-card h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">

                        {{-- Tiêu đề biểu đồ (sẽ được JS cập nhật) --}}
                        <h6 id="revenueChartTitle" class="m-0 font-weight-bold text-primary">Tổng Quan Doanh Thu (30
                            Ngày)</h6>

                        {{-- Dropdown menu --}}
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical text-secondary"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item chart-filter" href="#" data-filter="this_week"
                                        data-title="Tổng Quan Doanh Thu (Tuần Này)">Tuần này</a></li>
                                <li><a class="dropdown-item chart-filter" href="#" data-filter="this_month"
                                        data-title="Tổng Quan Doanh Thu (Tháng Này)">Tháng này</a></li>
                                <li><a class="dropdown-item chart-filter" href="#" data-filter="this_year"
                                        data-title="Tổng Quan Doanh Thu (Năm Nay)">Năm nay</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item chart-filter" href="#" data-filter="30days"
                                        data-title="Tổng Quan Doanh Thu (30 Ngày)">30 Ngày qua</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 320px;">
                            <canvas id="revenueChart" data-url="{{ route('admin.revenueChartData') }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card chart-card h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tỉ Lệ Người Dùng</h6>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="chart-pie pt-4" style="height: 250px;">
                            <canvas id="userDonutChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="me-2">
                                <i class="bi bi-circle-fill text-primary"></i> Khách vãng lai
                            </span>
                            <span class="me-2">
                                <i class="bi bi-circle-fill text-success"></i> Khách cố định
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card table-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Các Lượt Đặt Sân Gần Đây</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">Mã Đặt</th>
                                        <th scope="col">Khách Hàng</th>
                                        <th scope="col">Tên Sân</th>
                                        <th scope="col">Thời Gian</th>
                                        <th scope="col">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>#1256</strong></td>
                                        <td>Sao Cũng Được</td>
                                        <td>Sân Ánh Dương (Sân 2)</td>
                                        <td>18:00 - 19:00</td>
                                        <td><span class="badge text-bg-success">Đã xác nhận</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#1255</strong></td>
                                        <td>Nguyễn Văn A</td>
                                        <td>Sân Tốc Độ (Sân 5)</td>
                                        <td>19:00 - 21:00</td>
                                        <td><span class="badge text-bg-warning">Chờ thanh toán</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#1254</strong></td>
                                        <td>Trần Thị B</td>
                                        <td>Sân Kỳ Hòa (Sân 1)</td>
                                        <td>17:00 - 18:00</td>
                                        <td><span class="badge text-bg-danger">Đã hủy</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#1253</strong></td>
                                        <td>Lê Văn C</td>
                                        <td>Sân Ánh Dương (Sân 3)</td>
                                        <td>20:00 - 21:00</td>
                                        <td><span class="badge text-bg-success">Đã xác nhận</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#1252</strong></td>
                                        <td>Phạm Hùng D</td>
                                        <td>Sân Kỳ Hòa (Sân 4)</td>
                                        <td>08:00 - 09:00</td>
                                        <td><span class="badge text-bg-secondary">Đã hoàn thành</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="#" class="small text-decoration-none">Xem tất cả lượt đặt sân &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yêu cầu chờ duyệt -->
            <div class="col-lg-5 mb-4">
                <div class="card feed-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Yêu Cầu Chờ Duyệt
                            @if($pendingOwners->count() > 0)
                                <span class="badge bg-danger ms-2">{{ $pendingOwners->count() }}</span>
                            @endif
                        </h6>
                    </div>
                    <div class="card-body activity-feed" style="max-height: 300px; overflow-y: auto;">

                        @forelse ($pendingOwners as $owner)
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex">
                                        <span class="feed-icon bg-warning"><i class="bi bi-building-add"></i></span>
                                        <div class="ms-3">
                                            <strong>{{ $owner->fullname ?? 'Chưa có tên' }}</strong>
                                            <div><small>Yêu cầu đăng ký: {{ $owner->email }}</small></div>
                                            <div class="feed-time">{{ $owner->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    {{-- Nút này sẽ mở Modal (popup) chi tiết --}}
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#ownerDetailsModal-{{ $owner->user_id }}">
                                        Xem chi tiết
                                    </button>
                                </li>
                            </ul>
                        @empty
                            {{-- Nếu không có ai chờ duyệt --}}
                            <div class="text-center text-muted p-3">
                                <i class="bi bi-check-circle fs-3"></i>
                                <p class="mt-2">Không có yêu cầu nào chờ duyệt.</p>
                            </div>
                        @endforelse

                    </div>
                    <div class="card-footer text-center">
                        <a href="#" class="small text-decoration-none">Xem tất cả lịch sử duyệt &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
                {{-- =============================================== --}}
{{-- MODAL (POPUP) HIỂN THỊ CHI TIẾT DOANH NGHIỆP --}}

@foreach ($pendingOwners as $owner)
<div class="modal fade" id="ownerDetailsModal-{{ $owner->user_id }}" tabindex="-1" aria-labelledby="modalTitle-{{ $owner->user_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle-{{ $owner->user_id }}">Duyệt Yêu Cầu: {{ $owner->fullname }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- THÔNG TIN TÀI KHOẢN--}}
                    <div class="col-md-6">
                        <h6><i class="bi bi-person-badge"></i> Thông tin Tài khoản (Chủ sân)</h6>
                        <dl class="row">
                            <dt class="col-sm-4">Họ và tên</dt>
                            <dd class="col-sm-8">{{ $owner->fullname }}</dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ $owner->email }}</dd>
                            
                            <dt class="col-sm-4">Số điện thoại</dt>
                            <dd class="col-sm-8">{{ $owner->phone ?? '(Chưa cập nhật)' }}</dd>

                            <dt class="col-sm-4">Số CCCD</dt>
                            <dd class="col-sm-8">{{ $owner->CCCD ?? '(Chưa cập nhật)' }}</dd>
                            
                            <dt class="col-sm-4">Địa chỉ</dt>
                            <dd class="col-sm-8">{{ $owner->address ?? '(Chưa cập nhật)' }}</dd>
                        </dl>
                    </div>

                    {{-- THÔNG TIN SÂN--}}
                    <div class="col-md-6 border-start">
                        <h6><i class="bi bi-building"></i> Thông tin Sân (Giả định)</h6>
                        <p class="text-muted">(Lưu ý: Bạn cần thiết kế CSDL để lưu các thông tin này khi họ đăng ký)</p>
                        
                        <dl class="row">
                            <dt class="col-sm-5">Tên cơ sở kinh doanh</dt>
                            <dd class="col-sm-7">(Sân Ánh Dương)</dd>

                            <dt class="col-sm-5">Địa chỉ sân</dt>
                            <dd class="col-sm-7">(123 Nguyễn Văn Linh)</dd>
                            
                            <dt class="col-sm-5">Giấy phép KD</dt>
                            <dd class="col-sm-7">
                                {{-- Giả sử bạn lưu tên file --}}
                                <a href="#" target="_blank">Xem file đính kèm</a> 
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            {{-- NÚT DUYỆT / TỪ CHỐI (Use Case) --}}
            <div class="modal-footer">
                <form action="{{ route('admin.owner.deny', $owner->user_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle"></i> Từ chối
                    </button>
                </form>

                <form action="{{ route('admin.owner.approve', $owner->user_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Phê duyệt
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach                            
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    {{-- Thư viện Chart.js PHẢI được tải trước --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    {{-- File JS của bạn tải SAU --}}
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
</body>
</html>