<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamSports</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/aos/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatbox.css') }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
</head>
<body>

<div class="main-wrapper">

<!-- Header -->
<!-- Header -->
<header class="header header-trans" id="site-header">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg header-nav">
            <!-- Logo và Mobile menu -->
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);" class="text-white">
                    <span class="bar-icon"><span></span><span></span><span></span></span>
                </a>
                <a href="{{ route('trang_chu') }}" class="navbar-brand logo">
                    <img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
                </a>
            </div>

            <!-- Menu -->
            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="{{ route('trang_chu') }}" class="menu-logo">
                        <img src="{{ asset('img/logo-black.svg') }}" class="img-fluid" alt="Logo">
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                <ul class="main-nav">
                    <li class="{{ request()->is('/') ? 'active' : '' }}">
                        <a href="{{ route('trang_chu') }}">Trang Chủ</a>
                    </li>
                    <li class="has-submenu">
                        <a href="#">Sân Cầu Lông <i class="fas fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="#">Book a Court</a></li>
                            <li><a href="#">Profile Settings</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Liên Hệ</a></li>
                </ul>
            </div>

            <!-- User -->
            <ul class="nav header-navbar-rht">
                @auth
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm" href="{{ route('user.courts') }}">
                            <i class="feather-check-circle"></i> Sân Của Bạn
                        </a>
                    </li>

                    <!-- Dropdown User -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white"
                           href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar ?? asset('img/profiles/avatar-05.jpg') }}"
                                 class="rounded-circle me-2" width="32" alt="Avatar">
                            <span class="d-none d-md-inline">{{ auth()->user()->fullname }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- Header -->
                            <li class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <img src="{{ auth()->user()->avatar ?? asset('img/profiles/avatar-05.jpg') }}"
                                         class="rounded-circle me-2" width="40" alt="">
                                    <div>
                                        <div class="fw-semibold">{{ auth()->user()->fullname }}</div>
                                        {{-- <small class="text-muted">{{ auth()->user()->email }}</small> --}}
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.profile', auth()->id()) }}">
                                    <i class="fas fa-user me-2"></i> Hồ sơ
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="javascript:void(0)"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                </a>
                                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <div class="nav-link btn btn-outline-light log-register">
                            <a href="{{ route('login') }}"><i class="feather-users"></i> Đăng Nhập</a> /
                            <a href="{{ route('register') }}">Đăng Ký</a>
                        </div>
                    </li>
                @endauth
            </ul>
        </nav>
    </div>
</header>
<!-- /Header -->

<!-- CSS Fix Dropdown -->
<style>
    .header-trans {
        background: rgba(40, 40, 40, 0.92) !important;
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(255,255,255,0.08);
        transition: background 0.3s ease;
    }
    .header-trans.scrolled {
        background: rgba(30, 30, 30, 0.98) !important;
    }
    /* Không ép toàn bộ * thành màu trắng nữa */
    .header-trans .navbar-nav .nav-link,
    .header-trans .navbar-brand,
    .header-trans .main-nav > li > a {
        color: #ffffff !important;
    }

    /* Dropdown sửa màu */
    .dropdown-menu {
        background-color: #ffffff;
        color: #333 !important;
        border-radius: 10px;
        min-width: 220px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 1055;
    }
    .dropdown-menu .dropdown-item {
        color: #333 !important;
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
    }
    .dropdown-menu .dropdown-item:hover {
        background-color: #f1f1f1;
    }
    .dropdown-item.text-danger:hover {
        background: rgba(220, 53, 69, 0.1);
    }
</style>


</div> <!-- /.main-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    // CHẠY SAU KHI DOM LOAD XONG
    document.addEventListener("DOMContentLoaded", function () {
        feather.replace();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const header = document.getElementById('site-header');
        let ticking = false;

        function updateHeader() {
            header.classList.toggle('scrolled', window.scrollY > 30);
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(updateHeader);
                ticking = true;
            }
        });
    });
</script>

{{-- -------------------------------------- --}}
@yield('index_content')
@yield('listing-grid_content')
@yield('venue-details_content')
@yield('login')

{{-- -------------------------------------- --}}

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
<!-- AOS JS -->
<script src="{{ asset('plugins/aos/aos.js') }}"></script>
<script>
	AOS.init();
</script>
</body>

<!-- Mirrored from dreamsports.dreamstechnologies.com/html/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 03 Apr 2025 04:28:07 GMT -->
</html>

