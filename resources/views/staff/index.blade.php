@extends('layouts.staff')

@section('staff_content')
<h1 class="h3 mb-4 fw-bold">Lịch Đặt Sân Hôm Nay</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3">

    {{-- Bảng danh sách đặt sân --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 fw-semibold">Danh sách đặt sân</h5>
            </div>

            <div class="p-3">
                <form method="POST" action="{{ route('staff.customer.search') }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="search" class="form-control" placeholder="Tìm SĐT hoặc tên khách hàng..." style="max-width: 300px">
                    <button type="submit" class="btn btn-success px-4">Tìm</button>
                </form>
            </div>

            <div class="card-body pt-0">
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
                            @php $index=1 @endphp
                            @isset($invoices)
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $invoice->fullname }}</td>
                                        <td>{{ date('d/m/Y', strtotime($invoice->issue_date)) }}</td>
                                        <td>{{ number_format($invoice->final_amount) }}₫</td>

                                        <td>
                                            @if ($invoice->payment_status === 'Đã thanh toán')
                                                <span class="badge bg-success">Đã thanh toán</span>
                                            @elseif ($invoice->payment_status === 'Chưa thanh toán')
                                                <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                            @elseif ($invoice->payment_status === 'Đã Hủy')
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($invoice->payment_status !== 'Đã Hủy')
                                                <form action="{{ route('chi_tiet_hd') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="facility_id" value="{{ $invoice->facility_id }}">
                                                    <input type="hidden" name="user_id" value="{{ $invoice->customer_id }}">
                                                    <input type="hidden" name="slots" value='@json($mybooking_details[$invoice->invoice_detail_id] ?? [])'>
                                                    <input type="hidden" name="invoice_detail_id" value="{{ $invoice->invoice_detail_id }}">
                                                    <button class="btn btn-sm btn-primary">Chi tiết</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-muted">Chưa có lịch đặt nào</td></tr>
                                @endforelse
                            @else
                                <tr><td colspan="6" class="text-muted">Không có thông tin khách này</td></tr>
                            @endisset
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng hợp đồng lâu dài --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 fw-semibold">Thông tin hợp đồng</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>#</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Tình trạng</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php $index=1 @endphp
                            @isset($invoices)
                                @forelse ($long_term_contracts as $ct)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $ct->fullname }}</td>
                                        <td>{{ date('d/m/Y', strtotime($ct->issue_date)) }}</td>
                                        <td>{{ number_format($ct->final_amount) }}₫</td>

                                        <td>
                                            @if ($ct->payment_status === 'Đã Hủy')
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @elseif ($ct->payment_status === 'Đã sử dụng')
                                                <span class="badge bg-info">Đã sử dụng</span>
                                            @else
                                                <form action="{{ route('chi_tiet_ct') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="invoice_detail_id" value="{{ $ct->invoice_detail_id }}">
                                                    <input type="hidden" name="slots" value='@json($mycontract_details[$ct->invoice_detail_id] ?? [])'>
                                                    <button class="btn btn-sm btn-primary">Chi tiết</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-muted">Chưa có hợp đồng nào</td></tr>
                                @endforelse
                            @else
                                <tr><td colspan="6" class="text-muted">Không có hợp đồng của khách này</td></tr>
                            @endisset
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
