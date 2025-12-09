@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4 fw-bold">Lịch Đặt Sân Hôm Nay</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(isset($facilityName))
        <div class="facility-info">
            <div class="facility-name">
                <i class="bi bi-building"></i>
                <b>{{ $facilityName }}</b>
            </div>
        </div>
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
                        <input type="text" name="search" class="form-control" placeholder="Tìm SĐT hoặc tên khách hàng..."
                            style="max-width: 300px">
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
                                @php $index = 1 @endphp
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
                                                    <form action="{{ route('staff.chi_tiet_hd_nv') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="facility_id" value="{{ $invoice->facility_id }}">
                                                        <input type="hidden" name="user_id" value="{{ $invoice->customer_id }}">
                                                        <input type="hidden" name="slots"
                                                            value='@json($mybooking_details[$invoice->invoice_detail_id] ?? [])'>
                                                        <input type="hidden" name="invoice_detail_id"
                                                            value="{{ $invoice->invoice_detail_id }}">
                                                        <input type="hidden" name="invoice_id" value="{{ $invoice->invoice_id }}">
                                                        <button class="btn btn-sm btn-primary">Chi tiết</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-muted">Chưa có lịch đặt nào</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="6" class="text-muted">Không có thông tin khách này</td>
                                    </tr>
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
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên sân</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Sử dụng</th>
                                    <th>Tình trạng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 0; @endphp
                                @forelse ($long_term_contracts as $ct)
                                    @php
                                        // Kiểm tra xem invoice_detail_id có tồn tại trong mảng chi tiết không
                                        $details = isset($mycontract_details[$ct->invoice_detail_id]) ? $mycontract_details[$ct->invoice_detail_id] : null;
                                        $firstBooking = ($details && count($details)) ? $details->first() : null;
                                        $bookingDate = $firstBooking->booking_date ?? null;
                                        $isExpired = $bookingDate ? \Carbon\Carbon::parse($bookingDate)->lt(\Carbon\Carbon::today()) : false;
                                    @endphp

                                    <tr class="text-center">
                                        <td>{{ ++$index }}</td>
                                        <td class="fw-semibold">{{ $ct->facility_name ?? '---' }}</td>
                                        <td>{{ $ct->fullname ?? '---' }}</td>
                                        <td>{{ $ct->issue_date ? \Carbon\Carbon::parse($ct->issue_date)->format('d/m/Y H:i:s') : '---' }}
                                        </td>
                                        <td class="fw-bold text-success">
                                            {{ $ct->final_amount ? number_format($ct->final_amount, 0, ',', '.') . '₫' : '---' }}
                                        </td>
                                        <td>{{ $bookingDate ? \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') : '---' }}
                                        </td>
                                        <td>
                                            @if($isExpired)
                                                <span class="badge bg-warning text-dark">Đã quá hạn</span>
                                            @else
                                                <span class="badge bg-info text-dark">Chưa sử dụng</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ct->payment_status === 'Đã Hủy')
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @elseif ($ct->payment_status === 'Đã sử dụng')
                                                <span class="badge bg-primary">Đã sử dụng</span>
                                            @else
                                                <form action="{{ route('chi_tiet_ct') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="invoice_detail_id"
                                                        value="{{ $ct->invoice_detail_id }}">
                                                    <input type="hidden" name="user_id" value="{{ $ct->customer_id }}">
                                                    <input type="hidden" name="slots" value='@json($details ?? [])'>
                                                    <button type="submit"
                                                        class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">Chi tiết</button>
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
                        <!-- Phân trang -->
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $long_term_contracts->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection