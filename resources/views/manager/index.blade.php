@extends('layouts.manager')

@section('manager_content')
    <h1 class="h3 mb-4 fw-bold">Dashboard Quản lý Sân</h1>

    {{-- =======================
        BỘ LỌC THỜI GIAN
    ======================== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('manager.index') }}" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ngày</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Từ ngày</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Đến ngày</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tháng</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                </div>

                <div class="col-12 text-end mt-2">
                    <button class="btn btn-primary px-4">Lọc</button>
                    <a href="{{ route('manager.index') }}" class="btn btn-secondary px-4">Reset</a>
                </div>
            </form>

        </div>
    </div>



    {{-- =======================
        KPI CARDS
    ======================== --}}

    <div class="row">

        @php
            $kpiCards = [
                ['title' => 'Lượt đặt hôm nay', 'value' => $stats['bookings_today'], 'color' => 'primary'],
                ['title' => 'Hủy hôm nay', 'value' => $stats['cancel_today'], 'color' => 'danger'],
                ['title' => 'Giờ hoạt động', 'value' => $stats['open_time'] . ' - ' . $stats['close_time'], 'color' => 'warning'],
                ['title' => 'Sân bận / Sân trống', 'value' => $stats['busy_courts'] . ' / ' . $stats['free_courts'], 'color' => 'info'],
                ['title' => 'Doanh thu hôm nay', 'value' => number_format($stats['revenue_today']) . ' đ', 'color' => 'success'],
                ['title' => 'Doanh thu tháng', 'value' => number_format($stats['revenue_month']) . ' đ', 'color' => 'dark'],
            ];
        @endphp

        @foreach ($kpiCards as $kpi)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-start border-3 border-{{ $kpi['color'] }}">
                    <div class="card-body">
                        <h6 class="text-{{ $kpi['color'] }} fw-bold">{{ $kpi['title'] }}</h6>
                        <h3 class="fw-bold">{{ $kpi['value'] }}</h3>
                    </div>
                </div>
            </div>
        @endforeach

    </div>


    {{-- =======================
        BIỂU ĐỒ
    ======================== --}}
    <div class="row mt-4">

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Biểu đồ đặt sân theo giờ</div>
                <div class="card-body">
                    <canvas id="hourChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Hiệu suất từng sân</div>
                <div class="card-body">
                    <canvas id="courtChart"></canvas>
                </div>
            </div>
        </div>

    </div>

@endsection



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const hourLabels = @json($hourlyBookings['labels']);
    const hourData   = @json($hourlyBookings['data']);

    const courtLabels = @json($courtPerformance['labels']);
    const courtData   = @json($courtPerformance['data']);

    new Chart(document.getElementById('hourChart'), {
        type: 'line',
        data: {
            labels: hourLabels,
            datasets: [{
                label: "Lượt đặt",
                data: hourData,
                borderWidth: 2,
                tension: 0.4
            }]
        }
    });

    new Chart(document.getElementById('courtChart'), {
        type: 'bar',
        data: {
            labels: courtLabels,
            datasets: [{
                label: "Lượt đặt",
                data: courtData,
                borderWidth: 1
            }]
        }
    });

    // ===============================
    // 🔄 Auto Refresh mỗi 10 giây
    // ===============================
    setInterval(() => {
        location.reload();
    }, 5000);
</script>
@endsection
