@extends('layouts.main')

@section('my_bookings_content')
		<!-- Breadcrumb -->
		<section class="breadcrumb breadcrumb-list mb-0">
			<span class="primary-right-round"></span>
			<div class="container">
				<h1 class="text-white">Lịch Sử Giao Dịch</h1>
				<ul>
					<li><a href="index.html">Trang Chủ</a></li>
					<li >Lịch Sử Giao Dịch</li>
				</ul>
			</div>
		</section>
		<!-- /Breadcrumb -->

		<!-- Dashboard Menu -->
		<!-- <div class="dashboard-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="dashboard-menu">
							<ul>
								<li>
									<a href="user-dashboard.html">
										<img src="assets/img/icons/dashboard-icon.svg" alt="Icon">
										<span>Dashboard</span>
									</a>
								</li>
								<li>
									<a href="user-bookings.html" class="active">
										<img src="assets/img/icons/booking-icon.svg" alt="Icon">
										<span>My Bookings</span>
									</a>
								</li>
								<li>
									<a href="user-chat.html">
										<img src="assets/img/icons/chat-icon.svg" alt="Icon">
										<span>Chat</span>
									</a>
								</li>
								<li>
									<a href="user-invoice.html">
										<img src="assets/img/icons/invoice-icon.svg" alt="Icon">
										<span>Invoices</span>
									</a>
								</li>
								<li>
									<a href="user-wallet.html">
										<img src="assets/img/icons/wallet-icon.svg" alt="Icon">
										<span>Wallet</span>
									</a>
								</li>
								<li>
									<a href="user-profile.html">
										<img src="assets/img/icons/profile-icon.svg" alt="Icon">
										<span>Profile Setting</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<!-- /Dashboard Menu -->

		<!-- Page Content -->
		<div class="content court-bg">
			<div class="container">

				<!-- Sort By -->
				{{-- <div class="row">
					<div class="col-lg-12">
						<div class="sortby-section court-sortby-section">
							<div class="sorting-info">
								<div class="row d-flex align-items-center">
									<div class="col-xl-7 col-lg-7 col-sm-12 col-12">
										<div class="coach-court-list">
											<ul class="nav">
												<!-- <li><a href="user-bookings.html">Upcoming</a></li> -->
												
											</ul>
										</div>
									</div>
									<div class="col-xl-5 col-lg-5 col-sm-12 col-12">
										<div class="sortby-filter-group court-sortby">
											<div class="sortbyset week-bg">
												<div class="sorting-select">
													<select class="form-control select">
														<option>This Week</option>
														<option>One Day</option>
													</select>
												</div>
											</div>
											<div class="sortbyset">
												<span class="sortbytitle">Sort By</span>
												<div class="sorting-select">
													<select class="form-control select">
														<option>Relevance</option>
														<option>Price</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div> --}}
				<!-- Sort By -->

				<div class="row">
					<div class="col-sm-12">
						<div class="court-tab-content">
							<div class="card card-tableset">
								<div class="card-body">
									<div class="coache-head-blk">
										<div class="row align-items-center">
											<div class="col-md-5">
												<div class="court-table-head">
													<h4>Giao Dịch Của Bạn</h4>
													<p>Theo dõi và quản lý các sân đã hoàn thành của bạn</p>
												</div>
											</div>
											<div class="col-md-7">
												<!-- <div class="table-search-top">
													<div id="tablefilter"></div>
													<div class="request-coach-list">
														<div class="card-header-btns">
															<nav>
																<div class="nav nav-tabs" role="tablist">
																	<button class="nav-link active" id="nav-Recent-tab" data-bs-toggle="tab" data-bs-target="#nav-Recent" type="button" role="tab" aria-controls="nav-Recent" aria-selected="true">Courts</button>
																	<button class="nav-link" id="nav-RecentCoaching-tab" data-bs-toggle="tab" data-bs-target="#nav-RecentCoaching" type="button" role="tab" aria-controls="nav-RecentCoaching" aria-selected="false">Coaches</button>
																</div>
															</nav>
														</div>
													</div>
												</div> -->
											</div>
										</div>
									</div>
									<div>
										
										<div>
											<table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Lastname</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $index=0 @endphp
                                                    @foreach ($invoices as $invoice)
                                                        <tr>
                                                            <td>{{ $index=+1 }}</td>
                                                            <td>Doe</td>
                                                            <td>john@example.com</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
										</div>
										
									</div>
									
								</div>
							</div> 
							
							<!-- <div class="tab-footer">
								<div class="row">
									<div class="col-md-6">
										<div id="tablelength"></div>
									</div>
									<div class="col-md-6">
										<div id="tablepage"></div>
									</div>
								</div>
							</div> -->
						</div>
					</div>
				</div>

			</div>
		</div>
		<!-- /Page Content -->

		<!-- Footer -->
		<footer class="footer">
			<div class="container">
				<!-- Footer Join -->
				<div class="footer-join">
					<h2>We Welcome Your Passion And Expertise</h2>
					<p class="sub-title">Join our empowering sports community today and grow with us.</p>
					<a href="register.html" class="btn btn-primary"><i class="feather-user-plus"></i> Join With Us</a>
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
										<p><a href="https://dreamsports.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="45213720242836352a37313605203d24283529206b262a28">[email&#160;protected]</a></p>
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
										<a href="#"><img src="assets/img/icons/icon-apple.svg" alt="Apple"></a>
									</li>
									<li>
										<a href="#"><img src="assets/img/icons/google-icon.svg" alt="Apple"></a>
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
													<option>Japan</option>
												</select>
											</div>
										</li>
										<li class="nav-item dropdown">
											<div class="lang-select">
												<span class="select-icon"></span>
												<select class="select">
													<option>$ USD</option>
													<option>$ Euro</option>
												</select>				
											</div>	
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
			
@endsection