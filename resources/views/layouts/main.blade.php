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
	<link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">

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
						<a href="{{ route('trang_chu') }}" class="navbar-brand logo">
							<img src="{{ asset('img/logo.svg') }}" class="img-fluid" alt="Logo">
						</a>
					</div>

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
									<li><a href="{{ route('danh_sach_san') }}">Danh sách sân</a></li>
									{{-- <li><a href="#">Profile Settings</a></li> --}}
								</ul>
							</li>
							<li><a href="#">Liên Hệ</a></li>
						</ul>
					</div>

					<ul class="nav header-navbar-rht">
						@auth
							<li class="nav-item">
								<form method="GET" action="{{ route('lich_dat_san') }}">
									@csrf
									<input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}">
									<button type="submit" class="nav-link btn btn-outline-light btn-sm">
										<i class="feather-check-circle"></i> Lịch Đặt Của Bạn
									</button>
								</form>

							</li>
							<li class="nav-item">
								<form method="GET" action="{{ route('lich_co_dinh') }}">
									@csrf
									<input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}">
									<button type="submit" class="nav-link btn btn-outline-light btn-sm">
										<i class="feather-check-circle"></i> Lịch cố định
									</button>
								</form>

							</li>
							<li class="nav-item">
								<a href="{{ route('chat.history') }}" class="nav-link btn btn-outline-light btn-sm">
									<i class="feather-message-circle"></i> Lịch sử Chat
								</a>
							</li>
							<li class="nav-item dropdown">
								<a class="nav-link d-flex align-items-center text-white" href="#" id="userDropdown"
									role="button" data-bs-toggle="dropdown" aria-expanded="false">

									<img src="{{ asset(auth()->user()->avatar ?? 'img/profiles/avatar-05.jpg') }}"
										alt="{{ auth()->user()->fullname ?? 'Avatar' }}" class="rounded-circle me-2"
										style="width: 32px; height: 32px; object-fit: cover;">

									<span class="d-none d-md-inline me-1">{{ auth()->user()->fullname }}</span>

									<i class="fas fa-chevron-down fa-xs"></i>
								</a>

								<ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
									<li class="dropdown-header">
										<div class="d-flex align-items-center">
											<img src="{{ asset(auth()->user()->avatar ?? 'img/profiles/avatar-05.jpg') }}"
												alt="Avatar" class="rounded-circle me-2"
												style="width: 40px; height: 40px; object-fit: cover;">
											<div>
												<div class="fw-bold text-dark">{{ auth()->user()->fullname }}</div>
												<small class="text-muted">Thành viên</small>
											</div>
										</div>
									</li>
									<li>
										<hr class="dropdown-divider">
									</li>
									<li>
										<a class="dropdown-item" href="{{ route('user.profile', ['id' => auth()->id()]) }}">
											<i class="feather-user me-2"></i> Hồ sơ
										</a>
									</li>
									<li>
										<a class="dropdown-item text-danger" href="javascript:void(0)"
											onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
											<i class="feather-log-out me-2"></i> Đăng xuất
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

	<main id="main-content">
		@yield('index_content')
		@yield('listing-grid_content')
		@yield('venue-details_content')
		@yield('payments_content')
		@yield('login')
		@yield('payment_content')
		{{-- @yield('contract_content')
		@yield('payment_contract_content') --}}
		@yield('my_bookings_content')
		@yield('my_contracts_content')
		@yield('search_content')
		@yield('invoice_details_content')
		@yield('contract_details_content')
		@yield('chat_history_content')
	</main>
	{{-- -------------------------------------- --}}

	<footer class="footer">
		<div class="container">
			<!-- Footer Join -->
			<!-- <div class="footer-join aos" data-aos="fade-up">
				<h2>We Welcome Your Passion And Expertise</h2>
				<p class="sub-title">Join our empowering sports community today and grow with us.</p>
				<a href="register.php" class="btn btn-primary"><i class="feather-user-plus"></i> Tham gia cùng chúng
					tôi</a>
			</div> -->
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
									<p><a href="https://dreamsports.dreamstechnologies.com/cdn-cgi/l/email-protection"
											class="__cf_email__"
											data-cfemail="94f0e6f1f5f9e7e4fbe6e0e7d4f1ecf5f9e4f8f1baf7fbf9">[email&#160;protected]</a>
									</p>
								</div>
							</div>
							<div class="social-icon">
								<ul>
									<li>
										<a href="javascript:void(0);" class="facebook"><i class="fab fa-facebook-f"></i>
										</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="twitter"><i class="fab fa-twitter"></i>
										</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="instagram"><i
												class="fab fa-instagram"></i></a>
									</li>
									<li>
										<a href="javascript:void(0);" class="linked-in"><i
												class="fab fa-linkedin-in"></i></a>
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
								<p class="mb-0">&copy; 2025 DreamSports - All rights reserved.</p>
							</div>
						</div>
						<div class="col-md-6">
							<!-- Copyright Menu -->
							<div class="dropdown-blk">
								<ul class="navbar-nav selection-list">
									<li class="nav-item dropdown">
										<div class="lang-select">
											<!-- <span class="select-icon" style="display: flex;"><i class="feather-globe"></i></span> -->
											<select class="select">
												<option>English (US)</option>
												<option>UK</option>
												<option>Vietnamese</option>
											</select>
										</div>
									</li>
									<li class="nav-item dropdown">
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
	<!-- Chatbot Widget -->
	<button id="chatbot-button" style="padding bottom: 50px;">💬</button>
	<div id="chatbot-box">
		<div id="chat-header">Chatbot Sân Cầu Lông</div>
		<div id="chat-body"></div>
		<div id="quick-actions">
			<!-- <button class="quick-action-btn" data-action="Đặt sân">📅 Đặt sân</button> -->
			<button class="quick-action-btn" data-action="Kiểm tra giờ trống">🔍 Kiểm tra giờ trống</button>
			<button class="quick-action-btn" data-action="Giá sân bao nhiêu">💰 Xem giá</button>
		</div>
		<div id="chat-input-area">
			<input type="text" id="chat-input" placeholder="Nhập tin nhắn..." />
			<button id="chat-send">Gửi</button>
		</div>
	</div>

	<!-- /chatbox -->
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


	<!-- AOS JS -->
	<script src="{{ asset('plugins/aos/aos.js') }}"></script>
	<script>
		AOS.init();
	</script>
	<script>
		var botmanWidget = {
			serverUrl: "{{ url('/botman') }}",
			// ------------------------------------------------------------------

			aboutText: 'Hệ thống Đặt Sân',
			introMessage: "🏸 Xin chào! Gõ 'menu' để bắt đầu.",
			title: 'Trợ Lý Đặt Sân',
			mainColor: '#28a745',
			bubbleBackground: '#28a745',
			headerTextColor: '#ffffff',
			desktopHeight: 500,
			desktopWidth: 370,
			displayMessageTime: true
		};

		const btn = document.getElementById('chatbot-button');
		const box = document.getElementById('chatbot-box');
		const body = document.getElementById('chat-body');
		const input = document.getElementById('chat-input');
		const send = document.getElementById('chat-send');
		const quickActions = document.getElementById('quick-actions');
		const quickActionBtns = document.querySelectorAll('.quick-action-btn');

		// Biến để kiểm tra đã hiển thị lời chào chưa
		let hasShownGreeting = false;

		btn.addEventListener('click', () => {
			const isOpening = box.style.display !== 'flex';
			box.style.display = isOpening ? 'flex' : 'none';

			// Nếu đang mở chatbot và chưa hiển thị lời chào
			if (isOpening && !hasShownGreeting) {
				// Hiển thị lời chào tự động
				setTimeout(() => {
					addMessage('Xin chào 👋! Tôi là AI hỗ trợ đặt sân. Bạn có thể chọn một trong các tùy chọn bên dưới hoặc nhập tin nhắn trực tiếp.', 'bot');
					hasShownGreeting = true;
				}, 300); // Delay nhỏ để UI mở mượt hơn
			}
		});

		// Xử lý click vào nút quick action
		quickActionBtns.forEach(btn => {
			btn.addEventListener('click', () => {
				const action = btn.getAttribute('data-action');
				// Tự động gửi message tương ứng
				input.value = action;
				sendMessage();
				// Ẩn các nút quick action sau khi chọn
				quickActions.style.display = 'none';
			});
		});

		function addMessage(text, from = 'bot') {
			const div = document.createElement('div');
			div.className = from === 'bot' ? 'msg-bot' : 'msg-user';
			// Bot messages có thể chứa HTML (định dạng, emoji), user messages chỉ text
			if (from === 'bot') {
				div.innerHTML = text;
			} else {
				div.textContent = text; // An toàn hơn cho user input
			}
			body.appendChild(div);
			body.scrollTop = body.scrollHeight;
		}

		async function sendMessage() {
			const msg = input.value.trim();
			if (!msg) return;

			addMessage(msg, 'user');
			input.value = '';

			// Ẩn quick actions khi người dùng gửi tin nhắn
			quickActions.style.display = 'none';

			try {
				const res = await fetch('/api/chatbot', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
					body: JSON.stringify({ message: msg })
				});

				if (!res.ok) {
					throw new Error(`Chatbot request failed with status ${res.status}`);
				}

				const data = await res.json();
				addMessage(data.reply ?? 'Xin lỗi, tôi không nhận được phản hồi.', 'bot');

				// Hiển thị lại quick actions sau khi chatbot trả lời (nếu cần)
				// Có thể bỏ comment dòng dưới nếu muốn hiển thị lại nút
				// setTimeout(() => { quickActions.style.display = 'flex'; }, 500);
			} catch (error) {
				console.error(error);
				addMessage('Xin lỗi, chatbot đang gặp sự cố. Vui lòng thử lại sau.', 'bot');
			}
		}

		async function loadHistory() {
			const res = await fetch("/chat/history");
			const data = await res.json();

			if (data.success) {
				data.data.forEach(item => {
					if (item.message) {
						addMessageToUI(item.message, "user");
					} else {
						item.reply.forEach(r => addMessageToUI(r, "bot"));
					}
				});
			}
		}


		send.addEventListener('click', sendMessage);

		input.addEventListener('keypress', (e) => {
			if (e.key === 'Enter') sendMessage();
		});

		// Ẩn quick actions khi người dùng bắt đầu nhập
		input.addEventListener('focus', () => {
			quickActions.style.display = 'none';
		});
	</script>
</body>

<!-- Mirrored from dreamsports.dreamstechnologies.com/html/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 03 Apr 2025 04:28:07 GMT -->

</html>