@extends('layouts.owner')

@section('report_content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --orange: #ff8c42;
            --blue: #4a90e2;
            --yellow: #ffd93d;
            --purple: #9b59b6;
            --green: #2ecc71;
            --pink: #e91e63;
        }

        body {
            background: #f5f7fa;
        }

        .dashboard-container {
            min-height: 100vh;
            padding: 24px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .page-subtitle {
            color: #7f8c8d;
            font-size: 1rem;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .filter-section .row {
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        #customDateRange {
            display: none;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .kpi-card {
            border-radius: 16px;
            border: 2px solid transparent;
            padding: 24px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            height: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .kpi-card.orange {
            background: #fff3e6;
            border-color: #ffb366;
        }

        .kpi-card.blue {
            background: #e6f2ff;
            border-color: #80c1ff;
        }

        .kpi-card.yellow {
            background: #fffbea;
            border-color: #ffe680;
        }

        .kpi-card.purple {
            background: #f3e6ff;
            border-color: #c99dff;
        }

        .kpi-card.green {
            background: #e6fff2;
            border-color: #80e6b3;
        }

        .kpi-card.pink {
            background: #ffe6f2;
            border-color: #ff99cc;
        }

        .kpi-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-orange {
            background: #ffe0cc;
            color: #ff6600;
        }

        .icon-blue {
            background: #cce6ff;
            color: #0066cc;
        }

        .icon-yellow {
            background: #fff5cc;
            color: #cc9900;
        }

        .icon-purple {
            background: #e6ccff;
            color: #6600cc;
        }

        .icon-green {
            background: #ccffe6;
            color: #00cc66;
        }

        .icon-pink {
            background: #ffcce6;
            color: #cc0066;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 12px 0 8px 0;
        }

        .kpi-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-period {
            font-size: 0.75rem;
            color: #95a5a6;
        }

        .change-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .change-positive {
            background: #d4edda;
            color: #155724;
        }

        .change-negative {
            background: #f8d7da;
            color: #721c24;
        }

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: flex-start;
        }

        .chart-card canvas {
            width: 100% !important;
            height: 500px !important;
        }


        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 16px;
        }

        .table-hover tbody tr:hover {
            background: #f8f9fa;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        #courtFilter+button {
            border-left: 0;
        }

        #courtFilter+button:hover {
            background-color: #f8f9fa;
        }

        #courtFilter+button i {
            transition: transform 0.3s ease;
        }

        #courtFilter+button:active i {
            transform: rotate(180deg);
        }

        .loading {
            animation: pulse 1.5s ease-in-out infinite;
        }

        @media (max-width: 992px) {

            .chart-card,
            .kpi-card {
                min-height: auto !important;
            }
        }
    </style>

    <div class="container-fluid dashboard-container">
        <div class="page-header">
            <h1 class="page-title">Dashboard Báo Cáo</h1>
            <p class="page-subtitle">Tổng quan hoạt động kinh doanh sân cầu lông</p>
        </div>
        {{-- ==== THÔNG BÁO SESSION ==== --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ==== THÔNG BÁO TRẠNG THÁI CƠ SỞ ==== --}}
        @if(!empty($facilityStatusMessage))
            <div class="alert alert-{{ $facilityStatusType }} alert-dismissible fade show" role="alert">
                {{ $facilityStatusMessage }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ==== FILTER, KPI, CHARTS ==== --}}
        <div class="filter-section">
            <div class="row align-items: center">
                <div class="col-auto d-flex align-items-center">
                    <i class="fas fa-filter text-muted"></i><strong class="ms-2">Bộ lọc:</strong>
                </div>
                <div class="col-auto">
                    <select class="form-select" id="dateRange">
                        <option value="today">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month" selected>Tháng này</option>
                        <option value="quarter">Quý này</option>
                        <option value="year">Năm nay</option>
                        <option value="custom">Tùy chỉnh</option>
                    </select>
                </div>
                <div class="col-auto" id="customDateRange">
                    <input type="date" class="form-control" id="startDate">
                    <span>đến</span>
                    <input type="date" class="form-control" id="endDate">
                    <button class="btn btn-primary" id="applyCustomDate">Áp dụng</button>
                </div>

                <div class="col-auto">
                    <div class="input-group" style="min-width: 200px;">
                        <select class="form-select" id="courtFilter">
                            <option value="all">Tất cả sân con</option>
                            @if(isset($courts) && $courts->count() > 0)
                                @foreach($courts as $court)
                                    <option value="{{ $court->court_id }}">{{ $court->court_name }}</option>
                                @endforeach
                            @else
                                <option disabled>Không có sân con nào</option>
                            @endif
                        </select>
                        <button class="btn btn-outline-secondary" type="button" onclick="loadCourts()"
                            title="Làm mới danh sách sân">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <input type="hidden" id="ownerFacilityId" value="{{ $facility_id ?? '' }}">

                <div class="col-auto ms-auto d-flex gap-2">
                    <button class="btn btn-success" id="exportExcel">
                        <i class="fas fa-file-excel me-2"></i>Xuất Excel
                    </button>
                    <button class="btn btn-danger" id="exportPdf"><i class="fas fa-file-pdf me-2"></i>Xuất PDF</button>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            @php
                $kpis = [
                    ['label' => 'TỔNG DOANH THU', 'id' => 'totalRevenue', 'badge' => 'revenueBadge', 'change' => 'revenueChange', 'icon' => 'fas fa-dollar-sign', 'color' => 'orange', 'period' => 'Trong kỳ'],
                    ['label' => 'TỔNG ĐẶT SÂN', 'id' => 'totalBookings', 'badge' => 'bookingBadge', 'change' => 'bookingChange', 'icon' => 'fas fa-calendar-check', 'color' => 'blue', 'period' => 'Bookings'],
                    ['label' => 'CÔNG SUẤT', 'id' => 'utilization', 'badge' => 'utilizationBadge', 'change' => 'utilizationChange', 'icon' => 'fas fa-chart-line', 'color' => 'yellow', 'period' => 'Trung bình'],
                    ['label' => 'KHÁCH HÀNG', 'id' => 'totalCustomers', 'badge' => 'customerBadge', 'change' => 'customerChange', 'icon' => 'fas fa-users', 'color' => 'purple', 'period' => 'Khách duy nhất'],
                    ['label' => 'GIÁ TRUNG BÌNH', 'id' => 'avgPrice', 'badge' => 'priceBadge', 'change' => 'priceChange', 'icon' => 'fas fa-money-bill-wave', 'color' => 'green', 'period' => 'Mỗi booking'],
                    ['label' => 'TĂNG TRƯỞNG', 'id' => 'growth', 'badge' => 'growthBadge', 'change' => 'growthChange', 'icon' => 'fas fa-arrow-trend-up', 'color' => 'pink', 'period' => 'So với kỳ trước'],
                ];
            @endphp
            @foreach($kpis as $kpi)
                <div class="col-12 col-md-6 col-lg-4 d-flex">
                    <div class="kpi-card {{ $kpi['color'] }} flex-fill">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="kpi-label">{{ $kpi['label'] }}</div>
                                <div class="kpi-value loading" id="{{ $kpi['id'] }}">0</div>
                            </div>
                            <div class="kpi-icon icon-{{ $kpi['color'] }}"><i class="{{ $kpi['icon'] }}"></i></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="kpi-period">{{ $kpi['period'] }}</span>
                            <span class="change-badge change-positive" id="{{ $kpi['badge'] }}">
                                <i class="fas fa-arrow-up"></i><span id="{{ $kpi['change'] }}">0%</span>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4 d-flex flex-wrap">
            <div class="col-12 col-lg-7 d-flex">
                <div class="chart-card flex-fill">
                    <h3 class="chart-title"><i class="fas fa-chart-line text-primary me-2"></i>Doanh thu theo thời gian</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="col-12 col-lg-5 ">
                <div class="chart-card">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-pie text-success me-2"></i>Doanh thu theo sân con
                    </h3>

                    <div class="pie-chart-wrapper">
                        <canvas id="courtPieChart"></canvas>
                    </div>

                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex">
                <div class="chart-card flex-fill">
                    <h3 class="chart-title"><i class="fas fa-chart-bar text-warning me-2"></i>Đặt sân theo giờ</h3>
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex">
                <div class="chart-card flex-fill">
                    <h3 class="chart-title"><i class="fas fa-chart-column text-info me-2"></i>So sánh hiệu suất sân</h3>
                    <canvas id="courtsComparisonChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title"><i class="fas fa-crown text-warning me-2"></i>Top 10 Khách Hàng</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Họ tên</th>
                            <th>Điện thoại</th>
                            <th>Email</th>
                            <th class="text-center">Số lần đặt</th>
                            <th class="text-end">Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody id="topCustomersTable">
                        <tr>
                            <td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Đang tải...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@push('scripts')

    <script>
        const kpiDataUrl = "{{ route('owner.report.kpiData') }}";
        const revenueChartUrl = "{{ route('owner.report.revenueChart') }}";
        const bookingsByHourUrl = "{{ route('owner.report.bookingsByHour') }}";
        const facilityPieUrl = "{{ route('owner.report.revenueByCourt') }}";
        const courtsComparisonUrl = "{{ route('owner.report.courtsComparison') }}";
        const topCustomersUrl = "{{ route('owner.report.topCustomers') }}";
        const exportExcelUrl = "{{ route('owner.report.exportExcel') }}";
        const exportPdfUrl = "{{ route('owner.report.exportPdf') }}";
        const getCourtsUrl = "{{ route('owner.getCourts') }}";

        let revenueChartInstance, hourlyChartInstance, courtPieInstance, courtsChartInstance;

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', minimumFractionDigits: 0 }).format(amount);
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        }

        function updateBadge(badgeId, changeId, value) {
            const badge = document.getElementById(badgeId);
            const changeSpan = document.getElementById(changeId);
            const icon = badge.querySelector('i');
            if (value >= 0) {
                badge.className = 'change-badge change-positive';
                icon.className = 'fas fa-arrow-up';
            } else {
                badge.className = 'change-badge change-negative';
                icon.className = 'fas fa-arrow-down';
            }
            changeSpan.textContent = Math.abs(value) + '%';
        }

        function getQueryParams() {
            const dateRange = document.getElementById('dateRange').value;

            // 1. Lấy ID cơ sở TỪ INPUT ẨN
            const facilityId = document.getElementById('ownerFacilityId').value;

            // 2. Lấy ID sân con TỪ BỘ LỌC
            const courtId = document.getElementById('courtFilter').value;

            // Truyền cả 3 tham số (facilityId giờ là cố định, courtId là 'all' hoặc 1 số)
            let params = `range=${dateRange}&facility=${facilityId}&court=${courtId}`;

            if (dateRange === 'custom') {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                params += `&start_date=${startDate}&end_date=${endDate}`;
            }
            return params;
        }

        //Các thẻ KPI
        async function loadKpis() {
            document.querySelectorAll('.kpi-value').forEach(el => el.classList.add('loading'));
            try {
                const res = await fetch(`${kpiDataUrl}?${getQueryParams()}`);
                const data = await res.json();
                document.querySelectorAll('.kpi-value').forEach(el => el.classList.remove('loading'));

                document.getElementById('totalRevenue').textContent = formatCurrency(data.revenue.value);
                updateBadge('revenueBadge', 'revenueChange', data.revenue.change);
                document.getElementById('totalBookings').textContent = formatNumber(data.bookings.value);
                updateBadge('bookingBadge', 'bookingChange', data.bookings.change);
                document.getElementById('utilization').textContent = data.utilization.value + '%';
                updateBadge('utilizationBadge', 'utilizationChange', data.utilization.change);
                document.getElementById('totalCustomers').textContent = formatNumber(data.customers.value);
                updateBadge('customerBadge', 'customerChange', data.customers.change);
                document.getElementById('avgPrice').textContent = formatCurrency(data.avgPrice.value);
                updateBadge('priceBadge', 'priceChange', data.avgPrice.change);
                document.getElementById('growth').textContent = data.growth.value + '%';
                updateBadge('growthBadge', 'growthChange', data.growth.value);
            } catch (err) { console.error("Lỗi tải KPI:", err); }
        }

        async function loadCourts() {
            const select = document.getElementById('courtFilter');
            if (!select) {
                console.error('Không tìm thấy dropdown courtFilter');
                return;
            }

            const currentValue = select.value;
            console.log('🔄 Loading courts... Current value:', currentValue);

            try {
                const response = await fetch(getCourtsUrl);
                console.log('📡 Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                console.log('📊 Courts data:', data);

                if (data.success) {
                    // Xóa tất cả options hiện tại
                    select.innerHTML = '<option value="all">Tất cả sân con</option>';

                    if (data.courts && data.courts.length > 0) {
                        // Thêm từng sân vào dropdown
                        data.courts.forEach(court => {
                            const option = document.createElement('option');
                            option.value = court.court_id;
                            option.textContent = court.court_name;

                            // Giữ nguyên lựa chọn trước đó
                            if (court.court_id == currentValue) {
                                option.selected = true;
                            }

                            select.appendChild(option);
                        });

                        console.log(`Đã load ${data.courts.length} sân con`);
                    } else {
                        const option = document.createElement('option');
                        option.disabled = true;
                        option.textContent = 'Không có sân con nào';
                        select.appendChild(option);
                        console.warn('Không có sân con nào');
                    }
                } else {
                    console.error('API trả về success=false:', data);
                }
            } catch (error) {
                console.error('Lỗi load courts:', error);
                // alert('Không thể tải danh sách sân. Vui lòng thử lại.');
            }
        }


        //Các biểu đồ
        async function loadRevenueChart() {
            try {
                const res = await fetch(`${revenueChartUrl}?${getQueryParams()}`);
                const data = await res.json();
                if (revenueChartInstance) revenueChartInstance.destroy();

                const ctx = document.getElementById('revenueChart').getContext('2d');
                revenueChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: { labels: data.labels, datasets: [{ label: 'Doanh thu (VND)', data: data.revenues, borderColor: 'rgb(74,144,226)', backgroundColor: 'rgba(74,144,226,0.1)', tension: 0.4, fill: true }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true }, tooltip: { callbacks: { label: ctx => formatCurrency(ctx.parsed.y) } } }, scales: { y: { beginAtZero: true, ticks: { callback: value => formatCurrency(value) } } } }
                });
            } catch (err) { console.error("Lỗi tải Biểu đồ Doanh thu:", err); }
        }

        async function loadHourlyChart() {
            try {
                const res = await fetch(`${bookingsByHourUrl}?${getQueryParams()}`);
                const data = await res.json();
                if (hourlyChartInstance) hourlyChartInstance.destroy();

                const ctx = document.getElementById('hourlyChart').getContext('2d');
                hourlyChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: data.labels, datasets: [{ label: 'Số lượt đặt', data: data.counts, backgroundColor: 'rgba(255,211,61,0.7)', borderColor: 'rgb(255,211,61)', borderWidth: 2 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            } catch (err) { console.error("Lỗi tải Biểu đồ Giờ:", err); }
        }

        function generateColors(count) {
            const colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
            ];

            if (count <= colors.length) {
                return colors.slice(0, count);
            }

            // Generate thêm màu nếu cần
            const generated = [];
            for (let i = 0; i < count; i++) {
                const hue = Math.floor((360 / count) * i);
                generated.push(`hsl(${hue}, 70%, 60%)`);
            }
            return generated;
        }

        async function loadCourtPieChart() {
            const chartCanvas = document.getElementById("courtPieChart");
            const chartContainer = chartCanvas?.closest('.chart-card');

            if (!chartCanvas) {
                console.error('Canvas courtPieChart không tồn tại!');
                return;
            }

            try {
                const params = getQueryParams();
                const url = `${facilityPieUrl}?${params}`;

                console.log('🔍 Loading pie chart:', url);

                const res = await fetch(url);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();
                console.log('📊 Pie data received:', data);

                // Hủy chart cũ nếu có
                if (courtPieInstance) {
                    courtPieInstance.destroy();
                    courtPieInstance = null;
                }

                // Kiểm tra dữ liệu
                if (!data.labels || data.labels.length === 0) {
                    const ctx = chartCanvas.getContext('2d');
                    ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
                    ctx.font = '14px Arial';
                    ctx.fillStyle = '#999';
                    ctx.textAlign = 'center';
                    ctx.fillText('Không có dữ liệu', chartCanvas.width / 2, chartCanvas.height / 2);
                    return;
                }

                // Vẽ chart mới
                const ctx = chartCanvas.getContext("2d");
                courtPieInstance = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.revenues,
                            backgroundColor: generateColors(data.labels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 8,
                                    font: { size: 11 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const label = ctx.label || '';
                                        const value = formatCurrency(ctx.parsed);
                                        const percent = ((ctx.parsed / data.revenues.reduce((a, b) => a + b, 0)) * 100).toFixed(1);
                                        return `${label}: ${value} (${percent}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                console.log('Pie chart loaded successfully');

            } catch (err) {
                console.error("Lỗi tải Biểu đồ Pie:", err);

                const ctx = chartCanvas.getContext('2d');
                ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
                ctx.font = '14px Arial';
                ctx.fillStyle = '#dc3545';
                ctx.textAlign = 'center';
                ctx.fillText('Lỗi tải dữ liệu', chartCanvas.width / 2, chartCanvas.height / 2);
            }
        }


        async function loadCourtsComparison() {
            try {
                const res = await fetch(`${courtsComparisonUrl}?${getQueryParams()}`);
                const data = await res.json();
                if (courtsChartInstance) courtsChartInstance.destroy();

                const labels = data.map(d => `${d.court_name}`);
                const revenues = data.map(d => d.revenue);

                const ctx = document.getElementById('courtsComparisonChart').getContext('2d');
                courtsChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: labels, datasets: [{ label: 'Doanh thu', data: revenues, backgroundColor: 'rgba(74,144,226,0.7)', borderColor: 'rgb(74,144,226)', borderWidth: 2 }] },
                    options: {
                        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => formatCurrency(ctx.parsed.x) } } },
                        scales: { x: { beginAtZero: true, ticks: { callback: value => formatCurrency(value) } } }
                    }
                });
            } catch (err) { console.error("Lỗi tải Biểu đồ So sánh Sân:", err); }
        }

        async function loadTopCustomers() {
            const tbody = document.getElementById('topCustomersTable');
            try {
                const res = await fetch(`${topCustomersUrl}?${getQueryParams()}`);
                const data = await res.json();
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Không tìm thấy dữ liệu.</td></tr>';
                    return;
                }

                data.forEach((c, i) => {
                    tbody.innerHTML += `
                                                                            <tr>
                                                                                <td>${i + 1}</td> <td><strong>${c.fullname}</strong></td> <td>${c.phone}</td> <td>${c.email}</td>
                                                                                <td class="text-center"><span class="badge bg-primary">${c.total_bookings}</span></td>
                                                                                <td class="text-end"><strong>${formatCurrency(c.total_spent)}</strong></td>
                                                                            </tr>`;
                });
            } catch (err) { console.error("Lỗi tải Top Khách hàng:", err); tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi khi tải dữ liệu.</td></tr>'; }
        }

        function loadAllData() {
            loadKpis();
            loadRevenueChart();
            loadHourlyChart();
            loadCourtPieChart();
            loadCourtsComparison();
            loadTopCustomers();
        }

        document.getElementById('dateRange').addEventListener('change', function () {
            const customDiv = document.getElementById('customDateRange');
            if (this.value === 'custom') customDiv.style.display = 'flex';
            else { customDiv.style.display = 'none'; loadAllData(); }
        });


        // Listener cho SÂN CON (bộ lọc duy nhất)
        document.getElementById('courtFilter').addEventListener('change', loadAllData);

        document.getElementById('applyCustomDate')?.addEventListener('click', loadAllData);

        document.getElementById('exportExcel').addEventListener('click', () => window.location.href = `${exportExcelUrl}?${getQueryParams()}`);
        document.getElementById('exportPdf').addEventListener('click', () => window.location.href = `${exportPdfUrl}?${getQueryParams()}`);

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Page loaded');

            // Load danh sách sân trước
            loadCourts();

            // Sau đó load data sau 500ms
            setTimeout(loadAllData, 500);
        });

        setInterval(function () {
            console.log('Auto refresh courts');
            loadCourts();
        }, 30000);
    </script>
@endpush