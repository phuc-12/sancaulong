@extends('layouts.manager')

@section('manager_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý Hợp đồng Dài hạn</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContractModal">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Tạo Hợp đồng mới
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Khung giờ cố định</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- (Dùng @foreach để lặp qua $contracts) --}}
                    <tr>
                        <td>Nguyễn Văn A</td>
                        <td>Sân 2 (T3, T5 18:00-19:00)</td>
                        <td>01/10/2025</td>
                        <td>01/01/2026</td>
                        <td><span class="badge bg-success">Đang hoạt động</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">Sửa</button>
                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- (Thêm Modal "Tạo Hợp đồng mới) --}}
@endsection