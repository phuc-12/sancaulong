@extends('layouts.main')

@section('payment_content')
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
		<!-- /Breadcrumb -->
		<!-- <section class="booking-steps py-30">
			<div class="container">
				<ul class="d-xl-flex justify-content-center align-items-center">
					<li><h5><a href="coach-details.html"><span>1</span>Type of Booking</a></h5></li>
					<li><h5><a href="coach-timedate.html"><span>2</span>Time & Date</a></h5></li>
					<li><h5><a href="coach-personalinfo.html"><span>3</span>Personal Information</a></h5></li>
					<li><h5><a href="coach-order-confirm.html"><span>4</span>Order Confirmation</a></h5></li>
					<li class="active"><h5><a href="coach-payment.html"><span>5</span>Payment</a></h5></li>
				</ul>
			</div>
		</section> -->

		<!-- Page Content -->
		<div class="content">
			<div class="container">
				<section>
					<!-- <div class="text-center mb-40">
						<h3 class="mb-1">THANH TOÁN</h3>
						<p class="sub-title">Thanh toán an toàn cho đặt phòng của bạn.</p>
					</div> -->
					<!-- <div class="master-academy dull-whitesmoke-bg card mb-40">
						<div class="d-flex justify-content-between align-items-center">
							<div class="d-sm-flex justify-content-start align-items-center">
								<a href="javascript:void(0);"><img class="corner-radius-10" src="assets/img/profiles/avatar-02.png" alt="User"></a>
								<div class="info">
									<div class="d-flex justify-content-start align-items-center mb-3">
										<span class="text-white dark-yellow-bg color-white me-2 d-flex justify-content-center align-items-center">4.5</span>
										<span>300 Reviews</span>
									</div>
									<h3 class="mb-2">Kevin Anderson</h3>
									<p>Certified Badminton Coach with a deep understanding of the sport's  strategies.</p>
								</div>
							</div>
						</div>
					</div> -->
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
										<li><i class="feather-user me-2"></i>Tên: {{ $customer->fullname }}</li>
										<li><i class="feather-phone me-2"></i>SĐT: {{ $customer->phone }}</li>
										<li><i class="feather-mail me-2"></i>Email: {{ $customer->email }}</li>
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
											@php $total += $slot['price']; @endphp
											<tr class="text-center">
												<td>{{ $slot['court'] }}</td>
												<td>{{ $slot['start_time'] }}</td>
												<td>{{ $slot['end_time'] }}</td>
												<td>{{ $slot['date'] }}</td>
												<td>{{ number_format($slot['price']) }} đ</td>
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
								<div class="form-check d-flex justify-content-start align-items-center policy">
									{{-- <div class="d-inline-block">
										<input class="form-check-input" type="checkbox" value="" id="policy">
									</div> --}}
									<label>Sau khi click vào "Chuyển khoản" sẽ hiển thị mã QR bên dưới. <br> Vui lòng quét mã để thanh toán.</label>
								</div>
								<!-- <div class="d-grid btn-block">
									<button type="button" class="btn btn-primary course_item_btn">Thanh Toán</button>
								</div> -->
								<div class="d-flex justify-content-center gap-2">
									<button type="button" class="btn btn-primary btn-sm w-100 course_item_btn" style="width: 100%; height: 60px;">Chuyển Khoản</button>
									<!-- <button type="button" class="btn btn-primary btn-sm w-100 " style="max-width: 150px;">Tiền Mặt</button> -->
								</div>
								<div class="course_qr mt-4" align="center">
									<img 
										class="course_qr_img" style="width: 300px;"
										src="">
									<!-- <p>Nội dung chuyển khoản: <span id="paid_content"></span></p>
									<p>Số tiền: <span id="paid_price"></span></p>
									<p>Số tiền đã chuyển <span id="ketqua"></span></p> -->
								</div>
							</aside>
						</div>
					</div>
				</section>
			</div>
			<!-- Container -->
		</div>
		<!-- /Page Content -->

@endsection