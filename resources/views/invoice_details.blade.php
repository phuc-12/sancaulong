@extends('layouts.main')

@section('invoice_details_content')
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
			<div class="container">
				<section>
					<div class="row checkout">
						<div class="col-12 col-sm-12 col-md-12 col-lg-7">
							<div class="card booking-details" style="margin-bottom: 10px;">
								<h3 class="border-bottom">Thông tin đặt sân</h3>
								<ul>
									<div style="float:left;">
										<ul>
											{{-- SÂN SỐ DUY NHẤT --}}
											<li style="color: red; font-weight: 700;">
												<img src="{{ asset('img/icons/venue-type.svg') }}" alt="" class="me-2" width="54" style="background-color: green; border-radius: 100px;"> 
												Địa điểm: {{ $facilities->facility_name }}<br>
												Địa chỉ: {{ $facilities->address }} <br>
												Sân số: {{ $uniqueCourts }}
											</li>

											{{-- NGÀY ĐẶT DUY NHẤT --}}
											<li>
												<i class="feather-calendar me-2"></i>
												{{ $uniqueDates }}
											</li>

											{{-- THỜI GIAN DUY NHẤT --}}
											<li>
												<i class="feather-clock me-2"></i>
												{{ $uniqueTimes }}
											</li>

											{{-- TỔNG THỜI GIAN --}}
											<li>
												<i class="feather-users me-2"></i>Tổng thời gian : {{ $result }}
											</li>
										</ul>
									</div>
								</ul>
								
							</div>
							<div class="card booking-details">
								<h3 class="'border-bottom">Thông tin khách hàng</h3>
								<ul>
									<div style="float:left; width: 350px;">
										<li><i class="feather-user me-2"></i>Tên: {{ $customer_name ?? $customer->fullname }}</li>
										<li><i class="feather-phone me-2"></i>SĐT: {{ $customer_phone ?? $customer->phone }}</li>
										<li><i class="feather-mail me-2"></i>Email: {{ $customer_email ?? $customer->email }}</li>
									</div>
								</ul>
								
							</div>
							
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-5">
							<aside class="card payment-modes">
								<h3 class="border-bottom">Xác nhận thông tin thanh toán</h3>

								@if (!empty($slots))
								<table class="table table-bordered">
									<thead>
										<tr class="bg-gray-100 text-center">
											<th>Sân</th>
											<th>Bắt đầu</th>
											<th>Kết thúc</th>
											<th>Ngày</th>
											<th>Giá</th>
										</tr>
									</thead>
									<tbody>
										@php $total = 0; @endphp

										@foreach ($slots as $slot)
											@php $total += $slot['unit_price']; @endphp
											<tr class="text-center">
												<td>{{ $slot['court_id'] }}</td>
												<td>{{ $slot['start_time'] }}</td>
												<td>{{ $slot['end_time'] }}</td>
												<td>{{ $slot['booking_date'] }}</td>
												<td>{{ number_format($slot['unit_price']) }} đ</td>
											</tr>
										@endforeach
									</tbody>
								</table>

								<div class="text-right mt-3">
									<h3 class="text-lg font-semibold">Tổng tiền: {{ number_format($total) }} đ</h3>
									<input type="hidden" id="tongtien" value="{{ $total }}">
								</div>

								{{-- <button class="btn btn-success mt-4">Xác nhận thanh toán</button> --}}
							@else
								<p>Không có dữ liệu khung giờ nào!</p>
							@endif
			
								
								
								<form action="{{ route('cancel_invoice') }}" method="POST">
									@csrf
									<input type="hidden" name="invoice_detail_id" value="{{ $invoice_detail_id }}">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="submit" class="btn btn-danger btn-sm w-100 course_item_btn" style="width: 100%; height: 60px;">HỦY LỊCH</button>
                                    </div>
								</form>
							</aside>
						</div>
					</div>
				</section>
			</div>
			<!-- Container -->
		</div>
		<!-- /Page Content -->

@endsection