@extends('layouts.owner') {{-- Kế thừa layout Owner --}}

@section('reports_content')
    <h1 class="h3 mb-4">Báo Cáo Phân Tích Kinh Doanh</h1>
    <p class="text-muted">Dữ liệu được lọc theo Cơ sở của bạn (ID: {{ $facilityId }})</p>

    {{-- PHẦN LỌC DỮ LIỆU (FILTERS) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-funnel me-2"></i>Bộ Lọc Báo Cáo</h5>
            <form id="reportFilterForm" class="row g-3">
                
                {{-- Lọc theo Ngày Bắt Đầu/Kết Thúc --}}
                <div class="col-md-4">
                    <label for="startDate" class="form-label">Khoảng Ngày Báo Cáo</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                        <span class="input-group-text">đến</span>
                        <input type="date" class="form-control" id="endDate" name="end_date" required>
                    </div>
                </div>

                {{-- Lọc theo Sân Con --}}
                <div class="col-md-3">
                    <label for="courtFilter" class="form-label">Lọc theo Sân Con</label>
                    <select class="form-select" id="courtFilter" name="court_id">
                        <option value="">Tất cả các Sân</option>
                        @foreach ($courts as $court)
                            <option value="{{ $court->court_id }}">{{ $court->court_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nút Áp Dụng Lọc --}}
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-graph-up me-1"></i> Áp dụng
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- HIỂN THỊ BIỂU ĐỒ --}}
    <div class="row">
        {{-- Biểu đồ Doanh thu (Line Chart) --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">Doanh Thu Thuần Theo Ngày</h5></div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ Tỉ lệ sử dụng sân (Pie Chart) --}}
        <div class="col-lg-4 mb-4">
             <div class="card shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">Tỉ Lệ Đặt Sân Theo Sân Con</h5></div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="utilizationChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartDataUrl = '{{ route("owner.reports.data") }}';
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const utilizationCtx = document.getElementById('utilizationChart').getContext('2d');
            let revenueChart, utilizationChart;

            // Đặt ngày mặc định cho bộ lọc (ví dụ: 30 ngày qua)
            const today = new Date();
            const past30Days = new Date(today);
            past30Days.setDate(today.getDate() - 30);
            
            document.getElementById('startDate').valueAsDate = past30Days;
            document.getElementById('endDate').valueAsDate = today;

            // Hàm tạo biểu đồ đường (Doanh thu)
            function renderRevenueChart(labels, data) {
                if (revenueChart) revenueChart.destroy();
                revenueChart = new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Doanh Thu',
                            data: data,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Doanh Thu (VND)' },
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN') + 'đ';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Hàm tạo biểu đồ Pie (Tỉ lệ sử dụng)
            function renderUtilizationChart(labels, data) {
                if (utilizationChart) utilizationChart.destroy();
                utilizationChart = new Chart(utilizationCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Số Lượt Đặt',
                            data: data,
                            backgroundColor: [
                                '#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1', '#f8f9fa'
                            ], // Màu sắc vòng
                            hoverOffset: 10
                        }]
                    },
                    options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // Hàm chính tải dữ liệu
            async function loadReportData() {
                const formData = new FormData(document.getElementById('reportFilterForm'));
                const params = new URLSearchParams(formData).toString();
                
                try {
                    const response = await fetch(`${chartDataUrl}?${params}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const result = await response.json();

                    // Cập nhật biểu đồ Doanh thu
                    renderRevenueChart(result.revenue_data.labels, result.revenue_data.data);
                    
                    // Cập nhật biểu đồ Tỉ lệ sử dụng
                    renderUtilizationChart(result.utilization_data.labels, result.utilization_data.data);

                } catch (error) {
                    console.error('Lỗi khi tải dữ liệu báo cáo:', error);
                    // Hiển thị thông báo lỗi trên UI nếu cần
                }
            }

            // 4. Lắng nghe sự kiện submit form lọc
            document.getElementById('reportFilterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                loadReportData(); // Tải lại dữ liệu khi submit
            });

            // Tải dữ liệu lần đầu khi trang tải xong
            loadReportData(); 
        });
    </script>
@endpush
