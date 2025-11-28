@extends('layouts.staff')

@section('staff_content')
<h1 class="h3 mb-4 fw-bold">Lịch Sử Đặt Sân</h1>

<div class="card shadow-sm border-0">

    {{-- Form Tìm kiếm --}}
    <div class="card-body pb-0">
        <form method="POST" action="{{ route('staff.history.search') }}" class="row g-2">
            @csrf
            <div class="col-md-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control"
                    placeholder="Tìm kiếm SĐT hoặc tên khách hàng..."
                >
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100">Tìm kiếm</button>
            </div>
        </form>
    </div>

    <div class="card-header bg-success text-white mt-3 card shadow-sm border-0">
        <h5 class="mb-0 fw-semibold">Lịch sử hóa đơn</h5>
    </div>

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
                            $formattedIssueDate = $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y H:i:s') : '';
                            $formattedBookingDate = $bookingDate ? \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') : '';
                        @endphp

                        <tr class="text-center">
                            <td>{{ ++$index }}</td>
                            <td class="fw-semibold">{{ $invoice->fullname }}</td>
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

@endsection
