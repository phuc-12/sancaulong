@extends('layouts.main');

@section('contract_details_content')
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0 auto;
            max-width: 1300px;
            background: #fff;
            color: #000;
        }
        h2, h3, p { margin: 4px 0; }
        .header_invoice {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header_invoice h2 { font-size: 18px; text-transform: uppercase; }
        .info, .customer-info { margin-bottom: 10px; }
        .info p, .customer-info p { line-height: 1.5; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            font-size: 13px;
        }
        table th {
            background: #f3f3f3;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 15px;
            margin-top: 15px;
        }
        .footer {
            border-top: 1px solid #ccc;
            padding-top: 8px;
            text-align: left;
            font-size: 13px;
            margin-top: 25px;
        }
        .highlight { color: green; font-weight: bold; }
    </style>
</head>
<!-- Breadcrumb -->
		<div class="breadcrumb mb-0">
			<span class="primary-right-round"></span>
			<div class="container" style="margin-top: 40px;">
				<h1 class="text-white">Hoàn Thành Đặt Sân</h1>
				<ul>
					<li><a href="index.html">Trang Chủ</a></li>
					<li>Thanh Toán</li>
				</ul>
			</div>
		</div>

		<!-- Page Content -->
		<div class="content">
			<div class="">
				<section>
					<div class="row checkout">
						<div class="col-12 col-sm-12 col-md-12 col-lg-12">
							<div class="header_invoice">
								<h2>BẢNG BÁO GIÁ HỢP ĐỒNG</h2>
								<h3>{{ $facilities->facility_name }}</h3>
								<p><strong>Địa chỉ:</strong> {{ $facilities->address }}</p>
								<p><strong>Liên hệ:</strong> {{ $facilities->phone }}</p>
							</div>

							<div class="customer-info">
								<p><strong>Khách hàng:</strong> {{ $customer->fullname ?? '---' }}</p>
								<p><strong>Số điện thoại:</strong> {{ $customer->phone ?? '---' }}</p>
								<p><strong>Thời gian:</strong> 
									Từ {{ \Carbon\Carbon::parse($long_term_contracts->start_date)->format('d/m/Y') }} 
									đến {{ \Carbon\Carbon::parse($long_term_contracts->end_date)->format('d/m/Y') }}
								</p>
								<p><strong>Ngày trong tuần:</strong> {{ $daysOfWeek }}</p>
                                <p><strong>Sân:</strong> {{ $courts }}</p>
							</div>

							<table>
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Sân</th>
                                        <th>Tổng thời lượng (giờ)</th>
                                        <th>Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($slots as $slot)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($slot['booking_date'])->format('d/m') }}</td>
                                            <td>Sân {{ $slot['court_id'] }}</td>
                                            <td>{{ $slot['total_duration'] }} giờ</td>
                                            <td>{{ number_format($slot['total_price'], 0, ',', '.') }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

						<p class="total">Tổng cộng: <span class="highlight">{{ number_format($long_term_contracts->total_amount, 0, ',', '.') }} đ</span></p>
							{{-- <p class="total">Tổng cộng: <span class="highlight">{{ $summary['total_amount'] }} đ</span></p> --}}
							<p class="total">Số tiền cần thanh toán: <span class="highlight">{{ number_format($long_term_contracts->final_amount, 0, ',', '.') }} đ</span></p>
							
							<div>
								<form id="paymentCompleteForm" action="{{ route('payments_contract_complete') }}" method="POST">
									@csrf
									{{-- <input type="hidden" name="tongtien" id="tongtien" value="{{ $summary['total_amount'] }}">
									<input type="hidden" name="details" id="details_input" value='@json($details["actual_dates"])'>
									<input type="hidden" name="invoice_details_id" id="invoice_details_id" value="{{ $invoice_detail_id }}">
									<input type="hidden" name="facility_id" id="facility_id" value="{{ $userInfo["facility_id"] }}">
									<input type="hidden" name="user_id" id="user_id" value="{{ $userInfo["user_id"] }}">
									<input type="hidden" name="slot_details" value='@json($details["slot_details"])'> --}}
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="submit" class="btn btn-danger btn-sm w-100 course_item_btn" style="width: 100%; height: 60px;">Hủy hợp đồng</button>
                                    </div>
								</form>

							</div>
							
						</div>
					</div>
					{{-- <table> 
						<thead> 
							<tr> 
								<th>T.Gian</th> 
								<th>Dịch vụ</th> 
								<th>SL</th> 
								<th>Đ.Giá</th> 
							</tr> 
						</thead> 
						<tbody> 
							@php $total = 0; @endphp 
							@foreach ($details['actual_dates'] as $item) 
								@php $date = \Carbon\Carbon::parse($item['date'])->format('d/m'); @endphp 
								@foreach ($details['courts'] as $court) 
									@foreach ($details['slot_details'] as $slot) 
										@php $amount = $slot['amount']; $total += $amount; @endphp 
											<tr> 
												<td>{{ $date }}</td> 
												<td>Sân {{ $court }}</td> 
												<td>30 phút</td> 
												<td>{{ number_format($amount, 0, ',', '.') }}đ</td> 
											</tr> 
										@endforeach 
									@endforeach 
								@endforeach 
						</tbody> 
					</table> --}}
				</section>
			</div>
			<!-- Container -->
		</div>
		<!-- /Page Content -->

@endsection