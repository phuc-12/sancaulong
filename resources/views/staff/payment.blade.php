@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4">Thanh Toán Tại Quầy & In Hóa Đơn</h1>

    {{-- Hiển thị thông báo (Thành công/Lỗi) --}}
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
        {{-- Cột trái: Thông tin & Thanh toán --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin Hóa đơn</h5>
                </div>
                <div class="card-body">
                    {{-- Form Tìm Kiếm --}}
                    {{-- <form method="POST" action="{{ route("staff.payment.search") }}">
                    @csrf
                        <div>
                            <input type="text" name="search" placeholder="Tìm kiếm SĐT hoặc tên khách hàng..."
                                value="" class="form-control" style="margin:10px 0; width:300px; float: left">
                            <button type="submit" class="btn btn-success" style="float: left; margin: 10px;">Tìm kiếm</button>
                        </div>
                    </form> --}}
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
                                    @forelse ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $index+=1 }}</td>
                                            <td>{{ $invoice->fullname }}</td>
                                            <td>{{ date('d/m/Y', strtotime($invoice->issue_date)) }}</td>
                                            <td>{{ $invoice->final_amount }}</td>
                                            <td>
                                                <form action="{{ route('staff.chi_tiet_hd_nv') }}" method="POST">
                                                @csrf
                                                    <input type="hidden" name="facility_id" value="{{ $invoice->facility_id }}">
                                                    <input type="hidden" name="user_id" value="{{ $invoice->customer_id }}">
                                                    <input type="hidden" name="slots" value='@json($mybooking_details[$invoice->invoice_detail_id] ?? [])'>
                                                    <input type="hidden" name="invoice_detail_id" value="{{ $invoice->invoice_detail_id }}">
                                                    <input type="hidden" name="invoices" value="{{ $invoices }}">
                                                    @if ($invoice->payment_status === 'Đã Hủy')
                                                        <p class="text-danger pt-3">Đã hủy</p>
                                                    @elseif ($invoice->payment_status === 'Đã thanh toán')
                                                        <p class="text-primary pt-3">Đã thanh toán</p>
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

        
        
    </div>
@endsection