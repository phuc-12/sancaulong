@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4">Lịch Đặt Sân Hôm Nay</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm"  style="float: left; width: 45%;">
        <form method="POST" action="{{ route("staff.customer.search") }}">
        @csrf
            <div>
                <input type="text" name="search" placeholder="Tìm kiếm SĐT hoặc tên khách hàng..."
                    value="" class="form-control" style="margin:10px 0; width:300px; float: left">
                <button type="submit" class="btn btn-success" style="float: left; margin: 10px;">Tìm kiếm</button>
            </div>
        </form>
        <div class="card-header">
            <h5 class="mb-0">Danh sách</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Tình trạng</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index=0 @endphp
                                    @isset($invoices)
                                        @forelse ($invoices as $invoice)
                                            <tr>
                                                <td>{{ $index+=1 }}</td>
                                                <td>{{ $invoice->fullname }}</td>
                                                <td>{{ date('d/m/Y', strtotime($invoice->issue_date)) }}</td>
                                                <td>{{ $invoice->final_amount }}</td>
                                                <td>
                                                    @if ($invoice->payment_status === 'Đã thanh toán')
                                                        <p class="text-primary pt-3">Đã thanh toán</p>
                                                    @elseif ($invoice->payment_status === 'Chưa thanh toán')
                                                        <p class="text-primary pt-3">Chưa thanh toán</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('chi_tiet_hd') }}" method="POST">
                                                    @csrf
                                                        <input type="hidden" name="facility_id" value="{{ $invoice->facility_id }}">
                                                        <input type="hidden" name="user_id" value="{{ $invoice->customer_id }}">
                                                        <input type="hidden" name="slots" value='@json($mybooking_details[$invoice->invoice_detail_id] ?? [])'>
                                                        <input type="hidden" name="invoice_detail_id" value="{{ $invoice->invoice_detail_id }}">
                                                        <input type="hidden" name="invoices" value="{{ $invoices }}">
                                                        @if ($invoice->payment_status === 'Đã Hủy')
                                                            <p class="text-danger pt-3">Đã hủy</p>
                                                        @else 
                                                            <button type="submit" class="btn btn-success">
                                                                Chi tiết
                                                            </button>
                                                        @endif
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Chưa có lịch đặt nào
                                                </td>
                                            </tr>
                                        @endforelse
                                    @else
                                        <tr><td colspan="6" class="text-center text-muted">Không có thông tin khách hàng này</td></tr>
                                    @endisset
                                </tbody>
                            </table>
                    </div>

            </div>
        </div>
        
        
    </div>
    <div class="card shadow-sm" style="float: right; width: 50%">
                 <div class="card-header"><h5 class="mb-0">Thông tin hợp đồng</h5></div>
                    <div class="card-body" style="padding: 0;">
                    <div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Tình trạng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index=0 @endphp
                                @isset($invoices)
                                    @forelse ($long_term_contracts as $ct)
                                        <tr>
                                            <td>{{ $index+=1 }}</td>
                                            <td>{{ $ct->fullname }}</td>
                                            <td>{{ date('d/m/Y', strtotime($ct->issue_date)) }}</td>
                                            <td>{{ $ct->final_amount }}</td>
                                            <td>
                                                <form action="{{ route('chi_tiet_ct') }}" method="POST">
                                                @csrf
                                                    <input type="hidden" name="invoice_detail_id" value="{{ $ct->invoice_detail_id }}">
                                                    <input type="hidden" name="slots" value='@json($mycontract_details[$ct->invoice_detail_id] ?? [])'>
                                                    @if ($ct->payment_status === 'Đã Hủy')
                                                        <p class="text-danger pt-3">Đã hủy</p>
                                                    @elseif ($ct->payment_status === 'Đã sử dụng')
                                                        <p class="text-primary pt-3">Đã sử dụng</p>
                                                    @else 
                                                        <button type="submit" class="btn btn-success">
                                                            Chi tiết
                                                        </button>
                                                    @endif
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                Chưa có lịch đặt nào
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr><td colspan="6" class="text-center text-muted">Không có hợp đồng của khách này</td></tr>
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    
@endsection