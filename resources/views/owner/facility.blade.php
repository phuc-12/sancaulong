@extends('layouts.owner') {{-- Kế thừa layout của Owner --}}

@section('owner_content')
    <h1 class="h3 mb-4">Đăng Ký / Cập Nhật Cơ Sở Sân</h1>

    {{-- Hiển thị thông báo thành công (nếu có) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Hiển thị lỗi validation (nếu có) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="facilityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button"
                        role="tab" aria-controls="info" aria-selected="true">
                        <i class="bi bi-info-circle-fill me-1"></i> Thông Tin Chung
                    </button>
                </li>
                {{-- Thêm các tab khác (Sân con & Giá, Hình ảnh) ở đây nếu cần --}}
            </ul>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('owner.facility.store') }}" method="POST" enctype="multipart/form-data"> {{-- enctype để
                upload file --}}
                @csrf
                <div class="tab-content" id="facilityTabsContent">
                    {{-- TAB 1: THÔNG TIN CHUNG --}}
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <h5 class="card-title mb-3">Thông tin Cơ sở Kinh Doanh</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="facility_name" class="form-label">Tên đơn vị sân <span
                                        class="text-danger">*</span></label>
                                {{-- Ưu tiên giá trị cũ (nếu validation lỗi), sau đó mới lấy giá trị từ CSDL (nếu có) --}}
                                <input type="text" class="form-control" id="facility_name" name="facility_name"
                                    value="{{ old('facility_name', $facility->facility_name ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số điện thoại liên hệ <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $facility->phone ?? '') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $facility->address ?? '') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="open_time" class="form-label">Giờ mở cửa <span
                                        class="text-danger">*</span></label>
                                {{-- Mặc định là 05:00 nếu không có giá trị cũ hoặc CSDL --}}
                                <input type="time" class="form-control" id="open_time" name="open_time"
                                    value="{{ old('open_time', $facility->open_time ?? '05:00') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="close_time" class="form-label">Giờ đóng cửa <span
                                        class="text-danger">*</span></label>
                                {{-- Mặc định là 22:00 --}}
                                <input type="time" class="form-control" id="close_time" name="close_time"
                                    value="{{ old('close_time', $facility->close_time ?? '22:00') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả cơ sở</label>
                            <textarea class="form-control" id="description" name="description"
                                rows="4">{{ old('description', $facility->description ?? '') }}</textarea>
                            <div class="form-text">Giới thiệu về cơ sở vật chất, số lượng sân, tiện ích...</div>
                        </div>

                        <h5 class="card-title mt-4 mb-3 border-bottom pb-2">Thông tin Chủ Sở Hữu (Sẽ hiển thị cho Admin duyệt)</h5>
                        {{-- Lấy thông tin từ Auth::user() để điền vào value --}}
                        @php $ownerUser = Auth::user(); @endphp 

                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="owner_phone" class="form-label">Số điện thoại Chủ sân</label>
                                <input type="tel" class="form-control" id="owner_phone" name="owner_phone" 
                                       value="{{ old('owner_phone', $ownerUser->phone ?? '') }}">
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="owner_cccd" class="form-label">Số CCCD Chủ sân</label>
                                <input type="text" class="form-control" id="owner_cccd" name="owner_cccd" 
                                       value="{{ old('owner_cccd', $ownerUser->CCCD ?? '') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="owner_address" class="form-label">Địa chỉ liên hệ Chủ sân</label>
                            <input type="text" class="form-control" id="owner_address" name="owner_address" 
                                   value="{{ old('owner_address', $ownerUser->address ?? '') }}">
                        </div>
                        <div class="mb-4">
                            <label for="business_license" class="form-label">Giấy phép kinh doanh (PDF, JPG, PNG)</label>
                            <input class="form-control" type="file" id="business_license" name="business_license"
                                accept=".pdf,.jpg,.jpeg,.png">
                            {{-- Hiển thị link file hiện tại nếu đang cập nhật và đã có file --}}
                            @if(isset($facility) && $facility->business_license_path)
                                <div class="mt-2">
                                    File hiện tại:
                                    {{-- Storage::url() tạo đường dẫn công khai tới file trong storage/app/public --}}
                                    <a href="{{ Storage::url($facility->business_license_path) }}" target="_blank">
                                        Xem file
                                        <i class="bi bi-box-arrow-up-right small"></i>
                                    </a>
                                    <small class="text-muted">(Tải file mới sẽ thay thế file này)</small>
                                </div>
                            @endif
                        </div>

                        <hr>
                        {{-- Hiển thị trạng thái hiện tại của cơ sở (nếu có) --}}
                        @if(isset($facility) && $facility->status)
                            <p><strong>Trạng thái hiện tại:</strong>
                                @if($facility->status == 'chờ duyệt') <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($facility->status == 'đã duyệt') <span class="badge bg-success">Đã duyệt</span>
                                @elseif($facility->status == 'từ chối') <span class="badge bg-danger">Bị từ chối</span>
                                @else <span class="badge bg-secondary">{{ ucfirst($facility->status) }}</span> {{-- Hiển thị các
                                    trạng thái khác nếu có --}}
                                @endif
                            </p>
                        @endif
                        <p class="text-muted small">Sau khi gửi, thông tin sẽ được quản trị viên xem xét và phê duyệt.</p>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send-check-fill me-2"></i>
                            Gửi Yêu Cầu Đăng Ký / Cập Nhật
                        </button>
                    </div> {{-- Kết thúc Tab 1 --}}

                    {{-- Thêm các tab khác ở đây nếu cần --}}

                </div> {{-- Kết thúc Tab Content --}}
            </form>
        </div> {{-- Kết thúc Card Body --}}
    </div> {{-- Kết thúc Card --}}
@endsection

@push('scripts')
    {{-- Thêm JS riêng cho trang này nếu cần --}}
@endpush