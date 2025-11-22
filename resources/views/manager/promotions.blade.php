@extends('layouts.manager')

@section('manager_content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold">Quản lý khuyến mãi / Sự kiện</h1>

    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addPromotionModal">
        <i class="fa-solid fa-plus me-1"></i> Thêm khuyến mãi
    </button>
</div>

{{-- BẢNG DANH SÁCH KHUYẾN MÃI --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">
        Danh sách chương trình khuyến mãi
    </div>

    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Mô tả</th>
                    <th>Loại</th>
                    <th>Giá trị</th>
                    <th>Ngày áp dụng</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($promotions as $promo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <strong>{{ $promo->description }}</strong>
                        </td>

                        {{-- Loại khuyến mãi --}}
                        <td>
                            @php
                                $types = [
                                    // 'hours'     => 'Theo số giờ chơi',
                                    'special'   => 'Ngày đặc biệt',
                                    'customer'  => 'Khách cố định',
                                    'first'     => 'Lần đầu',
                                    'tournament'=> 'Tổ chức giải đấu',
                                    'invoice'   => 'Hóa đơn lớn',
                                    'monthly'   => 'Thuê dài hạn'
                                ];
                            @endphp

                            <span class="badge bg-info text-dark">
                                {{ $types[$promo->discount_type] ?? $promo->discount_type }}
                            </span>
                        </td>

                        {{-- Giá trị --}}
                        <td>
                            @if ($promo->value < 1)
                                <span>{{ $promo->value * 100 }}%</span>
                            @else
                                <span>{{ number_format($promo->value) }}đ</span>
                            @endif
                        </td>

                        {{-- Ngày --}}
                        <td>
                            {{ \Carbon\Carbon::parse($promo->start_date)->format('d/m/Y') }} <br>
                            → <strong>{{ \Carbon\Carbon::parse($promo->end_date)->format('d/m/Y') }}</strong>
                        </td>

                        {{-- Trạng thái --}}
                        <td>
                            @php
                                $today = \Carbon\Carbon::now();
                                $endDate = \Carbon\Carbon::parse($promo->end_date);
                            @endphp

                            @if ($endDate->lt($today))
                                <span class="badge bg-danger">Hết hạn</span>
                            @elseif ($promo->status == 1)
                                <span class="badge bg-success">Đang áp dụng</span>
                            @else
                                <span class="badge bg-secondary">Ngừng</span>
                            @endif
                        </td>

                        {{-- Button --}}
                        <td class="text-center">

                            {{-- Nút Edit mở modal theo ID --}}
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editPromoModal_{{ $promo->promotion_id }}">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            {{-- Nút Delete --}}
                            <form action="{{ route('manager.promotions.delete', $promo->promotion_id) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xoá chương trình này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Chưa có chương trình khuyến mãi nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@foreach ($promotions as $promo)
<div class="modal fade" id="editPromoModal_{{ $promo->promotion_id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('manager.promotions.update', $promo->promotion_id) }}" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Chỉnh sửa khuyến mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3" required>{{ $promo->description }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Loại khuyến mãi</label>
                            <select name="discount_type" class="form-select" required>
                                {{-- <option value="hours"      {{ $promo->discount_type=='hours' ? 'selected' : '' }}>Theo số giờ chơi</option> --}}
                                <option value="special"    {{ $promo->discount_type=='special' ? 'selected' : '' }}>Ngày đặc biệt</option>
                                <option value="customer"   {{ $promo->discount_type=='customer' ? 'selected' : '' }}>Khách cố định</option>
                                <option value="first"      {{ $promo->discount_type=='first' ? 'selected' : '' }}>Lần đầu</option>
                                <option value="tournament" {{ $promo->discount_type=='tournament' ? 'selected' : '' }}>Tổ chức giải đấu</option>
                                <option value="invoice"    {{ $promo->discount_type=='invoice' ? 'selected' : '' }}>Hóa đơn lớn</option>
                                <option value="monthly"    {{ $promo->discount_type=='monthly' ? 'selected' : '' }}>Thuê dài hạn</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Giá trị</label>
                            <input type="number" step="0.01" name="value" class="form-control"
                                   value="{{ $promo->value }}" required>
                            <small class="text-muted">0.1 = 10%, 20000 = 20k</small>
                        </div>
                    </div>

                    @php
                        $today = \Carbon\Carbon::now()->format('Y-m-d'); // ngày hiện tại
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <label class="fw-bold">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ \Carbon\Carbon::parse($promo->start_date)->format('Y-m-d') }}"
                                min="{{ $today }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ \Carbon\Carbon::parse($promo->end_date)->format('Y-m-d') }}"
                                min="{{ $today }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $promo->status == 1 ? 'selected' : '' }}>Đang áp dụng</option>
                            <option value="0" {{ $promo->status == 0 ? 'selected' : '' }}>Ngừng</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button class="btn btn-primary">Lưu thay đổi</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
{{-- MODAL THÊM KHUYẾN MÃI --}}
<div class="modal fade" id="addPromotionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('manager.promotions.create') }}" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Thêm chương trình khuyến mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Loại khuyến mãi</label>
                            <select name="discount_type" class="form-select" required>
                                {{-- <option value="hours">Theo số giờ chơi</option> --}}
                                <option value="special">Ngày đặc biệt</option>
                                <option value="customer">Khách cố định</option>
                                <option value="first">Lần đầu</option>
                                <option value="tournament">Tổ chức giải đấu</option>
                                <option value="invoice">Hóa đơn lớn</option>
                                <option value="monthly">Thuê dài hạn</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Giá trị</label>
                            <input type="number" step="0.01" name="value" class="form-control" required>
                            <small class="text-muted">Ví dụ: 0.1 = 10%, 20000 = 20k</small>
                        </div>
                    </div>

                    @php
                        $today = \Carbon\Carbon::now()->format('Y-m-d'); // ngày hiện tại
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <label class="fw-bold">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control"
                                min="{{ $today }}" required
                                id="start_date">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control"
                                min="{{ $today }}" required
                                id="end_date">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="1">Đang áp dụng</option>
                            <option value="0">Ngừng</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button class="btn btn-primary">Thêm</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
