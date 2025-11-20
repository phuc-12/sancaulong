@if(session('user_id'))
	<p>User ID: {{ session('user_id') }}</p>
@endif
@extends('layouts.main')

@section('index_content')
	<!-- Hero Section -->
	<section class="hero-section">
		<div class="banner-cock-one">
			<img src="{{ asset('img/icons/banner-cock1.svg') }}" alt="Banner">
		</div>
		<div class="banner-shapes">
			<div class="banner-dot-one">
				<span></span>
			</div>
			<div class="banner-cock-two">
				<img src="{{ asset('img/icons/banner-cock2.svg') }}" alt="Banner">
				<span></span>
			</div>
			<div class="banner-dot-two">
				<span></span>
			</div>
		</div>
		<div class="container">
			<div class="home-banner">
				<div class="row align-items-center w-100">
					<div class="col-lg-7 col-md-10 mx-auto">
						<div class="section-search aos" data-aos="fade-up">
							<h4>Những sân cầu lông cao cấp và Dịch vụ chuyên nghiệp</h4>
							<h1>Chọn <span>Sân Cầu Lông Tốt</span> Và Bắt Đầu Hành Trình Rèn Luyện</h1>
							<p class="sub-info">Giải phóng tiềm năng thể thao của bạn với cơ sở vật chất hiện đại và dịch vụ
								chuẩn thi đấu.</p>
							<div class="search-box">
								<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

								<form action="{{ route('search.results') }}" method="GET" class="d-flex align-items-center gap-2"
									style="background: #fff; padding: 10px 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
									
									<div class="position-relative flex-grow-1">
										<input type="search" name="keyword" required autocomplete="off"
											placeholder="🔍 Tìm theo tên sân, địa chỉ..."
											class="form-control"
											style="border-radius: 10px; padding-left: 40px;">
										
										<span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #888;">
											<i class="bi bi-search"></i>
										</span>
									</div>

									<button type="submit" class="btn btn-primary"
										style="border-radius: 10px; padding: 8px 18px; font-weight: 600;">
										Tìm kiếm
									</button>
								</form>

							</div>
						</div>
					</div>
					<div class="col-lg-5">
						<div class="banner-imgs text-center aos" data-aos="fade-up">
							<img class="img-fluid" src="{{ asset('img/bg/banner-right.png') }}" alt="Banner">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /Hero Section -->

	<!-- How It Works -->
	<section class="section work-section">
		<div class="work-cock-img">
			<img src="{{ asset('img/icons/work-cock.svg') }}" alt="Icon">
		</div>
		<div class="work-img">
			<div class="work-img-right">
				<img src="{{ asset('img/bg/work-bg.png') }}" alt="Icon">
			</div>
		</div>
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2>Nó <span>Hoạt Động Như Thế Nào</span></h2>
				<p class="sub-title">Đơn giản hóa quy trình đặt sân cho Doanh nghiệp, Tổ chức hoặc Cá nhân.</p>
			</div>
			<div class="row justify-content-center ">
				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/work-icon1.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h5>
								<a href="register.php">Tham Gia Cùng chúng tôi</a>
							</h5>
							<p>Đăng ký nhanh chóng và dễ dàng: Bắt đầu sử dụng nền tảng phần mềm của chúng tôi bằng quy
								trình tạo tài khoản đơn giản.</p>
							<a class="btn" href="{{ route('register') }}">
								Đăng Ký Ngay <i class="feather-arrow-right"></i>
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/work-icon2.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h5>
								<a href="listing-grid.php">Chọn Địa Điểm</a>
							</h5>
							<p>Đặt sân cầu lông nhanh chóng để được trải nghiệm cơ sở vật chất cao cấp và dịch vụ chuyên
								nghiệp.</p>
							<a class="btn" href="{{ route('danh_sach_san') }}">
								Đến Danh Sách Sân <i class="feather-arrow-right"></i>
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/work-icon3.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h5>
								<a href="coach-details.php">Quy Trình Đặt Sân</a>
							</h5>
							<p>Dễ dàng đặt chỗ, thanh toán và tận hưởng trải nghiệm liền mạch trên nền tảng thân thiện với
								người dùng của chúng tôi.</p>
							<a class="btn" href="coach-details.php">
								Đặt Ngay <i class="feather-arrow-right"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /How It Works -->

	<!-- Rental Deals -->
	<section class="section featured-venues">
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2><span>Địa Điểm</span> Nổi Bật</h2>
				<p class="sub-title">Các địa điểm thể thao tiên tiến cung cấp cơ sở vật chất mới nhất, môi trường năng động
					và độc đáo để nâng cao hiệu suất chơi cầu lông.</p>
			</div>
			<div class="row">
				<div class="featured-slider-group " align="center">
					<div class="owl-carousel featured-venues-slider owl-theme">
						<!-- Featured Item -->
						@isset($sancaulong)
							@forelse ($sancaulong as $thongtin)
								<form method="POST" action="{{ route('chi_tiet_san') }}">
									@csrf
									<div class="featured-venues-item aos" data-aos="fade-up"
										style="width: 380px; height: 582.8px; margin: 10px; float: left;">
										<div class="listing-item mb-0">
											<div class="listing-img">
												<button type="submit" style="border: white;">
													<input type="hidden" name="facility_id" value="{{ $thongtin['facility_id'] }}">
													<img src="{{ asset($thongtin->image) }}" alt="" style="width: 375px; height: 205px;">
												</button>
												<div class="fav-item-venues">
													<span class="tag tag-blue">Đang Hoạt Động</span>

													<h5 class="tag tag-primary">
														<!-- $thongtin->Court_prices -->
														{{ number_format($thongtin->courtPrice->default_price ?? 0) }}
														<span>/Giờ</span>
													</h5>

												</div>
											</div>
											<div class="listing-content">
												<div class="list-reviews">
													<div class="d-flex align-items-center">
														<span class="rating-bg">4.2</span><span>300 Reviews</span>
													</div>
													<a href="javascript:void(0)" class="fav-icon">
														<i class="feather-heart"></i>
													</a>
												</div>
												<h3 class="listing-title">
													<button type="submit" style="background-color: white; border: 1px solid white; height:62px;">
														{{ $thongtin->facility_name }}
													</button>
												</h3>
												<div class="listing-details-group" style="text-align: left;">
													<p style="height: 48px;">{{ $thongtin->description }}</p>
													<ul>
														<li>
															<span style="height: 48px;">
																<i class="feather-map-pin"></i>{{ $thongtin['address'] }} 
																
															</span>
															
														</li>
														<li style="float: left;">
															@php
																	$open = \Carbon\Carbon::parse($thongtin['open_time'])->format('H:i');
																	$close = \Carbon\Carbon::parse($thongtin['close_time'])->format('H:i');
																@endphp

															<i class="fa fa-clock-o"></i> {{ $open }} - {{ $close }}
														</li>
													</ul>
												</div>
												<div class="listing-button" style="clear: both">
													<div class="listing-venue-owner">
														<button class="btn btn-primary" >ĐẶT SÂN</button>
													</div>	
												</div>
											</div>
										</div>
									</div>
								</form>
							@empty
								<tr>
									<td colspan="7" class="text-center">Danh sách hiện tại đang trống</td>
								</tr>
							@endforelse
						@else
							<p>Dữ liệu chưa được tải.</p>
						@endisset
					</div>
				</div>
			</div>

			<!-- View More -->
			<div class="view-all text-center aos" data-aos="fade-up">
				<a href="{{ route('danh_sach_san') }}" class="btn btn-secondary d-inline-flex align-items-center mt-10">Xem
					tất cả<span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span></a>
			</div>
			<!-- View More -->

		</div>
	</section>
	<!-- /Rental Deals -->

	<!-- Services -->
	<section class="section service-section">
		<div class="work-cock-img">
			<img src="{{ asset('img/icons/work-cock.svg') }}" alt="Service">
		</div>
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2>Khám Phá <span>Dịch Vụ Của Chúng Tôi</span></h2>
				<p class="sub-title">Thúc đẩy sự xuất sắc và thúc đẩy sự phát triển của thể thao thông qua các dịch vụ phù
					hợp cho vận động viên, huấn luyện viên và người đam mê.</p>
			</div>
			<div class="row">
				<div class="col-lg-3 col-md-6 d-flex">
					<div class="service-grid w-100 aos" data-aos="fade-up">
						<div class="service-img">
							<a href="service-detail.html">
								<img src="{{ asset('img/services/service-01.jpg') }}" class="img-fluid" alt="Service">
							</a>
						</div>
						<div class="service-content">
							<h4><a href="service-detail.html">Court Rent</a></h4>
							<a href="service-detail.html">Tìm hiểu thêm</a>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 d-flex">
					<div class="service-grid w-100 aos" data-aos="fade-up">
						<div class="service-img">
							<a href="service-detail.html">
								<img src="{{ asset('img/services/service-02.jpg') }}" class="img-fluid" alt="Service">
							</a>
						</div>
						<div class="service-content">
							<h4><a href="service-detail.html">Group Lesson</a></h4>
							<a href="service-detail.html">Tìm hiểu thêm</a>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 d-flex">
					<div class="service-grid w-100 aos" data-aos="fade-up">
						<div class="service-img">
							<a href="service-detail.html">
								<img src="{{ asset('img/services/service-03.jpg') }}" class="img-fluid" alt="Service">
							</a>
						</div>
						<div class="service-content">
							<h4><a href="service-detail.html">Training Program</a></h4>
							<a href="service-detail.html">Tìm hiểu thêm</a>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 d-flex">
					<div class="service-grid w-100 aos" data-aos="fade-up">
						<div class="service-img">
							<a href="service-detail.html">
								<img src="{{ asset('img/services/service-04.jpg') }}" class="img-fluid" alt="Service">
							</a>
						</div>
						<div class="service-content">
							<h4><a href="service-detail.html">Private Lessons</a></h4>
							<a href="service-detail.html">Tìm hiểu thêm</a>
						</div>
					</div>
				</div>
			</div>
			<div class="view-all text-center aos" data-aos="fade-up">
				<a href="services.html" class="btn btn-secondary d-inline-flex align-items-center">
					View All Services <span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span>
				</a>
			</div>
		</div>
	</section>
	<!-- /Services -->

	<!-- Convenient -->
	<section class="section convenient-section">
		<div class="cock-img">
			<div class="cock-img-one">
				<img src="{{ asset('img/icons/cock-01.svg') }}" alt="Icon">
			</div>
			<div class="cock-img-two">
				<img src="{{ asset('img/icons/cock-02.svg') }}" alt="Icon">
			</div>
			<div class="cock-circle">
				<img src="{{ asset('img/bg/cock-shape.png') }}" alt="Icon">
			</div>
		</div>
		<div class="container">
			<div class="convenient-content aos" data-aos="fade-up">
				<h2>Lịch trình Thuận tiện và Linh hoạt</h2>
				<p>Tìm kiếm và đặt sân thuận tiện với hệ thống trực tuyến phù hợp với lịch trình và vị trí của bạn.</p>
			</div>
			<div class="convenient-btns aos" data-aos="fade-up">
				<a href="coach-details.html" class="btn btn-primary d-inline-flex align-items-center">
					Đặt Lịch Hoạt Động <span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span>
				</a>
				<a href="pricing.html" class="btn btn-secondary d-inline-flex align-items-center">
					Xem Bảng Giá <span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span>
				</a>
			</div>
		</div>
	</section>
	<!-- /Convenient -->

	<!-- Journey -->
	<section class="section journey-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 d-flex align-items-center">
					<div class="start-your-journey aos" data-aos="fade-up">
						<h2>Bắt đầu hành trình của bạn cùng <span class="active-sport">Dreamsports Badminton</span> ngay hôm
							nay.</h2>
						<p>Tại DreamSports Badminton, chúng tôi ưu tiên sự hài lòng của bạn và coi trọng phản hồi của bạn
							khi chúng tôi liên tục cải thiện và phát triển trải nghiệm học tập của mình.</p>
						<p>Sân cầu lông của chúng tôi sử dụng cơ sở vật chất hiện đại để tăng cường hiệu quả cũng như là
							trải nghiệm dành cho những người từ mới chơi đến những người đã chơi lâu năm.</p>
						<span class="stay-approach">Luôn dẫn đầu với cách tiếp cận sáng tạo của chúng tôi:</span>
						<div class="journey-list">
							<ul>
								<li><i class="fa-solid fa-circle-check"></i>Thảm Mới</li>
								<li><i class="fa-solid fa-circle-check"></i>Lưới Tốt</li>
								<li><i class="fa-solid fa-circle-check"></i>Hệ thống Wifi</li>
							</ul>
							<ul>
								<li><i class="fa-solid fa-circle-check"></i>Bãi Đỗ Xe Máy/Ôtô</li>
								<li><i class="fa-solid fa-circle-check"></i>Cửa Hàng Cầu Lông</li>
								<li><i class="fa-solid fa-circle-check"></i>Căn Tin</li>
							</ul>
						</div>
						<div class="convenient-btns">
							<a href="register.php" class="btn btn-primary d-inline-flex align-items-center">
								<span><i class="feather-user-plus me-2"></i></span>Tham Gia Cùng Chúng Tôi
							</a>
							<a href="about-us.html" class="btn btn-secondary d-inline-flex align-items-center">
								<span><i class="feather-align-justify me-2"></i></span>Tìm Hiểu Thêm
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="journey-img aos" data-aos="fade-up">
						<img src="{{ asset('img/journey-01.png') }}" class="img-fluid" alt="User">
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /Journey -->

	<!-- Group Coaching -->
	<section class="section group-coaching">
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2><span>Tính năng</span> Của Chúng Tôi</h2>
				<p class="sub-title">Khám phá khả năng của bản thân với cơ sở vật chất tiên tiến. Hãy tham gia cùng chúng
					tôi để cải thiện sức khỏe của bạn.</p>
			</div>
			<div class="row justify-content-center">

				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid coaching-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/coache-icon-02.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h3 style="text-align:center">Huấn luyện riêng</h3>
							<p>Tìm kiếm huấn luyện viên cầu lông tư nhân và các học viện để có phương pháp tiếp cận cá nhân
								hóa nhằm nâng cao kỹ năng.</p>
							<a href="javascript:void(0);">
								Tìm hiểu thêm
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid coaching-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/coache-icon-03.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h3 style="text-align:center">Cửa hàng thiết bị</h3>
							<p>Cửa hàng cung cấp cho bạn thiết bị cầu lông chất lượng cao, nâng cao hiệu suất trên sân của
								bạn.</p>
							<a href="javascript:void(0);">
								Tìm hiểu thêm
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid coaching-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/coache-icon-04.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h3 style="text-align:center">Bài học sáng tạo</h3>
							<p>Nâng cao kỹ năng cầu lông của bạn với các bài học sáng tạo, kết hợp các kỹ thuật và phương
								pháp đào tạo hiện đại.</p>
							<a href="javascript:void(0);">
								Tìm hiểu thêm
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid coaching-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/coache-icon-05.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h3 style="text-align:center">Cộng đồng</h3>
							<p>Nâng cao trò chơi của bạn với các bài học hấp dẫn và cộng đồng hỗ trợ. Tham gia với chúng tôi
								ngay bây giờ và đưa kỹ năng của bạn lên một tầm cao mới.</p>
							<a href="javascript:void(0);">
								Tìm hiểu thêm
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 d-flex">
					<div class="work-grid coaching-grid w-100 aos" data-aos="fade-up">
						<div class="work-icon">
							<div class="work-icon-inner">
								<img src="{{ asset('img/icons/coache-icon-06.svg') }}" alt="Icon">
							</div>
						</div>
						<div class="work-content">
							<h3 style="text-align:center">Thuê sân cầu lông</h3>
							<p>Tận hưởng các buổi cầu lông không bị gián đoạn tại DreamSports với dịch vụ cho thuê sân cao
								cấp của chúng tôi.</p>
							<a href="javascript:void(0);">
								Tìm hiểu thêm
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Group Coaching -->

	<!-- Best Services -->
	<section class="section best-services">
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2><span>Lợi ích và Dịch vụ</span> xuất sắc</h2>
				<p class="sub-title">Nâng cao hành trình cầu lông của bạn cùng DreamSports: Quyền lợi độc quyền, dịch vụ đặc
					biệt.</p>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="best-service-img aos" data-aos="fade-up">
						<img src="{{ asset('img/best-service.jpg') }}" class="img-fluid" alt="Service">
						<div class="service-count-blk">
							<div class="coach-count">
								<h3>Sân Cầu Lông</h3>
								<h2><span class="counter-up">88</span>+</h2>
								<h4>Sân được bảo trì tốt mang lại trải nghiệm chơi cầu lông tối ưu.</h4>
							</div>
							<div class="coach-count coart-count">
								<h3>Huấn Luyện Viên</h3>
								<h2><span class="counter-up">59</span>+</h2>
								<h4>Huấn luyện viên cầu lông có trình độ cao và chuyên môn sâu rộng trong môn thể thao này.
								</h4>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="ask-questions aos" data-aos="fade-up">
						<h3>Những câu hỏi thường gặp</h3>
						<p>Sau đây là một số câu hỏi thường gặp về cầu lông tại DreamSports:</p>
						<div class="faq-info">
							<div class="accordion" id="accordionExample">

								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingOne">
										<a href="javascript:;" class="accordion-button" data-bs-toggle="collapse"
											data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
											Tôi có thể đặt sân cầu lông tại DreamSports như thế nào?
										</a>
									</h2>
									<div id="collapseOne" class="accordion-collapse collapse show"
										aria-labelledby="headingOne" data-bs-parent="#accordionExample">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc
													khách hàng của chúng tôi để đặt chỗ dễ dàng. </p>
											</div>
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->

								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingTwo">
										<a href="javascript:;" class="accordion-button collapsed" data-bs-toggle="collapse"
											data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
											Thời hạn đặt sân cầu lông là bao lâu?
										</a>
									</h2>
									<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
										data-bs-parent="#accordionExample">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc
													khách hàng của chúng tôi để đặt chỗ dễ dàng. </p>
											</div>
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->

								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingThree">
										<a href="javascript:;" class="accordion-button collapsed" data-bs-toggle="collapse"
											data-bs-target="#collapseThree" aria-expanded="false"
											aria-controls="collapseThree">
											Tôi có thể thuê dụng cụ cầu lông tại DreamSports không?
										</a>
									</h2>
									<div id="collapseThree" class="accordion-collapse collapse"
										aria-labelledby="headingThree" data-bs-parent="#accordionExample">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc
													khách hàng của chúng tôi để đặt chỗ dễ dàng.</p>
											</div>
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->

								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingFour">
										<a href="javascript:;" class="accordion-button collapsed" data-bs-toggle="collapse"
											data-bs-target="#collapseFour" aria-expanded="false"
											aria-controls="collapseFour">
											DreamSports có cung cấp dịch vụ huấn luyện nào không?
										</a>
									</h2>
									<div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
										data-bs-parent="#accordionExample">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc
													khách hàng của chúng tôi để đặt chỗ dễ dàng.</p>
											</div>
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->

								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingFive">
										<a href="javascript:;" class="accordion-button collapsed" data-bs-toggle="collapse"
											data-bs-target="#collapseFive" aria-expanded="false"
											aria-controls="collapseFive">
											Tôi có thể tham gia các giải đấu hoặc giải đấu cầu lông tại DreamSports không?
										</a>
									</h2>
									<div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
										data-bs-parent="#accordionExample">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc
													khách hàng của chúng tôi để đặt chỗ dễ dàng.</p>
											</div>
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /Best Services -->

	<!-- Courts Near -->
	<section class="section court-near">
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2>Tìm sân <span>Gần với Bạn</span></h2>
				<p class="sub-title">Khám phá sân cầu lông gần đó để chơi trò chơi thuận tiện và dễ tiếp cận.</p>
			</div>
			<div class="row">
				<div class="featured-slider-group aos" data-aos="fade-up">
					<div class="owl-carousel featured-venues-slider owl-theme">

						<!-- Courts Item -->
						<div class="featured-venues-item court-near-item">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="venue-details.html">
										<img src="{{ asset('img/venues/venues-04.jpg') }}" alt="Venue">
									</a>
									<div class="fav-item-venues">
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content ">
									<h3 class="listing-title">
										<a href="venue-details.html">Smart Shuttlers</a>
									</h3>
									<div class="listing-details-group">
										<ul>
											<li>
												<span>
													<i class="feather-map-pin"></i>1 Crowthorne Road, 4th Street, NY
												</span>
											</li>
										</ul>
									</div>
									<div class="list-reviews near-review">
										<div class="d-flex align-items-center">
											<span class="rating-bg">4.2</span><span>300 Reviews</span>
										</div>
										<span class="mile-away"><i class="feather-zap"></i>2.1 Miles Away</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /Courts Item -->

						<!-- Courts Item -->
						<div class="featured-venues-item court-near-item">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="venue-details.html">
										<img src="{{ asset('img/venues/venues-05.jpg') }}" alt="Venue">
									</a>
									<div class="fav-item-venues">
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content ">
									<h3 class="listing-title">
										<a href="venue-details.html">Parlers Badminton</a>
									</h3>
									<div class="listing-details-group">
										<ul>
											<li>
												<span>
													<i class="feather-map-pin"></i>Hope Street, Battersea, SW11 2DA
												</span>
											</li>
										</ul>
									</div>
									<div class="list-reviews near-review">
										<div class="d-flex align-items-center">
											<span class="rating-bg">4.2</span><span>200 Reviews</span>
										</div>
										<span class="mile-away"><i class="feather-zap"></i>9.3 Miles Away</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /Courts Item -->

						<!-- Courts Item -->
						<div class="featured-venues-item court-near-item">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="venue-details.html">
										<img src="{{ asset('img/venues/venues-06.jpg') }}" alt="Venue">
									</a>
									<div class="fav-item-venues">
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content ">
									<h3 class="listing-title">
										<a href="venue-details.html">6 Feathers</a>
									</h3>
									<div class="listing-details-group">
										<ul>
											<li>
												<span>
													<i class="feather-map-pin"></i>Lonsdale Road, Barnes, SW13 9QL
												</span>
											</li>
										</ul>
									</div>
									<div class="list-reviews near-review">
										<div class="d-flex align-items-center">
											<span class="rating-bg">4.2</span><span>400 Reviews</span>
										</div>
										<span class="mile-away"><i class="feather-zap"></i>10.8 Miles Away</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /Courts Item -->

						<!-- Courts Item -->
						<div class="featured-venues-item court-near-item">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="venue-details.html">
										<img src="{{ asset('img/venues/venues-05.jpg') }}" alt="Venue">
									</a>
									<div class="fav-item-venues">
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content ">
									<h3 class="listing-title">
										<a href="venue-details.html">Parlers Badminton</a>
									</h3>
									<div class="listing-details-group">
										<ul>
											<li>
												<span>
													<i class="feather-map-pin"></i>1 Crowthorne Road, 4th Street, NY
												</span>
											</li>
										</ul>
									</div>
									<div class="list-reviews near-review">
										<div class="d-flex align-items-center">
											<span class="rating-bg">4.2</span><span>300 Reviews</span>
										</div>
										<span class="mile-away"><i class="feather-zap"></i>8.1 Miles Away</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /Courts Item -->

					</div>
				</div>
			</div>

			<!-- View More -->
			<div class="view-all text-center aos" data-aos="fade-up">
				<a href="listing-grid.html" class="btn btn-secondary d-inline-flex align-items-center">View All Services
					<span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span></a>
			</div>
			<!-- View More -->

		</div>
	</section>
	<!-- /Courts Near -->

	<!-- Testimonials -->
	<section class="section our-testimonials">
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2><span>Đánh Giá Chung</span> từ người dùng</h2>
				<p class="sub-title">Những đánh giá nhiệt tình từ những người đam mê cầu lông, giới thiệu các dịch vụ đặc
					biệt của chúng tôi.</p>
			</div>
			<div class="row">
				<div class="featured-slider-group aos" data-aos="fade-up">
					<div class="owl-carousel testimonial-slide featured-venues-slider owl-theme">

						<!-- Testimonials Item -->
						<div class="testimonial-group">
							<div class="testimonial-review">
								<div class="rating-point">
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<span> 5.0</span>
								</div>
								<h5>Personalized Attention</h5>
								<p>DreamSports' coaching services enhanced my badminton skills. Personalized attention from
									knowledgeable coaches propelled my game to new heights.</p>
							</div>
							<div class="listing-venue-owner">
								<a class="navigation">
									<img src="{{ asset('img/profiles/avatar-01.jpg') }}" alt="User">
								</a>
								<div class="testimonial-content">
									<h5><a href="javascript:;">Ariyan Rusov</a></h5>
									<a href="javascript:void(0);" class="btn btn-primary ">
										Badminton
									</a>
								</div>
							</div>
						</div>
						<!-- /Testimonials Item -->

						<!-- Testimonials Item -->
						<div class="testimonial-group">
							<div class="testimonial-review">
								<div class="rating-point">
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<span> 5.0</span>
								</div>
								<h5>Quality Matters !</h5>
								<p>DreamSports' advanced badminton equipment has greatly improved my performance on the
									court. Their quality range of rackets and shoes made a significant impact.</p>
							</div>
							<div class="listing-venue-owner">
								<a class="navigation">
									<img src="{{ asset('img/profiles/avatar-04.jpg') }}" alt="User">
								</a>
								<div class="testimonial-content">
									<h5><a href="javascript:;">Darren Valdez</a></h5>
									<a href="javascript:void(0);" class="btn btn-primary ">
										Badminton
									</a>
								</div>
							</div>
						</div>
						<!-- /Testimonials Item -->

						<!-- Testimonials Item -->
						<div class="testimonial-group">
							<div class="testimonial-review">
								<div class="rating-point">
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<span> 5.0</span>
								</div>
								<h5>Excellent Professionalism !</h5>
								<p>DreamSports' unmatched professionalism and service excellence left a positive experience.
									Highly recommended for court rentals and equipment purchases.</p>
							</div>
							<div class="listing-venue-owner">
								<a class="navigation">
									<img src="{{ asset('img/profiles/avatar-03.jpg') }}" alt="User">
								</a>
								<div class="testimonial-content">
									<h5><a href="javascript:;">Elinor Dunn</a></h5>
									<a href="javascript:void(0);" class="btn btn-primary ">
										Badminton
									</a>
								</div>
							</div>
						</div>
						<!-- /Testimonials Item -->

						<!-- Testimonials Item -->
						<div class="testimonial-group">
							<div class="testimonial-review">
								<div class="rating-point">
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<i class="fas fa-star filled"></i>
									<span> 5.0</span>
								</div>
								<h5>Quality Matters !</h5>
								<p>DreamSports' advanced badminton equipment has greatly improved my performance on the
									court. Their quality range of rackets and shoes made a significant impact.</p>
							</div>
							<div class="listing-venue-owner">
								<a class="navigation">
									<img src="{{ asset('img/profiles/avatar-04.jpg') }}" alt="User">
								</a>
								<div class="testimonial-content">
									<h5><a href="javascript:;">Darren Valdez</a></h5>
									<a href="javascript:void(0);" class="btn btn-primary ">
										Badminton
									</a>
								</div>
							</div>
						</div>
						<!-- /Testimonials Item -->

					</div>
				</div>

				<!-- Testimonials Slide -->
				<div class="brand-slider-group aos" data-aos="fade-up">
					<div class="owl-carousel testimonial-brand-slider owl-theme">
						<div class="brand-logos">
							<img src="{{ asset('img/testimonial-icon-01.svg') }}" alt="Brand">
						</div>
						<div class="brand-logos">
							<img src="{{ asset('img/testimonial-icon-04.svg') }}" alt="Brand">
						</div>
						<div class="brand-logos">
							<img src="{{ asset('img/testimonial-icon-03.svg') }}" alt="Brand">
						</div>
						<div class="brand-logos">
							<img src="{{ asset('img/testimonial-icon-04.svg') }}" alt="Brand">
						</div>
						<div class="brand-logos">
							<img src="{{ asset('img/testimonial-icon-05.svg') }}" alt="Brand">
						</div>
						<div class="brand-logos">
							<img src="{{ asset('img/testimonial-icon-03.svg') }}" alt="Brand">
						</div>
						<div class="brand-logos">
							<img src="{{ asset('img/testimonial-icon-04.svg') }}" alt="Brand">
						</div>
					</div>
				</div>
				<!-- /Testimonials Slide -->

			</div>
		</div>
	</section>
	<!-- /Testimonials -->

	<!-- Featured Plans -->
	<section class="section featured-plan">
		<div class="work-img ">
			<div class="work-img-right">
				<img src="{{ asset('img/bg/work-bg.png') }}" alt="Icon">
			</div>
		</div>
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2>Chúng tôi có <span>Những dịch vụ dài hạn tuyệt với dành cho bạn</span></h2>
				<p class="sub-title">Chọn gói tháng hoặc năm để được truy cập liên tục vào các cơ sở cầu lông cao cấp của
					chúng tôi. Hãy tham gia cùng chúng tôi và trải nghiệm sự tiện lợi tuyệt vời.</p>
			</div>
			<div class="interset-btn aos" data-aos="fade-up">
				<div class="status-toggle d-inline-flex align-items-center">
					Monthly
					<input type="checkbox" id="status_1" class="check">
					<label for="status_1" class="checktoggle">checkbox</label>
					Yearly
				</div>
			</div>
			<div class="price-wrap aos" data-aos="fade-up">
				<div class="row justify-content-center">
					<div class="col-lg-4 d-flex col-md-6">

						<!-- Price Card -->
						<div class="price-card flex-fill ">
							<div class="price-head">
								<img src="{{ asset('img/icons/price-01.svg') }}" alt="Price">
								<h3>Professoinal</h3>
							</div>
							<div class="price-body">
								<div class="per-month">
									<h2><sup>$</sup><span>60.00</span></h2>
									<span>Per Month</span>
								</div>
								<div class="features-price-list">
									<h5>Features</h5>
									<p>Everything in our free Upto 10 users. </p>
									<ul>
										<li class="active"><i class="feather-check-circle"></i>Included : Quality Checked By
											Envato</li>
										<li class="active"><i class="feather-check-circle"></i>Included : Future Updates
										</li>
										<li class="active"><i class="feather-check-circle"></i>Technical Support</li>
										<li class="inactive"><i class="feather-x-circle"></i>Add Listing </li>
										<li class="inactive"><i class="feather-x-circle"></i>Approval of Listing</li>
									</ul>
								</div>
								<div class="price-choose">
									<a href="javascript:;" class="btn viewdetails-btn">Choose Plan</a>
								</div>
								<div class="price-footer">
									<p>Use, by you or one client, in a single end product which end users. charged for. The
										total price includes the item price and a buyer fee.</p>
								</div>
							</div>
						</div>
						<!-- /Price Card -->

					</div>
					<div class="col-lg-4 d-flex col-md-6">

						<!-- Price Card -->
						<div class="price-card flex-fill">
							<div class="price-head expert-price">
								<img src="{{ asset('img/icons/price-02.svg') }}" alt="Price">
								<h3>Expert</h3>
								<span>Recommended</span>
							</div>
							<div class="price-body">
								<div class="per-month">
									<h2><sup>$</sup><span>60.00</span></h2>
									<span>Per Month</span>
								</div>
								<div class="features-price-list">
									<h5>Features</h5>
									<p>Everything in our free Upto 10 users. </p>
									<ul>
										<li class="active"><i class="feather-check-circle"></i>Included : Quality Checked By
											Envato</li>
										<li class="active"><i class="feather-check-circle"></i>Included : Future Updates
										</li>
										<li class="active"><i class="feather-check-circle"></i>6 Months Technical Support
										</li>
										<li class="inactive"><i class="feather-x-circle"></i>Add Listing </li>
										<li class="inactive"><i class="feather-x-circle"></i>Approval of Listing</li>
									</ul>
								</div>
								<div class="price-choose active-price">
									<a href="javascript:;" class="btn viewdetails-btn">Choose Plan</a>
								</div>
								<div class="price-footer">
									<p>Use, by you or one client, in a single end product which end users. charged for. The
										total price includes the item price and a buyer fee.</p>
								</div>
							</div>
						</div>
						<!-- /Price Card -->

					</div>
					<div class="col-lg-4 d-flex col-md-6">

						<!-- Price Card -->
						<div class="price-card flex-fill">
							<div class="price-head">
								<img src="{{ asset('img/icons/price-03.svg') }}" alt="Price">
								<h3>Enterprise</h3>
							</div>
							<div class="price-body">
								<div class="per-month">
									<h2><sup>$</sup><span>990.00</span></h2>
									<span>Per Month</span>
								</div>
								<div class="features-price-list">
									<h5>Features</h5>
									<p>Everything in our free Upto 10 users. </p>
									<ul>
										<li class="active"><i class="feather-check-circle"></i>Included : Quality Checked By
											Envato</li>
										<li class="active"><i class="feather-check-circle"></i>Included : Future Updates
										</li>
										<li class="active"><i class="feather-check-circle"></i>Technical Support</li>
										<li class="active"><i class="feather-check-circle"></i>Add Listing </li>
										<li class="active"><i class="feather-check-circle"></i>Approval of Listing</li>
									</ul>
								</div>
								<div class="price-choose">
									<a href="javascript:;" class="btn viewdetails-btn">Choose Plan</a>
								</div>
								<div class="price-footer">
									<p>Use, by you or one client, in a single end product which end users. charged for. The
										total price includes the item price and a buyer fee.</p>
								</div>
							</div>
						</div>
						<!-- /Price Card -->

					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /Featured Plans -->

	<!-- Latest News -->
	<section class="section featured-venues latest-news">
		<div class="container">
			<div class="section-heading aos" data-aos="fade-up">
				<h2>Tin tức <span>Mới Nhất</span></h2>
				<p class="sub-title">Cập nhật những thông tin mới nhất từ ​​thế giới cầu lông - luôn được cập nhật và truyền
					cảm hứng từ những tin tức thú vị và thành tích đáng chú ý trong môn thể thao này.</p>
			</div>
			<div class="row">
				<div class="featured-slider-group ">
					<div class="owl-carousel featured-venues-slider owl-theme">

						<!-- News -->
						<div class="featured-venues-item aos" data-aos="fade-up">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="blog-details.html">
										<img src="{{ asset('img/venues/venues-07.jpg') }}" alt="User">
									</a>
									<div class="fav-item-venues news-sports">
										<span class="tag tag-blue">Badminton</span>
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content news-content">
									<div class="listing-venue-owner listing-dates">
										<a href="javascript:;" class="navigation">
											<img src="{{ asset('img/profiles/avatar-01.jpg') }}" alt="User">Orlando Waters

										</a>
										<span><i class="feather-calendar"></i>15 May 2023</span>
									</div>
									<h3 class="listing-title">
										<a href="blog-details.html">Badminton Gear Guide: Must-Have Equipment for Every
											Player</a>
									</h3>
									<div class="listing-button read-new">
										<ul class="nav">
											<li><a href="javascript:;"><i class="feather-heart"></i>45</a></li>
											<li><a href="javascript:;"><i class="feather-message-square"></i>45</a></li>
										</ul>
										<span><img src="{{ asset('img/icons/clock.svg') }}" alt="User">10 Min To Read</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /News -->

						<!-- News -->
						<div class="featured-venues-item aos" data-aos="fade-up">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="blog-details.html">
										<img src="{{ asset('img/venues/venues-08.jpg') }}" alt="User">
									</a>
									<div class="fav-item-venues news-sports">
										<span class="tag tag-blue">Sports Activites</span>
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content news-content">
									<div class="listing-venue-owner listing-dates">
										<a href="javascript:;" class="navigation">
											<img src="{{ asset('img/profiles/avatar-03.jpg') }}" alt="User">Nichols
										</a>
										<span><i class="feather-calendar"></i>16 Jun 2023</span>
									</div>
									<h3 class="listing-title">
										<a href="blog-details.html">Badminton Techniques: Mastering the Smash, Drop Shot,
											and Clear </a>
									</h3>
									<div class="listing-button read-new">
										<ul class="nav">
											<li><a href="javascript:;"><i class="feather-heart"></i>35</a></li>
											<li><a href="javascript:;"><i class="feather-message-square"></i>35</a></li>
										</ul>
										<span><img src="{{ asset('img/icons/clock.svg') }}" alt="Icon">12 Min To Read</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /News -->

						<!-- News -->
						<div class="featured-venues-item aos" data-aos="fade-up">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="blog-details.html">
										<img src="{{ asset('img/venues/venues-09.jpg') }}" alt="Venue">
									</a>
									<div class="fav-item-venues news-sports">
										<span class="tag tag-blue">Rules of Game</span>
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content news-content">
									<div class="listing-venue-owner listing-dates">
										<a href="javascript:;" class="navigation">
											<img src="{{ asset('img/profiles/avatar-06.jpg') }}" alt="User">Joanna Le
										</a>
										<span><i class="feather-calendar"></i>11 May 2023</span>
									</div>
									<h3 class="listing-title">
										<a href="blog-details.html">The Evolution of Badminton:From Backyard Fun to Olympic
											Sport</a>
									</h3>
									<div class="listing-button read-new">
										<ul class="nav">
											<li><a href="javascript:;"><i class="feather-heart"></i>25</a></li>
											<li><a href="javascript:;"><i class="feather-message-square"></i>25</a></li>
										</ul>
										<span><img src="{{ asset('img/icons/clock.svg') }}" alt="Clock">14 Min To
											Read</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /News -->

						<!-- News -->
						<div class="featured-venues-item aos" data-aos="fade-up">
							<div class="listing-item mb-0">
								<div class="listing-img">
									<a href="blog-details.html">
										<img src="{{ asset('img/venues/venues-08.jpg') }}" alt="Venue">
									</a>
									<div class="fav-item-venues news-sports">
										<span class="tag tag-blue">Sports Activites</span>
										<div class="list-reviews coche-star">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="listing-content news-content">
									<div class="listing-venue-owner listing-dates">
										<a href="javascript:;" class="navigation">
											<img src="{{ asset('img/profiles/avatar-01.jpg') }}" alt="User">Mart Sublin
										</a>
										<span><i class="feather-calendar"></i>12 May 2023</span>
									</div>
									<h3 class="listing-title">
										<a href="blog-details.html">Sports Make Us A Lot Stronger And Healthier Than We
											Think</a>
									</h3>
									<div class="listing-button read-new">
										<ul class="nav">
											<li><a href="javascript:;"><i class="feather-heart"></i>35</a></li>
											<li><a href="javascript:;"><i class="feather-message-square"></i>35</a></li>
										</ul>
										<span><img src="{{ asset('img/icons/clock.svg') }}" alt="Clock">12 Min To
											Read</span>
									</div>
								</div>
							</div>
						</div>
						<!-- /News -->

					</div>
				</div>
			</div>

			<!-- View More -->
			<div class="view-all text-center aos" data-aos="fade-up">
				<a href="blog-grid.html" class="btn btn-secondary d-inline-flex align-items-center">View All News <span
						class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span></a>
			</div>
			<!-- View More -->

		</div>
	</section>
	<!-- /Latest News -->

	<!-- Newsletter -->
	<section class="section newsletter-sport">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="subscribe-style aos" data-aos="fade-up">
						<div class="banner-blk">
							<img src="{{ asset('img/subscribe-bg.jpg') }}" class="img-fluid" alt="Subscribe">
						</div>
						<div class="banner-info ">
							<img src="{{ asset('img/icons/subscribe.svg') }}" class="img-fluid" alt="Subscribe">
							<h2>Subscribe to Newsletter</h2>
							<p>Just for you, exciting badminton news updates.</p>
							<div class="subscribe-blk bg-white">
								<div class="input-group align-items-center">
									<i class="feather-mail"></i>
									<input type="email" class="form-control" placeholder="Enter Email Address"
										aria-label="email">
									<div class="subscribe-btn-grp">
										<input type="submit" class="btn btn-secondary" value="Subscribe">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /Newsletter -->

@endsection