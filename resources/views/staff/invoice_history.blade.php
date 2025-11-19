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
                        <th>Tình trạng</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    @php $index = 1; @endphp

                    @isset($invoices)
                        @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $index++ }}</td>
                            <td>{{ $invoice->fullname }}</td>
                            <td>{{ date('d/m/Y', strtotime($invoice->issue_date)) }}</td>
                            <td>{{ number_format($invoice->final_amount) }}₫</td>

                            <td>
                                @if ($invoice->payment_status === 'Đã Hủy')
                                    <span class="badge bg-danger px-3 py-2">Đã hủy</span>
                                @elseif ($invoice->payment_status === 'Đã thanh toán')
                                    <span class="badge bg-success px-3 py-2">Đã thanh toán</span>
                                @else
                                    <span class="badge bg-warning text-dark px-3 py-2">Chưa thanh toán</span>
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
                                <td colspan="6" class="text-muted py-3">Không có lịch đặt nào</td>
                            </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="6" class="text-muted">Không có thông tin khách hàng</td>
                        </tr>
                    @endisset
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
