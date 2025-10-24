@extends('layouts.main')

@section('payments_content')
		<!-- Breadcrumb -->
		<div class="breadcrumb mb-0">
			<span class="primary-right-round"></span>
			<div class="container">
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
							<div class="card booking-details">
								<h3 class="border-bottom">Thông tin đặt sân</h3>
								<ul>
									<div style="float:left; width: 300px;">
										<li><i class="feather-calendar me-2"></i><?php echo $layngaydat; ?></li>
										<li><i class="feather-clock me-2"></i><?php echo $laygiobd.' Đến '.$laygiokt; ?> </li>
										<li><i class="feather-users me-2"></i>Tổng thời gian : <?php echo $laytongtg.' Tiếng'; ?></li>
									</div>
									
									<div style="float:left; width: 350px;">
										<li><i class="feather-user me-2"></i>Tên: <?php echo $layten; ?></li>
										<li><i class="feather-phone me-2"></i>SĐT: <?php echo $laysdt; ?></li>
										<li><i class="feather-mail me-2"></i><?php echo $layemail; ?></li>
									</div>
								</ul>
								
							</div>
							<div class="course_qr" align="center">
								<img 
									class="course_qr_img" style="width: 300px;"
									src="">
								<!-- <p>Nội dung chuyển khoản: <span id="paid_content"></span></p>
								<p>Số tiền: <span id="paid_price"></span></p>
								<p>Số tiền đã chuyển <span id="ketqua"></span></p> -->
							</div>
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-5">
							<aside class="card payment-modes">
								<h3 class="border-bottom">Thanh toán</h3>
								<!-- <h6 class="mb-3">Chọn phương thức thanh toán</h6> -->
								<div class="radio">
									<!-- <div class="form-check form-check-inline mb-3">
									  	<input class="form-check-input default-check me-2" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="Credit Card">
									  	<label class="form-check-label" for="inlineRadio1">Chuyển khoản</label>
									</div>
									<div class="form-check form-check-inline mb-3">
									  	<input class="form-check-input default-check me-2" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="Paypal">
									  	<label class="form-check-label" for="inlineRadio2">Tiền mặt</label>
									</div> -->
									<!-- <div class="form-check form-check-inline">
									  	<input class="form-check-input default-check me-2" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="Wallet">
									  	<label class="form-check-label" for="inlineRadio3">Wallet</label>
									</div> -->
								</div>
								<!-- <hr> -->
								<!-- <ul class="order-sub-total">
									<li>
										<p>Sub total</p>
										<h6>$250</h6>
									</li>
									<li>
										<p>Additional Guest</p>
										<h6>$25</h6>
									</li>
									<li>
										<p>Service charge</p>
										<h6>$70</h6>
									</li>
								</ul> -->
								<div class="mb-10">
									<div>
										<form method="POST" action=""></form>
											<table style="border: 1px solid grey; width: 100%;">
												<thead>
													<tr style="border: 1px solid grey">
														<td style="border: 1px solid grey" align="center"><b>STT</b></td>
														<td style="border: 1px solid grey" align="center"><b>Bắt đầu</b></td>
														<td style="border: 1px solid grey" align="center"><b>Kết thúc</b></td>
														<td style="border: 1px solid grey" align="center"><b>Giá</b></td>
														<td style="border: 1px solid grey" align="center"><b>Số lượng</b></td>
														<td style="border: 1px solid grey" align="center"></td>
													</tr>
												</thead>
												<tbody>
													<?php
														include_once("assets/view/sancaulong/viewgiodat.php");
													?>
												</tbody>
											</table>
										</form>
									</div>
									
									<div>
										<?php
											include_once('assets/model/mUser.php');
											$k = new mUser();
											
											if (isset($_POST['btn_cn']) && isset($_POST['maDat'])) {
												$maDat = $_POST['maDat'];
												$soLuong = $_REQUEST['soLuong'];
											if ($k->themxoasua("UPDATE bookings SET soLuong = '$soLuong' WHERE maDat = '$maDat' LIMIT 1") == 1) {
													echo '<script>window.location.href="court-payment.php?maKH='.$layid.'";</script>';
													exit();
												}
											}

										?>


									</div>
								</div>
								<div class="order-total d-flex justify-content-between align-items-center">
									<?php
										include_once("assets/view/sancaulong/viewtongtien.php");
									?>
									<input type="hidden" id="maKH" value="<?php echo $layid; ?>">
									<input type="hidden" id="tongtien" value="<?php echo $tongtien*1000; ?>">
								</div>
								<div class="form-check d-flex justify-content-start align-items-center policy">
									<div class="d-inline-block">
										<input class="form-check-input" type="checkbox" value="" id="policy">
									</div>
									<label class="form-check-label" for="policy">Bằng cách nhấp vào 'Gửi yêu cầu', tôi đồng ý với Chính sách bảo mật và Điều khoản sử dụng của Dreamsport</label>
								</div>
								<!-- <div class="d-grid btn-block">
									<button type="button" class="btn btn-primary course_item_btn">Thanh Toán</button>
								</div> -->
								<div class="d-flex justify-content-center gap-2">
									<button type="button" class="btn btn-primary btn-sm w-100 course_item_btn" style="max-width: 150px;">Chuyển Khoản</button>
									<!-- <button type="button" class="btn btn-primary btn-sm w-100 " style="max-width: 150px;">Tiền Mặt</button> -->
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