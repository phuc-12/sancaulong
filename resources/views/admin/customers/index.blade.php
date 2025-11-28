@extends('layouts.admin')

@section('customers_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý Khách hàng</h1>

        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                class="form-control form-control-sm me-2"
                placeholder="Tìm theo tên khách hàng...">

            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i> Tìm
            </button>

            @if(!empty($search))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Hiển thị thông báo (Thành công/Lỗi) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Bảng Danh sách Khách hàng --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if($customers->isEmpty())
                <div class="alert alert-secondary text-center mb-0">Hiện chưa có khách hàng nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Ảnh</th>
                                <th>Tên Khách Hàng</th>
                                <th>Email / SĐT</th>
                                <th class="text-center">Trạng Thái</th>
                                <th>Ngày Đăng Ký</th>
                                <th class="text-end">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <img src="{{ $customer->avatar ? asset($customer->avatar) : asset('img/profiles/avatar-default.jpg') }}" 
                                         alt="{{ $customer->fullname }}" class="rounded-circle object-fit-cover" width="40" height="40">
                                </td>
                                <td>{{ $customer->fullname }}</td>
                                <td>
                                    <div>{{ $customer->email }}</div>
                                    @if($customer->phone) <div class="small text-muted"><i class="bi bi-telephone-fill me-1"></i>{{ $customer->phone }}</div> @endif
                                </td>
                                <td class="text-center">
                                    @if($customer->status == 1) <span class="badge bg-success">Hoạt động</span>
                                    @else <span class="badge bg-secondary">Tạm khóa</span>
                                    @endif
                                </td>
                                <td>{{ $customer->created_at ? $customer->created_at->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.customers.edit', $customer->user_id) }}" class="btn btn-sm btn-outline-primary" title="Sửa thông tin">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Phân trang --}}
                <div class="mt-3 d-flex justify-content-end">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection