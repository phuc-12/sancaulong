@extends('layouts.manager')

@section('manager_content')
    {{-- ✅ THÊM META TAGS ĐỂ TRUYỀN URLS --}}
    <meta name="api-courts" content="{{ route('manager.api.courts') }}">
    <meta name="api-kpi" content="{{ route('manager.api.kpi') }}">
    <meta name="api-hourly" content="{{ route('manager.api.hourly') }}">
    <meta name="api-revenue" content="{{ route('manager.api.revenue') }}">

    <style>
        :root {
            --primary: #4a90e2;
            --bg: #f5f7fa;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .kpi-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            height: 100%;
            border-left: 4px solid #ddd;
        }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 5px;
        }

        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .custom-date {
            display: none;
        }
    </style>

    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Dashboard Quản lý</h1>
        </div>

        {{-- 1. BỘ LỌC --}}
        <div class="filter-section">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <strong><i class="fas fa-filter text-primary"></i> Bộ lọc:</strong>

                <select class="form-select w-auto" id="dateRange">
                    <option value="today">Hôm nay</option>
                    <option value="week">Tuần này</option>
                    <option value="month" selected>Tháng này</option>
                    <option value="custom">Tùy chỉnh</option>
                </select>

                <div id="customDate" class="custom-date gap-2 align-items-center">
                    <input type="date" id="startDate" class="form-control">
                    <span>-</span>
                    <input type="date" id="endDate" class="form-control">
                    <button class="btn btn-success btn-sm " style="width: 200px" id="btnApply">Áp dụng</button>
                </div>

                <select class="form-select w-auto ms-auto" id="courtFilter">
                    <option value="all">Đang tải sân...</option>
                </select>

                <button class="btn btn-light border" onclick="location.reload()"><i class="fas fa-sync"></i></button>
            </div>
        </div>

        {{-- 2. KPI CARDS --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="kpi-card" style="border-color: #4a90e2;">
                    <div class="text-muted fw-bold small">LƯỢT ĐẶT</div>
                    <div class="kpi-value" id="kpiBookings">...</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" style="border-color: #2ecc71;">
                    <div class="text-muted fw-bold small">DOANH THU</div>
                    <div class="kpi-value" id="kpiRevenue">...</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" style="border-color: #e74c3c;">
                    <div class="text-muted fw-bold small">LƯỢT HỦY</div>
                    <div class="kpi-value" id="kpiCancel">...</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" style="border-color: #f1c40f;">
                    <div class="text-muted fw-bold small">SÂN BẬN / TỔNG</div>
                    <div class="kpi-value" id="kpiUtil">...</div>
                </div>
            </div>
        </div>

        {{-- 3. BIỂU ĐỒ --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="chart-card">
                    <h5 class="mb-3">Mật độ đặt sân theo giờ</h5>
                    <div style="height: 350px;"><canvas id="hourlyChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <h5 class="mb-3">Hiệu suất từng sân (Doanh thu)</h5>
                    <div style="height: 350px;"><canvas id="courtChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        console.log('🚀 Script đã load!');

        // ✅ LẤY API URLs TỪ META TAGS
        const API = {
            courts: document.querySelector('meta[name="api-courts"]').content,
            kpi: document.querySelector('meta[name="api-kpi"]').content,
            hourly: document.querySelector('meta[name="api-hourly"]').content,
            revenue: document.querySelector('meta[name="api-revenue"]').content
        };

        console.log('📍 API URLs:', API);

        // Format tiền
        const formatCurrency = (val) => {
            const num = parseFloat(val) || 0;
            return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
        };

        let chart1, chart2;

        // Lấy params từ bộ lọc
        function getParams() {
            const range = document.getElementById('dateRange').value;
            const court = document.getElementById('courtFilter').value;
            let q = `?range=${range}&court=${court}`;
            
            if (range === 'custom') {
                const start = document.getElementById('startDate').value;
                const end = document.getElementById('endDate').value;
                if (!start || !end) {
                    alert('Vui lòng chọn đầy đủ ngày!');
                    return null;
                }
                q += `&start_date=${start}&end_date=${end}`;
            }
            return q;
        }

        // Tải danh sách sân
        async function loadCourts() {
            console.log('🏟️ Đang tải danh sách sân...');
            try {
                const res = await fetch(API.courts);
                const data = await res.json();
                console.log('✅ Courts loaded:', data);

                const select = document.getElementById('courtFilter');
                if (data.success && data.courts) {
                    let html = '<option value="all">Tất cả sân con</option>';
                    data.courts.forEach(c => {
                        html += `<option value="${c.court_id}">${c.court_name}</option>`;
                    });
                    select.innerHTML = html;
                    console.log('✅ Đã load', data.courts.length, 'sân');
                }
            } catch (e) {
                console.error("❌ Lỗi load courts:", e);
                document.getElementById('courtFilter').innerHTML = '<option>Lỗi</option>';
            }
        }

        // Tải tất cả dữ liệu
        async function loadData() {
            const params = getParams();
            if (!params) return;

            console.log('📊 Loading data with:', params);

            // === KPI ===
            try {
                console.log('🔗 Fetching KPI from:', API.kpi + params);
                const res = await fetch(API.kpi + params);
                const d = await res.json();
                console.log('✅ KPI Data:', d);

                document.getElementById('kpiBookings').innerText = d.bookings || 0;
                document.getElementById('kpiRevenue').innerText = formatCurrency(d.revenue);
                document.getElementById('kpiCancel').innerText = d.cancel || 0;
                document.getElementById('kpiUtil').innerText = d.utilization || '0/0';
            } catch (e) {
                console.error("❌ KPI Error:", e);
            }

            // === CHART GIỜ ===
            try {
                console.log('🔗 Fetching Hourly from:', API.hourly + params);
                const res = await fetch(API.hourly + params);
                const d = await res.json();
                console.log('✅ Hourly Data:', d);

                if (chart1) chart1.destroy();
                chart1 = new Chart(document.getElementById('hourlyChart'), {
                    type: 'bar',
                    data: {
                        labels: d.labels || [],
                        datasets: [{
                            label: 'Lượt đặt',
                            data: d.counts || [],
                            backgroundColor: 'rgba(74, 144, 226, 0.8)',
                            borderColor: 'rgb(74, 144, 226)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            } catch (e) {
                console.error("❌ Hourly Error:", e);
            }

            // === CHART SÂN ===
            try {
                console.log('🔗 Fetching Revenue from:', API.revenue + params);
                const res = await fetch(API.revenue + params);
                const d = await res.json();
                console.log('✅ Revenue Data:', d);

                const revenues = (d.revenues || []).map(v => parseFloat(v) || 0);

                if (chart2) chart2.destroy();
                chart2 = new Chart(document.getElementById('courtChart'), {
                    type: 'bar',
                    data: {
                        labels: d.labels || [],
                        datasets: [{
                            label: 'Doanh thu',
                            data: revenues,
                            backgroundColor: 'rgba(46, 204, 113, 0.8)',
                            borderColor: 'rgb(46, 204, 113)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (c) => formatCurrency(c.raw)
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    callback: (v) => formatCurrency(v)
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error("❌ Revenue Error:", e);
            }
        }

        // === EVENTS ===
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOM Ready!');
            loadCourts();
            setTimeout(loadData, 800);
        });

        document.getElementById('dateRange').addEventListener('change', function() {
            const div = document.getElementById('customDate');
            if (this.value === 'custom') {
                div.style.display = 'flex';
            } else {
                div.style.display = 'none';
                loadData();
            }
        });

        document.getElementById('courtFilter').addEventListener('change', loadData);
        document.getElementById('btnApply').addEventListener('click', loadData);
    </script>
@endsection
