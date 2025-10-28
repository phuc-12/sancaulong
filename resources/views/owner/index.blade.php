@extends('layouts.owner')

@section('owner_content')
    {{-- HIỂN THỊ THÔNG BÁO TRẠNG THÁI CƠ SỞ --}}
    @if(isset($facilityStatusMessage) && $facilityStatusMessage)
        <div class="alert alert-{{ $facilityStatusType }} alert-dismissible fade show" role="alert">
            {{ $facilityStatusMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <h1 class="h3 mb-4">Tổng Quan (Cơ sở của bạn)</h1>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="kpi-label text-success">Doanh Thu (Tháng)</div>
                    <div class="kpi-value mb-1">0đ</div>
                    <div class="kpi-growth text-muted small">Tính toán % ở đây</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="kpi-label text-primary">Đặt Sân (Hôm nay)</div>
                    <div class="kpi-value mb-1">0</div>
                    <div class="kpi-growth text-muted small">Tổng lượt đặt</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card border-start border-4 border-info h-100">
                <div class="card-body">
                    <div class="kpi-label text-info">Đánh Giá Mới (Tuần)</div>
                    <div class="kpi-value mb-1">+0</div>
                    <div class="kpi-growth text-muted small">...</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="kpi-label text-warning">Tổng Số Sân Con</div>
                    <div class="kpi-value mb-1">0</div>
                    <div class="kpi-growth text-muted small">Sân đang hoạt động</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><b>Biểu Đồ Doanh Thu (Cơ sở của bạn)</b></h6>
                </div>
                <div class="card-body">
                    {{-- (Canvas) --}}
                    <canvas id="ownerRevenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><b>Lịch đặt sắp tới</b></h6>
                </div>
                <div class="card-body">
                    {{-- (Danh sách lịch đặt) --}}
                    <p>Chưa có lịch đặt nào sắp tới.</p>
                </div>
            </div>
        </div>
    </div>
@endsection