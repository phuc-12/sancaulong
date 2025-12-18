@extends('layouts.staff')

@section('staff_content')
<h1 class="h3 mb-4 fw-bold">Thanh Toán Tại Quầy & In Hóa Đơn</h1>

{{-- Thông báo --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
    </div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="p-3">
                <form method="POST" action="{{ route('staff.invoice.search') }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="search" class="form-control" placeholder="Tìm SĐT hoặc tên khách hàng..." style="max-width: 300px">
                    <button type="submit" class="btn btn-success px-4">Tìm</button>
                </form>
            </div>
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 fw-semibold">Danh sách hóa đơn</h5>
            </div>
            @if(session('success_message'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- @if(session('error_message'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif --}}
            <div class="card-body">

                {{-- Bảng danh sách --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>#</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Ngày áp dụng</th>
                                <th>Sử dụng</th>
                                <th>Tình trạng</th>
                                <th></th>
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
                                    @if ($invoice->payment_status === 'Đã Hủy')
                                        <span class="text-danger fw-semibold">Đã hủy</span>
                                    @else
                                        <form action="{{ route('staff.chi_tiet_hd_nv') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $invoice->facility_id }}">
                                            <input type="hidden" name="user_id" value="{{ $invoice->customer_id }}">
                                            <input type="hidden" name="slots" value='@json($mybooking_details[$invoice->invoice_detail_id] ?? [])'>
                                            <input type="hidden" name="invoice_detail_id" value="{{ $invoice->invoice_detail_id }}">
                                            <input type="hidden" name="invoice_id" value="{{ $invoice->invoice_id }}">
                                            <button class="btn btn-sm btn-primary px-3">Chi tiết</button>
                                        </form>
                                    @endif
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
                    <!-- Pagination -->
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $invoices->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
