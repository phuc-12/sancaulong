@extends('layouts.owner') {{-- Kế thừa layout chung của Owner --}}

@section('owner_content')
    {{-- Tiêu đề và Nút Thêm --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý Nhân Viên & Quản Lý Sân</h1>
        {{-- Nút này gọi JS `prepareAddModal()` để chuẩn bị form Thêm --}}
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staffFormModal"
            onclick="prepareAddModal()">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Thêm Nhân viên/Quản lý
        </button>
    </div>

    {{-- Hiển thị thông báo (Thành công/Lỗi) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- Hiển thị lỗi validation chi tiết hơn --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Đã xảy ra lỗi:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- BẢNG DANH SÁCH NHÂN VIÊN --}}
    <div class="card shadow-sm"> {{-- Thêm shadow nhẹ --}}
        <div class="card-body">
            @if($staffMembers->isEmpty())
                <div class="alert alert-secondary text-center mb-0">Hiện chưa có nhân viên hoặc quản lý nào được thêm vào cơ sở
                    này.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0"> {{-- Bỏ margin bottom --}}
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Ảnh</th>
                                <th>Tên</th>
                                <th>Email / SĐT</th>
                                <th>Vai trò</th>
                                <th class="text-center">Trạng Thái</th>
                                <th>Quyền Chi Tiết</th>
                                <th class="text-end" style="width: 150px;">Hành Động</th> {{-- Tăng chiều rộng cột hành động
                                --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($staffMembers as $staff)
                                <tr>
                                    {{-- Ảnh đại diện --}}
                                    <td>
                                        <img src="{{ $staff->avatar ? asset($staff->avatar) : asset('img/profiles/avatar-default.jpg') }}"
                                            {{-- Thêm ảnh mặc định --}} alt="{{ $staff->fullname }}"
                                            class="rounded-circle object-fit-cover" width="40" height="40"> {{-- Thêm
                                        object-fit-cover --}}
                                    </td>
                                    {{-- Tên --}}
                                    <td>{{ $staff->fullname }}</td>
                                    {{-- Email / SĐT --}}
                                    <td>
                                        <div>{{ $staff->email }}</div>
                                        @if($staff->phone)
                                            <div class="small text-muted"><i class="bi bi-telephone-fill me-1"></i>{{ $staff->phone }}
                                        </div> @endif
                                        {{-- Hiển thị địa chỉ nếu cần --}}
                                        {{-- @if($staff->address) <div class="small text-muted"><i
                                                class="bi bi-geo-alt-fill me-1"></i>{{ $staff->address }}</div> @endif --}}
                                    </td>
                                    {{-- Vai trò --}}
                                    <td>
                                        @if($staff->role_id == 4) <span class="badge bg-primary">Quản lý</span>
                                        @elseif($staff->role_id == 3) <span class="badge bg-secondary">Nhân viên</span>
                                        @else <span class="badge bg-light text-dark">Khác</span>
                                        @endif
                                    </td>
                                    {{-- Trạng thái --}}
                                    <td class="text-center">
                                        @if($staff->status == 1) <span class="badge bg-success">Hoạt động</span>
                                        @else <span class="badge bg-secondary">Tạm khóa</span>
                                        @endif
                                    </td>
                                    {{-- Quyền chi tiết --}}
                                    <td>
                                        @if(!empty($staff->permissions))
                                            @foreach($staff->permissions as $permission)
                                                <span class="badge bg-info me-1" style="font-size: 0.75em;"> {{-- Thu nhỏ badge quyền --}}
                                                    @if($permission == 'manage_bookings') <i class="bi bi-calendar-check me-1"></i> QL Đặt
                                                        Sân
                                                    @elseif($permission == 'view_reports') <i class="bi bi-graph-up me-1"></i> Xem Tài chính
                                                        {{-- Thêm icon và tên rút gọn cho các quyền khác --}}
                                                    @else {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                                    @endif
                                                </span>
                                            @endforeach
                                        @else <span class="small text-muted fst-italic">Không có quyền cụ thể</span>
                                        @endif
                                    </td>
                                    {{-- Hành động --}}
                                    <td class="text-end">
                                        {{-- Nút Sửa --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" title="Sửa thông tin"
                                            data-bs-toggle="modal" data-bs-target="#staffFormModal" {{-- Truyền toàn bộ object staff
                                            (đã bao gồm permissions) vào JS --}} onclick='prepareEditModal(@json($staff))'>
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        {{-- Nút Xóa --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa nhân viên/quản lý"
                                            data-bs-toggle="modal" data-bs-target="#deleteStaffModal"
                                            onclick="prepareDeleteModal({{ $staff->user_id }}, '{{ $staff->fullname }}')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="staffFormModal" tabindex="-1" aria-labelledby="staffFormModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staffFormModalLabel">Thêm Nhân Viên Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Form action và method sẽ được JS cập nhật --}}
                <form id="staffForm" action="{{ route('owner.staff.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="staffFormMethod" value="POST"> {{-- Giả lập PUT khi sửa --}}

                    <div class="modal-body">
                        {{-- Tên và Email --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullname" class="form-label">Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required
                                    value="{{ old('fullname') }}">
                                @error('fullname') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror {{--
                                Hiện lỗi dưới input --}}
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Mật khẩu --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mật khẩu <span class="text-danger"
                                        id="passwordRequired">*</span></label>
                                <input type="password" class="form-control" id="password" name="password"
                                    aria-describedby="passwordHelp">
                                <div class="form-text" id="passwordHelp">Ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số. Bỏ
                                    trống khi sửa nếu không muốn đổi.</div>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3" id="passwordConfirmationRow">
                                <label for="password_confirmation" class="form-label">Xác nhận Mật khẩu <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                                @error('password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SĐT và Địa chỉ --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ old('address') }}">
                                @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Ảnh đại diện --}}
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Ảnh đại diện (Tối đa 1MB)</label>
                            <input class="form-control" type="file" id="avatar" name="avatar"
                                accept="image/png, image/jpeg, image/jpg">
                            <img id="avatarPreview" src="#" alt="Xem trước" class="mt-2 rounded"
                                style="max-height: 80px; display: none;">
                            @error('avatar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Trạng thái (chỉ hiện khi sửa) --}}
                        <div class="mb-3" id="statusRow" style="display: none;">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusActive" value="1"
                                        checked>
                                    <label class="form-check-label" for="statusActive">Hoạt động</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusInactive"
                                        value="0">
                                    <label class="form-check-label" for="statusInactive">Tạm khóa</label>
                                </div>
                            </div>
                            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <hr>

                        {{-- Vai trò --}}
                        <div class="mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="role_id" id="roleStaff" value="3" {{ old('role_id', '3') == '3' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="roleStaff">Nhân viên</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="role_id" id="roleManager" value="4"
                                        {{ old('role_id') == '4' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="roleManager">Quản lý sân</label>
                                </div>
                            </div>
                            @error('role_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Phân Quyền Chi Tiết --}}
                        <h6 class="mb-2">Phân Quyền Chi Tiết (Tùy chọn)</h6>
                        <div class="form-check mb-2">
                            @php $oldPerms = old('permissions', []); @endphp
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_bookings"
                                id="perm_bookings" {{ in_array('manage_bookings', $oldPerms) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_bookings">
                                <b>Quản lý Đặt Sân</b> (Xem lịch, xác nhận, hủy lịch)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="view_reports"
                                id="perm_reports" {{ in_array('view_reports', $oldPerms) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_reports">
                                <b>Xem Tài chính</b> (Xem doanh thu, xem hóa đơn)
                            </label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="saveStaffButton">Lưu Thông Tin</button> 
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteStaffModal" tabindex="-1" aria-labelledby="deleteStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteStaffModalLabel">Xác nhận Xóa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa nhân viên/quản lý <strong id="staffNameToDelete"></strong>? <br> Hành động này
                    không thể hoàn tác.
                </div>
                <div class="modal-footer">
                    {{-- Form ẩn để gửi yêu cầu DELETE --}}
                    <form id="deleteStaffForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Đồng ý Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Lấy các element cần thiết từ DOM
        const staffFormModalElement = document.getElementById('staffFormModal');
        const staffFormModal = new bootstrap.Modal(staffFormModalElement);
        const staffForm = document.getElementById('staffForm');
        const modalTitle = document.getElementById('staffFormModalLabel');
        const formMethodInput = document.getElementById('staffFormMethod');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const passwordConfirmRow = document.getElementById('passwordConfirmationRow');
        const passwordRequiredSpan = document.getElementById('passwordRequired');
        const passwordHelpText = document.getElementById('passwordHelp');
        const statusRow = document.getElementById('statusRow');
        const avatarPreview = document.getElementById('avatarPreview');
        const avatarInput = document.getElementById('avatar');
        const saveStaffButton = document.getElementById('saveStaffButton'); // Nút Lưu

        // Hàm chuẩn bị modal cho việc THÊM MỚI
        function prepareAddModal() {
            staffForm.reset(); // Xóa sạch dữ liệu cũ trên form
            modalTitle.innerText = 'Thêm Nhân Viên / Quản Lý Mới'; 
            staffForm.action = '{{ route("owner.staff.store") }}';
            formMethodInput.value = 'POST'; 
            passwordInput.required = true; 
            passwordConfirmInput.required = true; 
            passwordConfirmRow.style.display = 'block'; 
            passwordRequiredSpan.style.display = 'inline'; 
            passwordHelpText.innerText = 'Ít nhất 8 ký tự';
            statusRow.style.display = 'none'; // Ẩn trạng thái (mặc định active trên server)
            avatarPreview.style.display = 'none'; 
            avatarInput.value = ''; // Xóa file đã chọn (nếu có)
            saveStaffButton.innerText = 'Thêm Mới';

            // Bỏ check tất cả quyền
            document.querySelectorAll('#staffForm input[name="permissions[]"]').forEach(checkbox => checkbox.checked = false);
            // Mặc định chọn vai trò Nhân viên
            document.getElementById('roleStaff').checked = true;
        }

        // Hàm chuẩn bị modal cho việc SỬA
        function prepareEditModal(staffData) {
            staffForm.reset(); // Xóa sạch dữ liệu cũ
            modalTitle.innerText = 'Sửa Thông Tin: ' + staffData.fullname; 
           
            let updateUrl = '{{ route("owner.staff.update", ["staff" => ":staff"]) }}';
            staffForm.action = updateUrl.replace(':staff', staffData.user_id);
            formMethodInput.value = 'PUT'; 
            passwordInput.required = false; 
            passwordConfirmInput.required = false; 
            // passwordConfirmRow.style.display = 'none'; 
            passwordRequiredSpan.style.display = 'none';
            passwordHelpText.innerText = 'Bỏ trống nếu không muốn thay đổi mật khẩu.';
            statusRow.style.display = 'block'; // Hiện lựa chọn trạng thái
            saveStaffButton.innerText = 'Lưu Thay Đổi'; // Đổi text nút submit

            // Điền dữ liệu hiện tại của nhân viên vào form
            document.getElementById('fullname').value = staffData.fullname || '';
            document.getElementById('email').value = staffData.email || '';
            document.getElementById('phone').value = staffData.phone || '';
            document.getElementById('address').value = staffData.address || '';

            // Chọn trạng thái hiện tại
            if (staffData.status == 1) {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }

            // Chọn vai trò hiện tại
            if (staffData.role_id == 4) {
                document.getElementById('roleManager').checked = true;
            } else {
                document.getElementById('roleStaff').checked = true; // Mặc định là Staff
            }

            // Hiển thị ảnh đại diện hiện tại (nếu có)
            if (staffData.avatar) {
                avatarPreview.src = '{{ asset('') }}' + staffData.avatar; // Dùng asset() để lấy URL đúng
                avatarPreview.style.display = 'block';
            } else {
                avatarPreview.style.display = 'none';
            }
            avatarInput.value = ''; // Xóa lựa chọn file cũ

            // Check các quyền hiện có
            document.querySelectorAll('#staffForm input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = Array.isArray(staffData.permissions) && staffData.permissions.includes(checkbox.value);
            });
        }

        // Hàm chuẩn bị modal XÓA
        const deleteStaffForm = document.getElementById('deleteStaffForm');
        const staffNameToDelete = document.getElementById('staffNameToDelete');
        function prepareDeleteModal(staffId, staffName) {
            // Tạo URL cho route destroy
            let deleteUrl = '{{ route("owner.staff.destroy", ["staff" => ":staff"]) }}';
            deleteStaffForm.action = deleteUrl.replace(':staff', staffId);
            staffNameToDelete.innerText = staffName; // Hiển thị tên trong thông báo xác nhận
        }

        // Hiển thị ảnh preview khi người dùng chọn file mới
        avatarInput.addEventListener('change', function (evt) {
            const [file] = avatarInput.files;
            if (file) {
                avatarPreview.src = URL.createObjectURL(file); // Tạo URL tạm thời cho ảnh mới chọn
                avatarPreview.style.display = 'block';
            } else {
                avatarPreview.style.display = 'none'; // Ẩn nếu không chọn file
            }
        });

        // Xử lý khi có lỗi validation: Tự động mở lại modal và điền lại dữ liệu cũ
        @if ($errors->any())
            // Kiểm tra xem lỗi này là của form Thêm hay Sửa
            // Dựa vào phương thức (_method) hoặc sự tồn tại của ID nhân viên (nếu có)
            var isEditMode = '{{ old("_method") }}' === 'PUT';

            // Mở lại modal
            staffFormModal.show();

            if (isEditMode) {
                modalTitle.innerText = 'Sửa Thông Tin Nhân Viên - Có lỗi xảy ra';

                console.error("Lỗi validation ở chế độ sửa. Cần thêm logic điền lại dữ liệu 'old()' và action.");
                
                let failedEditAction = staffForm.action; // Lấy action URL đã được đặt bởi prepareEditModal trước đó
                formMethodInput.value = 'PUT';
                saveStaffButton.innerText = 'Thử Lưu Lại';
                statusRow.style.display = 'block'; // Đảm bảo trạng thái hiện
            } else {
                // Nếu là lỗi từ form THÊM
                modalTitle.innerText = 'Thêm Nhân Viên Mới - Có lỗi xảy ra';
                staffForm.action = '{{ route("owner.staff.store") }}';
                formMethodInput.value = 'POST';
                passwordInput.required = true; // Mật khẩu vẫn bắt buộc
                passwordConfirmInput.required = true;
                passwordRequiredSpan.style.display = 'inline';
                statusRow.style.display = 'none';
                saveStaffButton.innerText = 'Thử Thêm Lại';
                // (Code điền lại giá trị old() và check permissions đã có sẵn trong HTML modal)
            }
        @endif

    </script>
@endpush