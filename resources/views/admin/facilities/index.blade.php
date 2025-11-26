@extends('layouts.admin')

@section('facilities_content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý Doanh nghiệp (Cơ sở Sân)</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Đã xảy ra lỗi:</h6>
            <ul class="mb-0"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Bảng Danh sách Cơ sở --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if($facilities->isEmpty())
                <div class="alert alert-secondary text-center mb-0">Chưa có cơ sở sân cầu lông nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Cơ Sở</th>
                                <th>Chủ Sân</th>
                                <th class="text-center">Trạng Thái Hoạt Động</th>
                                <th class="text-end">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($facilities as $facility)
                                <tr>
                                    {{-- Tên & Địa chỉ --}}
                                    <td>
                                        <strong>{{ $facility->facility_name }}</strong>
                                        <div class="small text-muted">{{ $facility->address }}</div>
                                    </td>
                                    {{-- Chủ Sân --}}
                                    <td>
                                        @if($facility->owner)
                                            <div>{{ $facility->owner->fullname ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $facility->owner->email ?? 'N/A' }}</div>
                                        @else
                                            <span class="text-danger small">Lỗi liên kết</span>
                                        @endif
                                    </td>
                                    {{-- Trạng Thái --}}
                                    <td class="text-center">
                                        @if($facility->status == 'chờ duyệt') <span class="badge bg-warning text-dark">Chờ
                                            duyệt</span>
                                        @elseif($facility->status == 'đã duyệt') <span class="badge bg-success">Đang hoạt
                                            động</span>
                                        @elseif($facility->status == 'tạm khóa') <span class="badge bg-secondary">Tạm khóa</span>
                                        @elseif($facility->status == 'từ chối') <span class="badge bg-danger">Bị từ chối</span>
                                        @else <span
                                            class="badge bg-light text-dark">{{ ucfirst($facility->status ?? 'Chưa rõ') }}</span>
                                        @endif
                                    </td>

                                    {{-- Hành Động --}}
                                    <td class="text-end">
                                        @if($facility->status == 'chờ duyệt')
                                            {{-- Nút Mở Modal Duyệt --}}
                                            <button type="button" class="btn btn-sm btn-warning text-dark"
                                                title="Xem chi tiết và duyệt yêu cầu" data-bs-toggle="modal"
                                                data-bs-target="#facilityDetailsModal-{{ $facility->facility_id }}">
                                                <i class="bi bi-eye-fill me-1"></i> Xem & Duyệt
                                            </button>
                                        @elseif($facility->status == 'đã duyệt')
                                            {{-- Nút Tạm Khóa --}}
                                            <form action="{{ route('admin.facility.suspend', $facility->facility_id) }}" method="POST"
                                                class="d-inline needs-confirmation"
                                                data-confirm-message="Bạn có chắc muốn tạm khóa cơ sở '{{ $facility->facility_name }}'?">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    title="Tạm khóa hoạt động">
                                                    <i class="bi bi-pause-circle"></i> Tạm khóa
                                                </button>
                                            </form>
                                        @elseif($facility->status == 'tạm khóa')
                                            {{-- Nút Kích hoạt lại --}}
                                            <form action="{{ route('admin.facility.activate', $facility->facility_id) }}" method="POST"
                                                class="d-inline needs-confirmation"
                                                data-confirm-message="Bạn có chắc muốn kích hoạt lại cơ sở '{{ $facility->facility_name }}'?">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Kích hoạt lại">
                                                    <i class="bi bi-play-circle"></i> Kích hoạt
                                                </button>
                                            </form>
                                        @elseif($facility->status == 'từ chối')
                                            {{-- Nút Xem Chi Tiết --}}
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                title="Xem chi tiết yêu cầu đã từ chối" data-bs-toggle="modal"
                                                data-bs-target="#facilityDetailsModal-{{ $facility->facility_id }}">
                                                <i class="bi bi-info-circle"></i> Chi tiết
                                            </button>
                                            {{-- Có thể thêm nút duyệt lại --}}
                                            <form action="{{ route('admin.facility.approve', $facility->facility_id) }}" method="POST"
                                                class="d-inline ms-1 needs-confirmation" data-confirm-message="Duyệt lại cơ sở này?">
                                                @csrf <button type="submit" class="btn btn-sm btn-outline-warning" title="Duyệt lại"><i
                                                        class="bi bi-arrow-clockwise"></i> Duyệt Lại</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Phân trang --}}
                <div class="mt-3 d-flex justify-content-end">
                    {{ $facilities->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL DUYỆT YÊU CẦU / XEM CHI TIẾT --}}
    @foreach ($facilities->whereIn('status', ['chờ duyệt', 'từ chối']) as $facility)
        <div class="modal fade" id="facilityDetailsModal-{{ $facility->facility_id }}" tabindex="-1"
            aria-labelledby="modalTitle-{{ $facility->facility_id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle-{{ $facility->facility_id }}">
                            @if($facility->status == 'chờ duyệt') Duyệt Yêu Cầu Đăng Ký
                            @else Chi Tiết Yêu Cầu @endif
                            : {{ $facility->facility_name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Thông báo trạng thái --}}
                        @if($facility->status == 'chờ duyệt')
                            <div class="alert alert-warning d-flex align-items-center" role="alert"> <i
                                    class="bi bi-hourglass-split me-2"></i>
                                <div> Yêu cầu này đang ở trạng thái <strong>Chờ Duyệt</strong>. </div>
                            </div>
                        @elseif($facility->status == 'từ chối')
                            <div class="alert alert-danger d-flex align-items-center" role="alert"> <i
                                    class="bi bi-x-octagon-fill me-2"></i>
                                <div> Yêu cầu này đã bị <strong>Từ Chối</strong>. </div>
                            </div>
                        @endif

                        {{-- Thông tin chi tiết --}}
                        <div class="row">
                            {{-- THÔNG TIN CHỦ SÂN --}}
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6><i class="bi bi-person-badge me-2"></i>Thông tin Chủ sân</h6>
                                <hr class="mt-1 mb-2">
                                @if($facility->owner)
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Họ tên:</dt>
                                        <dd class="col-sm-8">{{ $facility->owner->fullname ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Email:</dt>
                                        <dd class="col-sm-8">{{ $facility->owner->email ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Số ĐT:</dt>
                                        <dd class="col-sm-8">{{ $facility->owner->phone ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Địa chỉ:</dt>
                                        <dd class="col-sm-8">{{ $facility->owner->address ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">CCCD:</dt>
                                        <dd class="col-sm-8">{{ $facility->owner->CCCD ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">STK:</dt>
                                        <dd class="col-sm-8">{{ $facility->account_no ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Chủ tài khoản:</dt>
                                        <dd class="col-sm-8">{{ $facility->account_name ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Ngân hàng:</dt>
                                        <dd class="col-sm-8">{{ $facility->account_bank ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Số sân con:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-info">{{ $facility->quantity_court ?? 0 }} sân</span>
                                        </dd>
                                    </dl>
                                @else
                                    <p class="text-danger small">Không tìm thấy thông tin chủ sân liên kết.</p>
                                @endif
                            </div>

                            {{-- THÔNG TIN CƠ SỞ ĐĂNG KÝ --}}
                            <div class="col-md-6 border-start">
                                <h6><i class="bi bi-building me-2"></i>Thông tin Cơ sở Đăng ký</h6>
                                <hr class="mt-1 mb-2">
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Tên cơ sở:</dt>
                                    <dd class="col-sm-7">{{ $facility->facility_name }}</dd>
                                    <dt class="col-sm-5">Địa chỉ sân:</dt>
                                    <dd class="col-sm-7">{{ $facility->address }}</dd>
                                    <dt class="col-sm-5">Số ĐT sân:</dt>
                                    <dd class="col-sm-7">{{ $facility->phone }}</dd>
                                    <dt class="col-sm-5">Giờ hoạt động:</dt>
                                    <dd class="col-sm-7">{{ \Carbon\Carbon::parse($facility->open_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($facility->close_time)->format('H:i') }}</dd>
                                    <dt class="col-sm-5">Giá mặc định:</dt>
                                    <dd class="col-sm-7">{{ $facility->courtPrice->default_price }}</dd>
                                    <dt class="col-sm-5">Giá giờ vàng</dt>
                                    <dd class="col-sm-7">{{ $facility->courtPrice->special_price }}</dd>

                                    <dt class="col-sm-5">Mô tả:</dt>
                                    <dd class="col-sm-7">{{ $facility->description ?? '(Không có)' }}</dd>
                                    <!-- <dt class="col-sm-5">Giấy phép KD:</dt>  -->
                                    <dd class="col-sm-7">
                                        <!-- @if($facility->business_license_path)
                                                <a href="{{ asset($facility->business_license_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"> {{-- Sửa: Dùng asset() nếu file trong public --}}
                                                    <i class="bi bi-file-earmark-text me-1"></i> Xem File
                                                </a>
                                            @else
                                                <span class="text-muted">(Không có file)</span>
                                            @endif -->
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    {{-- Nút bấm (Chỉ hiện khi 'chờ duyệt') --}}
                    @if($facility->status == 'chờ duyệt')
                        <div class="modal-footer justify-content-center">
                            <form action="{{ route('admin.facility.deny', $facility->facility_id) }}" method="POST"
                                class="me-2 needs-confirmation"
                                data-confirm-message="Bạn có chắc chắn muốn từ chối cơ sở '{{ $facility->facility_name }}'?">
                                @csrf
                                <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i> Từ chối Yêu
                                    cầu</button>
                            </form>
                            <form action="{{ route('admin.facility.approve', $facility->facility_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="bi bi-check2-circle me-1"></i> Chấp nhận
                                    Hoạt động</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    {{-- JavaScript xác nhận Khóa/Kích hoạt (Giữ nguyên) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const confirmationForms = document.querySelectorAll('.needs-confirmation');
            confirmationForms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    const message = this.getAttribute('data-confirm-message') || 'Bạn có chắc muốn thực hiện hành động này?';
                    if (!confirm(message)) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush