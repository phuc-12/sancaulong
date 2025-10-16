
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from dreamsports.dreamstechnologies.com/html/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 03 Apr 2025 04:25:18 GMT -->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>DreamSports</title>

	<!-- Meta Tags -->
	<meta name="twitter:description" content="Elevate your badminton business with Dream Sports template. Empower coaches & players, optimize court performance and unlock industry-leading success for your brand.">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="description" content="Elevate your badminton business with Dream Sports template. Empower coaches & players, optimize court performance and unlock industry-leading success for your brand.">
	<meta name="keywords" content="badminton, coaching, event, players, training, courts, tournament, athletes, courts rent, lessons, court booking, stores, sports faqs, leagues, chat, wallet, invoice">
	<meta name="author" content="Dreamguys - DreamSports">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@dreamguystech">
	<meta name="twitter:title" content="DreamSports -  Booking Coaches, Venue for tournaments, Court Rental template">

	<meta name="twitter:image" content="{{ asset('img/meta-image.jpg') }}">
	<meta name="twitter:image:alt" content="DreamSports">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta property="og:url" content="https://dreamsports.dreamguystech.com/">
	<meta property="og:title" content="DreamSports -  Booking Coaches, Venue for tournaments, Court Rental template">
	<meta property="og:description" content="Elevate your badminton business with Dream Sports template. Empower coaches & players, optimize court performance and unlock industry-leading success for your brand.">
	<meta property="og:image" content="../{{ asset('img/meta-image.jpg') }}">
	<meta property="og:image:secure_url" content="{{ asset('img/meta-image.jpg') }}">
	<meta property="og:image:type" content="image/png">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="600">

	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}">
	<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('img/apple-touch-icon-120x120.png') }}">
	<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/apple-touch-icon-152x152.png') }}">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

	<!-- Owl Carousel CSS -->
	<link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.theme.default.min.css') }}">

	<!-- Aos CSS -->
	<link rel="stylesheet" href="{{ asset('plugins/aos/aos.css') }}">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}">

	<!-- Select CSS -->
	<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">

	<!-- Feathericon CSS -->
	<link rel="stylesheet" href="{{ asset('css/feather.css') }}">

	<!-- Main CSS -->
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('css/chatbox.css') }}">

	<!-- chatbox  -->
	<script src="http://localhost:3000/socket.io/socket.io.js"></script>
	
</head>

<body>

	{{-- <div id="global-loader" >
		<div class="loader-img">
			<img src="{{ asset('img/loader.png') }}" class="img-fluid" alt="Global">
		</div>
	</div> --}}
	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<!-- Header -->
		<header class="header header-trans">
			<div class="container-fluid">
				<nav class="navbar navbar-expand-lg header-nav">
					<div class="navbar-header">
						<a id="mobile_btn" href="javascript:void(0);">
							<span class="bar-icon">
								<span></span>
								<span></span>
								<span></span>
							</span>
						</a>
						<a href="" class="navbar-brand logo">
							<img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
						</a>
					</div>
					<div class="main-menu-wrapper">
						<div class="menu-header">
							<a href="index.html" class="menu-logo">
								<img src="{{ asset('img/logo-black.svg') }}" class="img-fluid" alt="Logo">
							</a>
							<a id="menu_close" class="menu-close" href="javascript:void(0);"> <i class="fas fa-times"></i></a>
						</div>
						<ul class="main-nav">
							<li class="active"><a href="index.php?id=">Trang Chủ</a></li>
							<li class="has-submenu">
								<a href="#">Sân Cầu Lông <i class="fas fa-chevron-down"></i></a>
								<ul class="submenu">
									<li class="has-submenu">
										<a href="#">Coaches Map</a>
										<ul class="submenu inner-submenu">
											<li><a href="coaches-map.html">Coaches Map</a></li>
											<li><a href="coaches-map-sidebar.html">Coaches Map Sidebar</a></li>
										</ul>
									</li>
									<li><a href="coaches-grid.html">Coaches Grid</a></li>
									<li><a href="coaches-list.html">Coaches List</a></li>
									<li><a href="coaches-grid-sidebar.html">Coaches Grid Sidebar</a></li>
									<li><a href="coaches-list-sidebar.html">Coaches List Sidebar</a></li>
									<li class="has-submenu">
										<a href="javascript:void(0);">Booking</a>
										<ul class="submenu">
											<li><a href="cage-details.html">Book a Court</a></li>
											<li><a href="coach-details.html">Book a Coach</a></li>
										</ul>
									</li>
									<li><a href="coach-detail.html">Coach Details</a></li>
									<li class="has-submenu">
										<a href="#">Venue</a>
										<ul class="submenu inner-submenu">
											<li><a href="listing-list.html">Venue List</a></li>
											<li><a href="venue-details.html">Venue Details</a></li>
										</ul>
									</li>
									<li><a href="coach-dashboard.html">Coach Dashboard</a></li>
									<li><a href="all-court.html">Coach Courts</a></li>
									<li><a href="add-court.html">List Your Court</a></li>
									<li><a href="coach-chat.html">Chat</a></li>
									<li><a href="coach-earning.html">Earnings</a></li>
									<li><a href="coach-wallet.html">Wallet</a></li>
									<li><a href="coach-profile.html">Profile Settings</a></li>
									<li><a href="invoice.html">Invoice</a></li>
								</ul>
								
							</li>
							<li class="has-submenu">
								<a href="#">Người Dùng <i class="fas fa-chevron-down"></i></a>
								<ul class="submenu">
									<li><a href="user-dashboard.html">User Dashboard</a></li>
									<li><a href="user-bookings.html">Bookings</a></li>
									<li><a href="user-chat.html">Chat</a></li>
									<li><a href="user-invoice.html">Invoice</a></li>
									<li><a href="user-wallet.html">Wallet</a></li>
									<li><a href="user-profile.php">Profile Edit</a></li>
									<li><a href="user-setting-password.html">Change Password</a></li>
									<li><a href="user-profile-othersetting.html">Other Settings</a></li>
								</ul>
								
							</li>
							<li class="has-submenu">
								<a href="#">Diễn Đàn <i class="fas fa-chevron-down"></i></a>
								<ul class="submenu">
								    <li><a href="blog-list.html">Blog List</a></li>
								    <li class="has-submenu">
										<a href="javascript:void(0);">Blog List Sidebar</a>
										<ul class="submenu">
											<li><a href="blog-list-sidebar-left.html">Blog List Sidebar Left</a></li>
											<li><a href="blog-list-sidebar-right.html">Blog List Sidebar Right</a></li>
										</ul>
									</li>
									<li><a href="blog-grid.html">Blog Grid</a></li>
									<li class="has-submenu">
										<a href="javascript:void(0);">Blog Grid Sidebar</a>
										<ul class="submenu">
											<li><a href="blog-grid-sidebar-left.html">Blog Grid Sidebar Left</a></li>
											<li><a href="blog-grid-sidebar-right.html">Blog Grid Sidebar Right</a></li>
										</ul>
									</li>
									<li><a href="blog-details.html">Blog Details</a></li>
									<li class="has-submenu">
										<a href="javascript:void(0);">Blog Details Sidebar</a>
										<ul class="submenu">
											<li><a href="blog-details-sidebar-left.html">Blog Detail Sidebar Left</a></li>
											<li><a href="blog-details-sidebar-right.html">Blog Detail Sidebar Right</a></li>
										</ul>
									</li>
									<li><a href="blog-carousel.html">Blog Carousel</a></li>
								</ul>
							</li>
							<li><a href="contact-us.html">Liên Hệ</a></li>
							<li class="login-link">
								<a href="register.php">Sign Up</a>
							</li>
							<li class="login-link">
								<a href="login.html">Sign In</a>
							</li>
						</ul>
					</div>
					<ul class="nav header-navbar-rht logged-in">
						
								<!-- <a href="login.php"><span><i class="feather-users"></i></span>Đăng Nhập</a> / <a href="register.php">Đăng Ký</a> -->
							
						
					</ul>
				</nav>
			</div>
		</header>
		<!-- /Header -->

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
								<h4>Sân cầu lông cao cấp và Dịch vụ chuyên nghiệp</h4>
								<h1>Chọn <span>Sân Cầu Lông Tốt</span> Và Bắt Đầu Hành Trình Rèn Luyện</h1>
								<p class="sub-info">Giải phóng tiềm năng thể thao của bạn với cơ sở vật chất hiện đại và dịch vụ chuẩn thi đấu.</p>
								<div class="search-box">
									
									<form action="{{ asset('view/khachhang/timKiemSan.php') }}" method="GET">
										<input type="search" name="keyword" placeholder="Bạn cần tìm gì" autocomplete="off" required style="width: 90%; border-radius: 10px; margin-right: 10px; border: solid 1px black;">
										<input type="submit" class="btn btn-gradient pull-right write-review add-review" name="btn" id="btn" value="Search">
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
								<p>Đăng ký nhanh chóng và dễ dàng: Bắt đầu sử dụng nền tảng phần mềm của chúng tôi bằng quy trình tạo tài khoản đơn giản.</p>
								<a class="btn" href="register.php">
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
								<p>Đặt sân cầu lông nhanh chóng để được trải nghiệm cơ sở vật chất cao cấp và dịch vụ chuyên nghiệp.</p>
								<a class="btn" href="listing-grid.php?id=">
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
								<p>Dễ dàng đặt chỗ, thanh toán và tận hưởng trải nghiệm liền mạch trên nền tảng thân thiện với người dùng của chúng tôi.</p>
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
					<p class="sub-title">Các địa điểm thể thao tiên tiến cung cấp cơ sở vật chất mới nhất, môi trường năng động và độc đáo để nâng cao hiệu suất chơi cầu lông.</p>
				</div>
				<div class="row">
			        <div class="featured-slider-group ">
			        	<div class="owl-carousel featured-venues-slider owl-theme">

							<!-- Featured Item -->
							
						    

						</div>	
					</div>
				</div>

				<!-- View More -->
				<div class="view-all text-center aos" data-aos="fade-up">
					<a href="listing-grid.php" class="btn btn-secondary d-inline-flex align-items-center">Xem tất cả<span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span></a>
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
					<p class="sub-title">Thúc đẩy sự xuất sắc và thúc đẩy sự phát triển của thể thao thông qua các dịch vụ phù hợp cho vận động viên, huấn luyện viên và người đam mê.</p>
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
							<h2>Bắt đầu hành trình của bạn cùng <span class="active-sport">Dreamsports Badminton</span> ngay hôm nay.</h2>
							<p>Tại DreamSports Badminton, chúng tôi ưu tiên sự hài lòng của bạn và coi trọng phản hồi của bạn khi chúng tôi liên tục cải thiện và phát triển trải nghiệm học tập của mình.</p>
							<p>Sân cầu lông của chúng tôi sử dụng cơ sở vật chất hiện đại để tăng cường hiệu quả cũng như là trải nghiệm dành cho những người từ mới chơi đến những người đã chơi lâu năm.</p>
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
					<p class="sub-title">Khám phá khả năng của bản thân với cơ sở vật chất tiên tiến. Hãy tham gia cùng chúng tôi để cải thiện sức khỏe của bạn.</p>
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
								<p>Tìm kiếm huấn luyện viên cầu lông tư nhân và các học viện để có phương pháp tiếp cận cá nhân hóa nhằm nâng cao kỹ năng.</p>
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
								<p>Cửa hàng cung cấp cho bạn thiết bị cầu lông chất lượng cao, nâng cao hiệu suất trên sân của bạn.</p>
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
								<p>Nâng cao kỹ năng cầu lông của bạn với các bài học sáng tạo, kết hợp các kỹ thuật và phương pháp đào tạo hiện đại.</p>
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
								<p>Nâng cao trò chơi của bạn với các bài học hấp dẫn và cộng đồng hỗ trợ. Tham gia với chúng tôi ngay bây giờ và đưa kỹ năng của bạn lên một tầm cao mới.</p>
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
								<p>Tận hưởng các buổi cầu lông không bị gián đoạn tại DreamSports với dịch vụ cho thuê sân cao cấp của chúng tôi.</p>
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
					<p class="sub-title">Nâng cao hành trình cầu lông của bạn cùng DreamSports: Quyền lợi độc quyền, dịch vụ đặc biệt.</p>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="best-service-img aos" data-aos="fade-up">
							<img src="{{ asset('img/best-service.jpg') }}" class="img-fluid" alt="Service">
							<div class="service-count-blk">
								<div class="coach-count">
									<h3>Sân Cầu Lông</h3>
									<h2><span class="counter-up" >88</span>+</h2>
									<h4>Sân được bảo trì tốt mang lại trải nghiệm chơi cầu lông tối ưu.</h4>
								</div>
								<div class="coach-count coart-count">
									<h3>Huấn Luyện Viên</h3>
									<h2><span class="counter-up" >59</span>+</h2>
									<h4>Huấn luyện viên cầu lông có trình độ cao và chuyên môn sâu rộng trong môn thể thao này.</h4>
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
											<a href="javascript:;" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
												Tôi có thể đặt sân cầu lông tại DreamSports như thế nào?
											</a>
										</h2>
										<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
											<div class="accordion-body">
												<div class="accordion-content">
													<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc khách hàng của chúng tôi để đặt chỗ dễ dàng. </p>
												</div> 
											</div>
										</div>
									</div>
									<!-- /FAQ Item -->

									<!-- FAQ Item -->
									<div class="accordion-item">
										<h2 class="accordion-header" id="headingTwo">
											<a href="javascript:;" class="accordion-button collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
												Thời hạn đặt sân cầu lông là bao lâu?
											</a>
										</h2>
										<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
											<div class="accordion-body">
												<div class="accordion-content">
													<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc khách hàng của chúng tôi để đặt chỗ dễ dàng. </p>
												</div>
											</div>
										</div>
									</div>
									<!-- /FAQ Item -->

									<!-- FAQ Item -->
									<div class="accordion-item">
										<h2 class="accordion-header" id="headingThree">
											<a href="javascript:;" class="accordion-button collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
												Tôi có thể thuê dụng cụ cầu lông tại DreamSports không?
											</a>
										</h2>
										<div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
											<div class="accordion-body">
												<div class="accordion-content">
													<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc khách hàng của chúng tôi để đặt chỗ dễ dàng.</p>
												</div>
											</div>
										</div>
									</div>
									<!-- /FAQ Item -->

									<!-- FAQ Item -->
									<div class="accordion-item">
										<h2 class="accordion-header" id="headingFour">
											<a href="javascript:;" class="accordion-button collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
												DreamSports có cung cấp dịch vụ huấn luyện nào không?
											</a>
										</h2>
										<div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
											<div class="accordion-body">
												<div class="accordion-content">
													<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc khách hàng của chúng tôi để đặt chỗ dễ dàng.</p>
												</div>
											</div>
										</div>
									</div>
									<!-- /FAQ Item -->

									<!-- FAQ Item -->
									<div class="accordion-item">
										<h2 class="accordion-header" id="headingFive">
											<a href="javascript:;" class="accordion-button collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
												Tôi có thể tham gia các giải đấu hoặc giải đấu cầu lông tại DreamSports không?
											</a>
										</h2>
										<div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionExample">
											<div class="accordion-body">
												<div class="accordion-content">
													<p>Đặt sân cầu lông DreamSports trực tuyến hoặc liên hệ với bộ phận chăm sóc khách hàng của chúng tôi để đặt chỗ dễ dàng.</p>
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
										<div class="listing-details-group"><ul>
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
										<div class="listing-details-group"><ul>
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
										<div class="listing-details-group"><ul>
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
										<div class="listing-details-group"><ul>
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
					<a href="listing-grid.html" class="btn btn-secondary d-inline-flex align-items-center">View All Services <span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span></a>
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
					<p class="sub-title">Những đánh giá nhiệt tình từ những người đam mê cầu lông, giới thiệu các dịch vụ đặc biệt của chúng tôi.</p>
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
										<span > 5.0</span>
								   </div>
									<h5>Personalized Attention</h5>		
									<p>DreamSports' coaching services enhanced my badminton skills. Personalized attention from knowledgeable coaches propelled my game to new heights.</p>
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
										<span > 5.0</span>
								   </div>
									<h5>Quality Matters !</h5>		
									<p>DreamSports' advanced badminton equipment has greatly improved my performance on the court. Their quality range of rackets and shoes made a significant impact.</p>
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
										<span > 5.0</span>
								   </div>
									<h5>Excellent Professionalism !</h5>		
									<p>DreamSports' unmatched professionalism and service excellence left a positive experience. Highly recommended for court rentals and equipment purchases.</p>
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
										<span > 5.0</span>
								   </div>
								   <h5>Quality Matters !</h5>		
								   <p>DreamSports' advanced badminton equipment has greatly improved my performance on the court. Their quality range of rackets and shoes made a significant impact.</p>
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
								<img  src="{{ asset('img/testimonial-icon-01.svg') }}" alt="Brand">
							</div>
							<div class="brand-logos">
								<img  src="{{ asset('img/testimonial-icon-04.svg') }}" alt="Brand">
							</div>
							<div class="brand-logos">
								<img  src="{{ asset('img/testimonial-icon-03.svg') }}" alt="Brand">
							</div>
							<div class="brand-logos">
								<img  src="{{ asset('img/testimonial-icon-04.svg') }}" alt="Brand">
							</div>
							<div class="brand-logos">
								<img  src="{{ asset('img/testimonial-icon-05.svg') }}" alt="Brand">
							</div>
							<div class="brand-logos">
								<img  src="{{ asset('img/testimonial-icon-03.svg') }}" alt="Brand">
							</div>
							<div class="brand-logos">
								<img  src="{{ asset('img/testimonial-icon-04.svg') }}" alt="Brand">
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
					<p class="sub-title">Chọn gói tháng hoặc năm để được truy cập liên tục vào các cơ sở cầu lông cao cấp của chúng tôi. Hãy tham gia cùng chúng tôi và trải nghiệm sự tiện lợi tuyệt vời.</p>
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
									<img  src="{{ asset('img/icons/price-01.svg') }}" alt="Price">
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
											<li class="active"><i class="feather-check-circle"></i>Included : Quality Checked By Envato</li>
											<li class="active"><i class="feather-check-circle"></i>Included : Future Updates</li>
											<li class="active"><i class="feather-check-circle"></i>Technical Support</li>
											<li class="inactive"><i class="feather-x-circle"></i>Add Listing </li>
											<li class="inactive"><i class="feather-x-circle"></i>Approval of Listing</li>
										</ul>
									</div>
									<div class="price-choose">
										<a href="javascript:;" class="btn viewdetails-btn">Choose Plan</a>
									</div>
									<div class="price-footer">
										<p>Use, by you or one client, in a single end product which end users.  charged for. The total price includes the item price and a buyer fee.</p>
									</div>							
								</div>							
						    </div>
							<!-- /Price Card -->

						</div>
						<div class="col-lg-4 d-flex col-md-6">

							<!-- Price Card -->
						    <div class="price-card flex-fill">
								<div class="price-head expert-price">
									<img  src="{{ asset('img/icons/price-02.svg') }}" alt="Price">
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
											<li class="active"><i class="feather-check-circle"></i>Included : Quality Checked By Envato</li>
											<li class="active"><i class="feather-check-circle"></i>Included : Future Updates</li>
											<li class="active"><i class="feather-check-circle"></i>6 Months Technical Support</li>
											<li class="inactive"><i class="feather-x-circle"></i>Add Listing </li>
											<li class="inactive"><i class="feather-x-circle"></i>Approval of Listing</li>
										</ul>
									</div>
									<div class="price-choose active-price">
										<a href="javascript:;" class="btn viewdetails-btn">Choose Plan</a>
									</div>
									<div class="price-footer">
										<p>Use, by you or one client, in a single end product which end users.  charged for. The total price includes the item price and a buyer fee.</p>
									</div>							
								</div>							
						    </div>
							<!-- /Price Card -->
							
						</div>
						<div class="col-lg-4 d-flex col-md-6">

							<!-- Price Card -->
						    <div class="price-card flex-fill">
								<div class="price-head">
									<img  src="{{ asset('img/icons/price-03.svg') }}" alt="Price">
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
											<li class="active"><i class="feather-check-circle"></i>Included : Quality Checked By Envato</li>
											<li class="active"><i class="feather-check-circle"></i>Included : Future Updates</li>
											<li class="active"><i class="feather-check-circle"></i>Technical Support</li>
											<li class="active"><i class="feather-check-circle"></i>Add Listing </li>
											<li class="active"><i class="feather-check-circle"></i>Approval of Listing</li>
										</ul>
									</div>
									<div class="price-choose">
										<a href="javascript:;" class="btn viewdetails-btn">Choose Plan</a>
									</div>
									<div class="price-footer">
										<p>Use, by you or one client, in a single end product which end users.  charged for. The total price includes the item price and a buyer fee.</p>
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
					<p class="sub-title">Cập nhật những thông tin mới nhất từ ​​thế giới cầu lông - luôn được cập nhật và truyền cảm hứng từ những tin tức thú vị và thành tích đáng chú ý trong môn thể thao này.</p>
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
											<span ><i class="feather-calendar"></i>15 May 2023</span>									
										</div>
										<h3 class="listing-title">
											<a href="blog-details.html">Badminton Gear Guide: Must-Have Equipment for Every Player</a>
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
											<span ><i class="feather-calendar"></i>16 Jun 2023</span>												
										</div>
										<h3 class="listing-title">
											<a href="blog-details.html">Badminton Techniques: Mastering the Smash, Drop Shot, and Clear											</a>
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
											<span ><i class="feather-calendar"></i>11 May 2023</span>												
										</div>
										<h3 class="listing-title">
											<a href="blog-details.html">The Evolution of Badminton:From Backyard Fun to Olympic Sport</a>
										</h3>
										<div class="listing-button read-new">
											<ul class="nav">
												<li><a href="javascript:;"><i class="feather-heart"></i>25</a></li>
												<li><a href="javascript:;"><i class="feather-message-square"></i>25</a></li>
											</ul>
											<span><img src="{{ asset('img/icons/clock.svg') }}" alt="Clock">14 Min To Read</span>
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
											<span ><i class="feather-calendar"></i>12 May 2023</span>												
										</div>
										<h3 class="listing-title">
											<a href="blog-details.html">Sports Make Us A Lot Stronger And Healthier Than We Think</a>
										</h3>
										<div class="listing-button read-new">
											<ul class="nav">
												<li><a href="javascript:;"><i class="feather-heart"></i>35</a></li>
												<li><a href="javascript:;"><i class="feather-message-square"></i>35</a></li>
											</ul>
											<span><img src="{{ asset('img/icons/clock.svg') }}" alt="Clock">12 Min To Read</span>
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
					<a href="blog-grid.html" class="btn btn-secondary d-inline-flex align-items-center">View All News <span class="lh-1"><i class="feather-arrow-right-circle ms-2"></i></span></a>
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
										<input type="email" class="form-control" placeholder="Enter Email Address" aria-label="email">
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

		<!-- Footer -->
		<footer class="footer">
			<div class="container">
				<!-- Footer Join -->
				<div class="footer-join aos" data-aos="fade-up">
					<h2>We Welcome Your Passion And Expertise</h2>
					<p class="sub-title">Join our empowering sports community today and grow with us.</p>
					<a href="register.php" class="btn btn-primary"><i class="feather-user-plus"></i> Tham gia cùng chúng tôi</a>
				</div>
				<!-- /Footer Join -->
			
				<!-- Footer Top -->
				<div class="footer-top">
					<div class="row">
						<div class="col-lg-2 col-md-6">
							<!-- Footer Widget -->
							<div class="footer-widget footer-menu">
								<h4 class="footer-title">Contact us</h4>
								<div class="footer-address-blk">
									<div class="footer-call">
										<span>Toll free Customer Care</span>
										<p>+017 123 456 78</p>
									</div>
									<div class="footer-call">
										<span>Need Live Suppot</span>
										<p><a href="https://dreamsports.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="94f0e6f1f5f9e7e4fbe6e0e7d4f1ecf5f9e4f8f1baf7fbf9">[email&#160;protected]</a></p>
									</div>
								</div>
								<div class="social-icon">
									<ul>
										<li>
											<a href="javascript:void(0);" class="facebook" ><i class="fab fa-facebook-f"></i> </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="twitter" ><i class="fab fa-twitter"></i> </a>
										</li>
										<li>
											<a href="javascript:void(0);" class="instagram" ><i class="fab fa-instagram"></i></a>
										</li>
										<li>
											<a href="javascript:void(0);" class="linked-in" ><i class="fab fa-linkedin-in"></i></a>
										</li>
									</ul>
								</div>
							</div>
							<!-- /Footer Widget -->
						</div>
						<div class="col-lg-2 col-md-6">
							<!-- Footer Widget -->
							<div class="footer-widget footer-menu">
								<h4 class="footer-title">Quick Links</h4>
								<ul>
									<li>
										<a href="about-us.html">About us</a>
									</li>
									<li>
										<a href="services.html">Services</a>
									</li>
									<li>
										<a href="events.html">Events</a>
									</li>
									<li>
										<a href="blog-grid.html">Blogs</a>
									</li>
									<li>
										<a href="contact-us.html">Contact us</a>
									</li>
								</ul>
							</div>
							<!-- /Footer Widget -->
						</div>
						<div class="col-lg-2 col-md-6">
							<!-- Footer Widget -->
							<div class="footer-widget footer-menu">
								<h4 class="footer-title">Support</h4>
								<ul>
									<li>
										<a href="contact-us.html">Contact Us</a>
									</li>
									<li>
										<a href="faq.html">Faq</a>
									</li>
									<li>
										<a href="privacy-policy.html">Privacy Policy</a>
									</li>
									<li>
										<a href="terms-condition.html">Terms & Conditions</a>
									</li>
									<li>
										<a href="pricing.html">Pricing</a>
									</li>
								</ul>
							</div>
							<!-- /Footer Widget -->
						</div>
						<div class="col-lg-2 col-md-6">
							<!-- Footer Widget -->
							<div class="footer-widget footer-menu">
								<h4 class="footer-title">Other Links</h4>
								<ul>
									<li>
										<a href="coaches-grid.html">Coaches</a>
									</li>
									<li>
										<a href="listing-grid.html">Sports Venue</a>
									</li>
									<li>
										<a href="coach-details.html">Join As Coach</a>
									</li>
									<li>
										<a href="coaches-map.html">Add Venue</a>
									</li>
									<li>
										<a href="my-profile.html">My Account</a>
									</li>
								</ul>
							</div>
							<!-- /Footer Widget -->
						</div>
						<div class="col-lg-2 col-md-6">
							<!-- Footer Widget -->
							<div class="footer-widget footer-menu">
								<h4 class="footer-title">Our Locations</h4>
								<ul>
									<li>
										<a href="javascript:void(0);">Germany</a>
									</li>
									<li>
										<a href="javascript:void(0);">Russia</a>
									</li>
									<li>
										<a href="javascript:void(0);">France</a>
									</li>
									<li>
										<a href="javascript:void(0);">UK</a>
									</li>
									<li>
										<a href="javascript:void(0);">Colombia</a>
									</li>
								</ul>
							</div>
							<!-- /Footer Widget -->
						</div>
						<div class="col-lg-2 col-md-6">
							<!-- Footer Widget -->
							<div class="footer-widget footer-menu">
								<h4 class="footer-title">Download</h4>
								<ul>
									<li>
										<a href="#"><img src="{{ asset('img/icons/icon-apple.svg') }}" alt="Apple"></a>
									</li>
									<li>
										<a href="#"><img src="{{ asset('img/icons/google-icon.svg') }}" alt="Google"></a>
									</li>
								</ul>
							</div>
							<!-- /Footer Widget -->
						</div>
					</div>
				</div>
				<!-- /Footer Top -->
			</div>
			
			<!-- Footer Bottom -->
			<div class="footer-bottom">
				<div class="container">
					<!-- Copyright -->
					<div class="copyright">
						<div class="row align-items-center">
							<div class="col-md-6">
								<div class="copyright-text">
									<p class="mb-0">&copy; 2023 DreamSports  - All rights reserved.</p>
								</div>
							</div>
							<div class="col-md-6">
								<!-- Copyright Menu -->
								<div class="dropdown-blk">
									<ul class="navbar-nav selection-list">
										<li class="nav-item dropdown">
											<div class="lang-select">
												<span class="select-icon"><i class="feather-globe"></i></span>
												<select class="select">
													<option>English (US)</option>
													<option>UK</option>
													<option>Vietnamese</option>
												</select>
											</div>
										</li>
										<li class="nav-item dropdown">
											<!-- <div class="lang-select">
												<span class="select-icon"></span>
												<select class="select">
													<option>$ USD</option>
													<option>$ Euro</option>
												</select>				
											</div>	 -->
										</li>
									</ul>
								</div>
								<!-- /Copyright Menu -->
							</div>
						</div>
					</div>
					<!-- /Copyright -->
				</div>
			</div>
			<!-- /Footer Bottom -->
			
		</footer>
		<!-- /Footer -->
		<!-- chatbox -->
		<div class="chatbox">
			<input type="checkbox" id="click">
			<label for="click">
			<i class="fab fa-facebook-messenger"></i>
			<i class="fas fa-times"></i>
			</label>
			<div class="wrapper">
				<div class="head-text">
					Bắt đầu chat? - Online
				</div>
				<div class="chat-box">
					<form id="chatRequestForm">
						<!-- Chọn doanh nghiệp đang online -->
						<div class="field">
							<input type="hidden" id="maDN" value="" />
							<input type="hidden" id="tenDN" value="" />
							<select id="companySelect" style="color:black; border: 1px solid red; padding: 5px;">
								<option value="">Loading...</option>
							</select>
						</div>
						<!-- Nội dung yêu cầu -->
						<div class="field textarea">
							<textarea name="message" cols="30" rows="10" placeholder="Yêu cầu của bạn" required></textarea>
						</div>
						 
						<div class="field">
							<input type="hidden" name="maKH" id="maKH" value="">
							<button type="submit">Bắt đầu chat</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /chatbox -->
	</div>
	<!-- /Main Wrapper -->

	<!-- scrollToTop start -->
	<div class="progress-wrap active-progress">
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
		<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;"></path>
		</svg>
	</div>
	<!-- scrollToTop end -->

	

	<!-- jQuery -->
	<script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="{{ asset('') }}js/jquery-3.7.1.min.js" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

	<!-- Select JS -->
	<script src="{{ asset('plugins/select2/js/select2.min.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

	<!-- Owl Carousel JS -->
	<script src="{{ asset('plugins/owl-carousel/owl.carousel.min.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

	<!-- Aos -->
	<script src="{{ asset('plugins/aos/aos.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

	<!-- Counterup JS -->
	<script src="{{ asset('js/jquery.waypoints.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>
	<script src="{{ asset('js/jquery.counterup.min.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

	<!-- Top JS -->
	<script src="{{ asset('js/backToTop.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

	<!-- Custom JS -->
	<script src="{{ asset('js/script.js') }}" type="e4c26da156d9fccf88a221dd-text/javascript"></script>

<script src="../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="e4c26da156d9fccf88a221dd-|49" defer></script><script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"92a5cc7dff1f1a5b","version":"2025.3.0","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}' crossorigin="anonymous"></script>
<script>
    // const maDN = "<?php echo isset($laymaDN) ? $laymaDN : ''; ?>";
    // const tenDN = "<?php echo isset($laytenDN) ? $laytenDN : ''; ?>";
	// if (!maDN && !tenDN) {
	// 	console.error("maDN rỗng hoặc không xác định");
	// } else {
	// 	console.log("maDN lấy từ DB: ", maDN);
	// 	console.log("tenDN lấy từ DB: ", tenDN);
	// }
</script>
<script>
  console.log("Giá trị maKH:", document.getElementById("maKH").value);
</script>
<script>
  const socket = io("http://localhost:3000"); // Nếu server Node.js chạy ở port 3000
</script>
<script src="/quanLySanCauLong-B2B-/html/{{ asset('js/chatbox/customer.js') }}"></script>
</body>

<!-- Mirrored from dreamsports.dreamstechnologies.com/html/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 03 Apr 2025 04:28:07 GMT -->
</html>

