@extends('layouts.owner')

@section('owner_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý Nhân Viên</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Thêm Nhân viên mới
        </button>
    </div>

    {{-- BẢNG DANH SÁCH NHÂN VIÊN --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tên Nhân Viên</th>
                        <th>Email</th>
                        <th>Quyền (Vai trò)</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nguyễn Văn A</td>
                        <td>nva@gmail.com</td>
                        <td><span class="badge bg-primary">Quản lý</span></td>
                        <td><span class="badge bg-success">Hoạt động</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">Sửa</tr>
                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStaffModalLabel">Thêm/Sửa Nhân Viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{-- route('owner.staff.store') --}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="staff_name" class="form-label">Tên nhân viên</label>
                                <input type="text" class="form-control" id="staff_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="staff_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="staff_email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="staff_password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="staff_password" name="password">
                            <div class="form-text">Bỏ trống nếu không muốn thay đổi mật khẩu.</div>
                        </div>
                        <hr>
                        <h6 class="mb-3">Phân Quyền</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_bookings" id="perm_bookings">
                            <label class="form-check-label" for="perm_bookings">
                                <b>Quản lý Đặt Sân</b> (Xem lịch, xác nhận, hủy lịch)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="view_reports" id="perm_reports">
                            <label class="form-check-label" for="perm_reports">
                                <b>Xem Tài chính</b> (Xem doanh thu, xem hóa đơn)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu Nhân Viên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection