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
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button"
                        role="tab" aria-controls="pricing" aria-selected="false">
                        <i class="bi bi-currency-dollar me-1"></i> Giá Sân
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('owner.facility.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="tab-content" id="facilityTabsContent">
                    {{-- TAB 1: THÔNG TIN CHUNG --}}
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <h5 class="card-title mb-3">Thông tin Cơ sở Kinh Doanh</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="facility_name" class="form-label"><b>Tên đơn vị sân </b><span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="facility_name" name="facility_name"
                                    value="{{ old('facility_name', $facility->facility_name ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label"><b>Số điện thoại liên hệ </b><span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $facility->phone ?? '') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label"><b>Địa chỉ</b><span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $facility->address ?? '') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="open_time" class="form-label"><b>Giờ mở cửa</b> <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="open_time" name="open_time"
                                    value="{{ old('open_time', $facility->open_time ?? '05:00') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="close_time" class="form-label"><b>Giờ đóng cửa</b> <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="close_time" name="close_time"
                                    value="{{ old('close_time', $facility->close_time ?? '22:00') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="quantity_court" class="form-label"><b>Số lượng sân cầu lông</b></label>
                                <input type="number" class="form-control" id="quantity_court" name="quantity_court" min="1"
                                    value="{{ old('quantity_court', $facility->quantity_court ?? 1) }}"
                                    placeholder="Nhập số lượng sân...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label"><b>Mô tả cơ sở</b></label>
                            <textarea class="form-control" id="description" name="description"
                                rows="4">{{ old('description', $facility->description ?? '') }}</textarea>
                            <div class="form-text">Giới thiệu về cơ sở vật chất, số lượng sân, tiện ích...</div>
                        </div>

                        <!-- Thông tin chủ sân -->
                        <h5 class="card-title mt-4 mb-3 border-bottom pb-2">Thông tin Chủ Sở Hữu (Sẽ hiển thị cho Admin duyệt)</h5>
                        @php $ownerUser = Auth::user(); @endphp

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="owner_phone" class="form-label"><b>Số điện thoại Chủ sân</b></label>
                                <input type="tel" class="form-control" id="owner_phone" name="owner_phone"
                                    value="{{ old('owner_phone', $ownerUser->phone ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="owner_cccd" class="form-label"><b>Số CCCD Chủ sân</b></label>
                                <input type="text" class="form-control" id="owner_cccd" name="owner_cccd"
                                    value="{{ old('owner_cccd', $ownerUser->CCCD ?? '') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="owner_address" class="form-label"><b>Địa chỉ liên hệ Chủ sân</b></label>
                            <input type="text" class="form-control" id="owner_address" name="owner_address"
                                value="{{ old('owner_address', $ownerUser->address ?? '') }}">
                        </div>
                        <!-- Thông tin tài khoản ngân hàng -->
                        <h5 class="card-title mt-4 mb-3 border-bottom pb-2">Thông tin Tài Khoản Ngân Hàng</h5>

                        <div class="row">
                            <!-- Số tài khoản -->
                            <div class="col-md-6 mb-3">
                                <label for="account_no" class="form-label"><b>Số tài khoản</b></label>
                                <input type="text" class="form-control" id="account_no" name="account_no"
                                    value="{{ old('account_no', $facility->account_no ?? '') }}"
                                    placeholder="Nhập số tài khoản ngân hàng">
                            </div>

                            <!-- Tên ngân hàng -->
                            <div class="col-md-6 mb-3">
                                <label for="account_bank" class="form-label"><b>Ngân hàng</b></label>
                                <select class="form-select" id="account_bank" name="account_bank" required>
                                    <option value="">-- Chọn ngân hàng --</option>
                                    <option value="VCB" {{ old('account_bank', $facility->account_bank ?? '') == 'VCB' ? 'selected' : '' }}>Vietcombank</option>
                                    <option value="TCB" {{ old('account_bank', $facility->account_bank ?? '') == 'TCB' ? 'selected' : '' }}>Techcombank</option>
                                    <option value="BIDV" {{ old('account_bank', $facility->account_bank ?? '') == 'BIDV' ? 'selected' : '' }}>BIDV</option>
                                    <option value="MB" {{ old('account_bank', $facility->account_bank ?? '') == 'MB' ? 'selected' : '' }}>MB Bank</option>
                                    <option value="ACB" {{ old('account_bank', $facility->account_bank ?? '') == 'ACB' ? 'selected' : '' }}>ACB</option>
                                    <option value="AGRIBANK" {{ old('account_bank', $facility->account_bank ?? '') == 'AGRIBANK' ? 'selected' : '' }}>Agribank</option>
                                    <option value="VPB" {{ old('account_bank', $facility->account_bank ?? '') == 'VPB' ? 'selected' : '' }}>VPBank</option>
                                    <option value="STB" {{ old('account_bank', $facility->account_bank ?? '') == 'STB' ? 'selected' : '' }}>Sacombank</option>
                                    <option value="TPB" {{ old('account_bank', $facility->account_bank ?? '') == 'TPB' ? 'selected' : '' }}>TPBank</option>
                                    <option value="SHB" {{ old('account_bank', $facility->account_bank ?? '') == 'SHB' ? 'selected' : '' }}>SHB</option>
                                </select>
                            </div>
                        </div>

                        <!-- Chủ tài khoản -->
                        <div class="mb-3">
                            <label for="account_name" class="form-label"><b>Tên chủ tài khoản</b></label>
                            <input type="text" class="form-control" id="account_name" name="account_name"
                                value="{{ old('account_name', $facility->account_name ?? $ownerUser->name ?? '') }}"
                                placeholder="Nhập tên chủ tài khoản (không dấu)">
                        </div>

                        <div class="mb-3">
                            <label for="business_license_upload" class="form-label"><b>Giấy phép kinh doanh (PDF, JPG,
                                    PNG)</b></label>
                            <input class="form-control" type="file" id="business_license_upload" name="business_license"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @if(isset($facility) && $facility->business_license)
                                <div class="mt-2">
                                    <a href="{{ asset($facility->business_license) }}" target="_blank">
                                        Xem file hiện tại
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="image_upload" class="form-label"><b>Ảnh đại diện sân (JPG, PNG)</b></label>
                            <input class="form-control" type="file" id="image_upload" name="image"
                                accept="image/jpeg, image/png, image/jpg">
                            @if(isset($facility) && $facility->image)
                                <div class="mt-2">
                                    <img src="{{ asset($facility->image) }}" alt="Ảnh sân" height="200px" width="200px"
                                        class="rounded">
                                </div>
                            @endif
                        </div>

                        <hr>
                        @if(isset($facility) && $facility->status)
                            <p><strong>Trạng thái hiện tại:</strong>
                                @if($facility->status == 'chờ duyệt') <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($facility->status == 'đã duyệt') <span class="badge bg-success">Đã duyệt</span>
                                @elseif($facility->status == 'từ chối') <span class="badge bg-danger">Bị từ chối</span>
                                @else <span class="badge bg-secondary">{{ ucfirst($facility->status) }}</span>
                                @endif
                            </p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0">Sau khi gửi, thông tin sẽ được quản trị viên xem xét và phê
                                duyệt.</p>
                            <button type="button" class="btn btn-outline-primary"
                                onclick="document.getElementById('pricing-tab').click()">
                                Tiếp theo: Giá Sân <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    {{-- TAB 2: GIÁ SÂN --}}
                    <div class="tab-pane fade" id="pricing" role="tabpanel" aria-labelledby="pricing-tab">
                        <h5 class="card-title mb-3">Thiết Lập Giá Sân</h5>
                        <p class="text-muted">Nhập giá cho các khung giờ khác nhau trong ngày</p>

                        {{-- Giá mặc định --}}
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label mb-0">
                                            <i class="bi bi-sun text-warning"></i>
                                            <strong>Giờ mặc định</strong>
                                            <small class="d-block text-muted">05:00 - 16:00</small>
                                        </label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="default_price"
                                                value="{{ old('default_price', $facility->courtPrice->default_price ?? '') }}">
                                            <span class="input-group-text">VNĐ / giờ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Giá cao điểm --}}
                        <div class="card mb-4 border-primary">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label mb-0">
                                            <i class="bi bi-calendar-week text-success"></i>
                                            <strong>Giờ cao điểm</strong>
                                            <small class="d-block text-muted">16:00 - 23:00</small>
                                        </label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="special_price"
                                                value="{{ old('special_price', $facility->courtPrice->special_price ?? '') }}">
                                            <span class="input-group-text">VNĐ / giờ</span>
                                        </div>
                                        <!-- <small class="form-text text-muted">
                                                            <i class="bi bi-info-circle"></i> Giá áp dụng cho cả ngày cuối tuần (nếu để trống sẽ theo giá khung giờ thường)
                                                        </small> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb"></i>
                            <strong>Lưu ý:</strong> Giá áp dụng cho cả ngày cuối tuần
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="document.getElementById('info-tab').click()">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send-check-fill me-2"></i>
                                Gửi Yêu Cầu Đăng Ký / Cập Nhật
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Thêm JS riêng cho trang này nếu cần --}}
@endpush