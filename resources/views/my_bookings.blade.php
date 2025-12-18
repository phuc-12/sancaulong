@extends('layouts.main')

@section('my_bookings_content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<section class="breadcrumb breadcrumb-list mb-0">
    <span class="primary-right-round"></span>
    <div class="container">
        <h1 class="text-white">Lịch Sử Giao Dịch</h1>
        <ul>
            <li><a href="/">Trang Chủ</a></li>
            <li>Lịch Sử Giao Dịch</li>
        </ul>
    </div>
</section>

<div class="content court-bg py-4">
    <div class="container">

        <!-- Card Wrapper -->
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-success text-white py-3">
                <h4 class="mb-0">Giao Dịch Của Bạn</h4>
                <p class="mb-0 text-light">Theo dõi và quản lý các sân đã hoàn thành của bạn</p>
            </div>

            <div class="card-body p-4">

                @if($success_message)
                    <div class="alert alert-danger">
                        {{ $success_message }}
                    </div>
                @endif

                <div class="table-responsive rounded-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form action="{{ route('lich_dat_san') }}" method="GET" class="d-flex" onsubmit="return validateDate()">
                            <input type="hidden" name="user_id" value="{{ $user_id }}">

                            <!-- Ô tìm kiếm tên sân -->
                            <input type="text" name="facility_name" value="{{ request('facility_name') }}" 
                                class="form-control me-2" 
                                placeholder="Tìm theo tên sân">

                            <!-- Ô tìm kiếm ngày -->
                            <input type="text" name="booking_date" value="{{ request('booking_date') }}" 
                                class="form-control me-2" 
                                placeholder="Tìm theo ngày dd/mm/yyyy (09/02/2025)" id="bookingDateInput">

                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </form>

                    </div>

<script>
function validateDate() {
    const dateInput = document.getElementById('bookingDateInput').value.trim();
    if (!dateInput) return true;

    const dateRegex = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/;

    if (!dateRegex.test(dateInput)) {
        Swal.fire({
            icon: 'error',
            title: 'Ngày không hợp lệ',
            text: 'Vui lòng nhập đúng định dạng: dd/mm/yyyy (ví dụ: 23/11/2025)',
            confirmButtonText: 'Đã hiểu',
            confirmButtonColor: '#d33',
        });
        return false;
    }

    return true;
}
</script>
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>STT</th>
                                <th>Tên sân</th>
                                {{-- <th>Khách hàng</th> --}}
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Ngày áp dụng</th>
                                <th>Sử dụng</th>
                                <th>Tình trạng</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php 
                                // Tính số thứ tự dựa trên trang hiện tại
                                $index = ($invoices->currentPage() - 1) * $invoices->perPage();
                            @endphp

                            @forelse ($invoices as $invoice)
                                @php
                                    $firstBooking = $mybooking_details[$invoice->invoice_detail_id]->first() ?? null;
                                    $bookingDate = $firstBooking->booking_date ?? null;
                                    $isExpired = $bookingDate ? \Carbon\Carbon::parse($bookingDate)->lt(\Carbon\Carbon::today()) : false;

                                    // Format ngày
                                    $formattedIssueDate = $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') : '';
                                    $formattedBookingDate = $bookingDate ? \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') : '';
                                @endphp

                                <tr class="text-center">
                                    <td>{{ ++$index }}</td>
                                    <td class="fw-semibold">{{ $invoice->facility_name }}</td>
                                    {{-- <td>{{ $invoice->fullname }}</td> --}}
                                    <td>{{ $formattedIssueDate }}</td>
                                    <td class="fw-bold text-success">{{ number_format($invoice->final_amount, 0, ',', '.') }}₫</td>
                                    <td>{{ $formattedBookingDate }}</td>
                                    <td>
                                        @if($isExpired)
                                            <span class="badge bg-warning text-dark">Đã quá hạn</span>
                                        @else
                                            <span class="badge bg-info text-dark">Chưa sử dụng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('chi_tiet_hd') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $invoice->facility_id }}">
                                            <input type="hidden" name="user_id" value="{{ $invoice->customer_id }}">
                                            <input type="hidden" name="slots" value='@json($mybooking_details[$invoice->invoice_detail_id] ?? [])'>
                                            <input type="hidden" name="invoice_detail_id" value="{{ $invoice->invoice_detail_id }}">

                                            @if ($invoice->payment_status === 'Đã Hủy')
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @elseif ($invoice->payment_status === 'Đã sử dụng')
                                                <span class="badge bg-primary">Đã sử dụng</span>
                                            @else
                                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">Chi tiết</button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                        Chưa có lịch đặt nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

				<!-- Pagination -->
				<div class="mt-4 d-flex justify-content-center">
					{{ $invoices->links('vendor.pagination.bootstrap-5') }}
				</div>
            </div>
        </div>
    </div>
</div>
@endsection
