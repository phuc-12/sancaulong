@extends('layouts.manager') {{-- Kế thừa layout Manager --}}

@section('manager_content')
    <h1 class="h3 mb-4">Quản lý Sân Bãi & Lịch Đặt</h1>

    {{-- Hiển thị thông báo thành công (nếu có) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Phần 1: Cập nhật Trạng thái Sân --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Cập nhật Trạng thái Sân</h5>
        </div>
        <div class="card-body">
            @if($courts->isEmpty())
                <p class="text-muted">Chưa có sân nào được thêm vào cơ sở này.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Tên Sân</th>
                                <th class="text-center">Trạng thái hiện tại</th>
                                <th>Hành động (Chọn trạng thái mới & Cập nhật)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Lặp qua danh sách sân con --}}
                            @foreach ($courts as $court)
                            <tr>
                                {{-- Tên Sân --}}
                                <td><strong>{{ $court->court_name }}</strong></td>
                                
                                {{-- Trạng thái hiện tại (dùng Badge) --}}
                                <td class="text-center">
                                    @if($court->status == 'Hoạt động')
                                        <span class="badge bg-success">{{ $court->status }}</span>
                                    @elseif($court->status == 'Bảo trì')
                                        <span class="badge bg-warning text-dark">{{ $court->status }}</span>
                                     @elseif($court->status == 'Đóng cửa')
                                        <span class="badge bg-danger">{{ $court->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $court->status ?? 'Chưa rõ' }}</span>
                                    @endif
                                </td>
                                
                                {{-- Hành động (Form cập nhật) --}}
                                <td>
                                    {{-- Mỗi hàng là một form riêng biệt --}}
                                    <form action="{{ route('manager.courts.updateStatus', $court->court_id) }}" method="POST" class="d-flex align-items-center">
                                        @csrf {{-- Token bảo mật --}}
                                        @method('PUT') {{-- Giả lập phương thức PUT --}}
                                        
                                        {{-- Dropdown chọn trạng thái mới --}}
                                        <select class="form-select form-select-sm w-auto me-2" name="status" required>
                                            {{-- Lặp qua các trạng thái hợp lệ từ Controller --}}
                                            @foreach($validStatuses as $statusOption)
                                                <option value="{{ $statusOption }}" 
                                                        {{ $court->status == $statusOption ? 'selected' : '' }}> 
                                                    {{ $statusOption }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                        {{-- Nút Cập nhật --}}
                                        <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Phần 2: Điều chỉnh Lịch Đặt (Placeholder) --}}
    <div class="card mt-4">
         <div class="card-header">
            <h5 class="mb-0">Điều chỉnh Lịch Đặt</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Khu vực này sẽ chứa một giao diện lịch (giống Google Calendar) để quản lý viên có thể kéo-thả, điều chỉnh các lịch đặt sân hiện có.</p>
            {{-- (Thư viện JS như FullCalendar sẽ được tích hợp ở đây) --}}
        </div>
    </div>
@endsection

@push('scripts')
{{-- Thêm JS riêng cho trang này nếu cần --}}
@endpush