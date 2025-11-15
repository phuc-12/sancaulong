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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	{{--
	<link rel="stylesheet" href="{{ asset('css/chatbox.css') }}"> --}}

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">

</head>

<body>
	<div class="main-wrapper">
		<!-- Header -->
		<header class="header header-trans" id="site-header">
			<div class="container-fluid">
				<nav class="navbar navbar-expand-lg header-nav">
					<div class="navbar-header">
						<a id="mobile_btn" href="javascript:void(0);">
							<span class="bar-icon"><span></span><span></span><span></span></span>
						</a>
						<a class="navbar-brand logo">
							<img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
						</a>
					</div>

					{{-- <div class="main-menu-wrapper">
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
									<li><a href="{{ route('danh_sach_san') }}">Danh sách sân</a></li>
									
								</ul>
							</li>
							<li><a href="#">Liên Hệ</a></li>
						</ul>
					</div> --}}

					<ul class="nav header-navbar-rht">
						@auth
							{{-- <li class="nav-item">
								<form method="POST" action="{{ route('lich_dat_san') }}">
									@csrf
									<input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}">
									<button type="submit" class="nav-link btn btn-outline-light btn-sm">
										<i class="feather-check-circle"></i> Lịch Đặt Của Bạn
									</button>
								</form>

							</li>
							<li class="nav-item">
								<form method="POST" action="{{ route('lich_co_dinh') }}">
									@csrf
									<input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}">
									<button type="submit" class="nav-link btn btn-outline-light btn-sm">
										<i class="feather-check-circle"></i> Lịch cố định
									</button>
								</form>

							</li> --}}
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#"
									id="userDropdown" role="button" data-bs-toggle="dropdown">

									<img src="{{ asset(auth()->user()->avatar ?? 'img/profiles/avatar-05.jpg') }}"
										alt="{{ auth()->user()->fullname ?? 'Avatar' }}" class="rounded-circle me-2"
										width="32">

									<span class="d-none d-md-inline">{{ auth()->user()->fullname }}</span>
								</a>
								<ul class="dropdown-menu dropdown-menu-end">
									<li class="dropdown-header">
										<div class="d-flex align-items-center">

											<img src="{{ asset(auth()->user()->avatar ?? 'img/profiles/avatar-05.jpg') }}"
												alt="{{ auth()->user()->fullname ?? 'Avatar' }}" class="rounded-circle me-2"
												width="40">

											<div>
												<div class="fw-semibold">{{ auth()->user()->fullname }}</div>
											</div>
										</div>
									</li>
									<li>
										<hr class="dropdown-divider">
									</li>
									<li>
										<a class="dropdown-item" href="{{ route('user.profile', ['id' => auth()->id()]) }}">
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
				border-bottom: 1px solid rgba(255, 255, 255, 0.08);
				transition: background 0.3s ease;
			}

			.header-trans.scrolled {
				background: rgba(30, 30, 30, 0.98) !important;
			}

			/* Không ép toàn bộ * thành màu trắng nữa */
			.header-trans .navbar-nav .nav-link,
			.header-trans .navbar-brand,
			.header-trans .main-nav>li>a {
				color: #ffffff !important;
			}

			/* Dropdown sửa màu */
			.dropdown-menu {
				background-color: #ffffff;
				color: #333 !important;
				border-radius: 10px;
				min-width: 220px;
				box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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

	<!-- Bootstrap JS -->
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
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
	@yield('contract_content')
	@yield('payment_contract_content')
	{{-- -------------------------------------- --}}

	<!-- chatbox -->

	<!-- /chatbox -->
	</div>
	<!-- /Main Wrapper -->

	<!-- scrollToTop start -->
	<div class="progress-wrap active-progress">
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
			<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
				style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;">
			</path>
		</svg>
	</div>
	<!-- scrollToTop end -->



	<!-- jQuery -->
	<script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}" type=""></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('js/bootstrap.bundle.min.js') }}" type=""></script>

	<!-- Select JS -->
	<script src="{{ asset('plugins/select2/js/select2.min.js') }}" type="e4c26da156d9fcc"></script>

	<!-- Owl Carousel JS -->
	<script src="{{ asset('plugins/owl-carousel/owl.carousel.min.js') }}" type=""></script>

	<!-- Aos -->
	<script src="{{ asset('plugins/aos/aos.js') }}" type=""></script>

	<!-- Counterup JS -->
	<script src="{{ asset('js/jquery.waypoints.js') }}" type=""></script>
	<script src="{{ asset('js/jquery.counterup.min.js') }}" type=""></script>

	<!-- Top JS -->
	<script src="{{ asset('js/backToTop.js') }}" type=""></script>

	<!-- Custom JS -->
	<script src="{{ asset('js/script.js') }}" type=""></script>

	<script src="../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
		data-cf-settings="e4c26da156d9fccf88a221dd-|49" defer></script>
	<script defer
		src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
		integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
		data-cf-beacon='{"rayId":"92a5cc7dff1f1a5b","version":"2025.3.0","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}'
		crossorigin="anonymous"></script>

	<!-- AOS JS -->
	<script src="{{ asset('plugins/aos/aos.js') }}"></script>
	<script>
		AOS.init();
	</script>

</body>

<!-- Mirrored from dreamsports.dreamstechnologies.com/html/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 03 Apr 2025 04:28:07 GMT -->

</html>