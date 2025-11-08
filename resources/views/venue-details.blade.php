@if(session('user_id'))
    <p>User ID: {{ session('user_id') }}</p>
@endif
@extends('layouts.main')

@section('venue-details_content')
    <style>
        .venue-info h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }
        .venue-info ul li {
            list-style: none;
            margin-right: 20px;
            color: #555;
            font-size: 14px;
        }
        .venue-info ul li i {
            color: #28a745;
            margin-right: 6px;
        }
        .bannergallery-section img {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .showphotos a {
            background: #fff;
            border: 1px solid #ddd;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            color: #333;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .details li {
            display: flex;
            align-items: center;
            margin-right: 25px;
        }
        .primary-text {
            color: #28a745;
            font-weight: bold;
        }
        .social-options li a{
            font-size:15px;
            color:#555;
            margin-right:15px;
        }
        .hero-banner {
            position: relative;
            width: 100%;
            height: 320px; /* Bạn chỉnh thấp/cao hơn tùy thích */
            background: url('{{ asset('img/venues/' . $thongtinsan->image) }}') center/cover no-repeat;
            border-radius: 10px;
            overflow: hidden;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%; /* Làm tối hình 1 chút để chữ rõ */
        }

        .hero-content {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
        }

        .hero-content p {
            font-size: 15px;
            margin: 0;
        }

        table.fixed-table {
                min-width: 1000px;
                border-collapse: collapse;
            }

            table.fixed-table th,
            table.fixed-table td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: center;
                white-space: nowrap;
            }

            thead th {
                position: sticky;
                top: 0;
                background-color: white;
                z-index: 2;
            }

            

            thead .sticky-col {
                z-index: 4; /* để cột đầu của thead nổi hơn */
            }

            /* Cố định cột đầu tiên (Khung giờ) */
            .sticky-col {
                position: sticky;
                left: 0;
                background: white;
                z-index: 2;
                font-weight: bold;
            }

            /* Ô đã quá hạn */
            td span.het-han {
                color: gray;
                font-weight: 500;
            }

            /* Ô đã được chọn */
            td span.da-chon {
                color: red;
                font-weight: bold;
            }

            /* Button đặt giờ */
            td form button {
                background-color: white;
                border: 2px solid #007F7F;
                color: #007F7F;
                padding: 6px 10px;
                font-weight: bold;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s ease-in-out;
                width: 100%;
            }

            /* Hover hiệu ứng */
            td form button:hover {
                background-color: #007F7F;
                color: white;
            }

            /* Trạng thái nhấn */
            td form button:active {
                transform: scale(0.98);
                background-color: #005f5f;
            }

            /* Container cho các nút ở hàng đầu tiên */
            .venue-options-styled {
                display: flex;
                gap: 10px; /* Khoảng cách giữa các nút */
                margin-bottom: 10px; /* Khoảng cách với hàng thứ hai */
            }

            /* Container cho nút ở hàng thứ hai */
            .venue-options-styled-row2 {
                display: flex;
                gap: 10px;
            }

            /* Định kiểu chung cho tất cả các nút */
            .option-button {
                /* Đặt màu chữ và nền mặc định (Trắng) */
                color: #000;
                background-color: #fff;
                border: 1px solid #e0e0e0; /* Viền rất nhạt */
                border-radius: 6px; /* Bo góc */
                padding: 8px 15px; /* Đệm bên trong */
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease; /* Hiệu ứng chuyển đổi mượt */
                white-space: nowrap;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); /* Bóng đổ nhẹ */
            }

            /* Nút slot mặc định */
    .slot-btn {
        width: 100%;
        height: 35px;
        border: 1px solid #ddd;
        background-color: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }

    /* Hover trên slot chưa chọn */
    .slot-btn:not(.selected):hover {
        background-color: #f7f7f7; /* nền xám nhạt */
        border-color: #ccc;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Khi nhấn giữ chuột */
    .slot-btn:active {
        transform: scale(0.98);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Khi đã chọn */
    .slot-btn.selected {
        background-color: #28a745;
        color: #fff;
        border-color: #28a745;
    }

    /* Hover trên slot đã chọn (nếu muốn) */
    .slot-btn.selected:hover {
        background-color: #218838; /* màu xanh đậm hơn khi hover */
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transform: translateY(-1px);
    }

            
    </style>

<!-- Banner Full Width + Header nhỏ -->
<div class="container-fluid px-0 mb-4">
    <div class="hero-banner">
        <div class="hero-content">
            {{-- <h1>{{ $thongtinsan->facility_name }}</h1> --}}
            {{-- <p><i class="feather-map-pin"></i> {{ $thongtinsan->address }} &nbsp;
               | &nbsp;<i class="feather-phone-call"></i> {{ $thongtinsan->phone }}
            </p> --}}
        </div>
    </div>
</div>

<!-- Venue Info -->
<div class="venue-info white-bg py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>{{ $thongtinsan->facility_name }} 
                    <span><i class="fas fa-check-double text-success ms-2"></i></span>
                </h1>
                <ul class="d-flex mt-2">
                    <li><i class="feather-map-pin"></i>{{ $thongtinsan->address }}</li>
                    <li><i class="feather-phone-call"></i>{{ $thongtinsan->phone }}</li>
                    <li><i class="feather-mail"></i>{{ $thongtinsan->Users->email }}</li>
                </ul>
            </div>

            <div class="col-lg-6 text-end">
                <ul class="social-options d-flex justify-content-end">
                    <li><a href="#"><i class="feather-share-2"></i> Chia sẻ</a></li>
                    <li><a href="#" class="favour-adds"><i class="feather-star"></i> Lưu yêu thích</a></li>
                    <li class="d-flex align-items-center">
                        <span class="badge bg-success me-2">5.0</span>
                        <div>
                            <div class="rating text-warning">
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                            </div>
                            <a href="#" class="text-muted">15 đánh giá</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr>

        <div class="row align-items-center mt-3">
            <div class="col-md-6">
                <ul class="details d-flex">
                    <li>
                        <img src="{{ asset('img/icons/venue-type.svg') }}" alt="" class="me-2" width="35" style="background-color: green; border-radius: 100px;">
                        <div>
                            <p style="margin-bottom: 0;">Loại sân</p>
                            <h6 class="mb-0">Sân trong nhà</h6>
                        </div>
                    </li>
                    <li>
                        <img src="{{ asset('img/profiles/avatar-01.jpg') }}" alt="" class="rounded-circle me-2" width="35">
                        <div>
                            <p style="margin-bottom: 0;">Được đăng bởi</p>
                            <h6 class="mb-0">Admin</h6>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-md-6 text-end">
                <p class="d-inline-block me-2 mb-0">Giá từ:</p>   
                 <h3 class="primary-text d-inline-block">{{ $thongtinsan->courtPrice?->default_price ?? 'Chưa có giá' }}</span>/Giờ</span></h3>
            </div>
        </div>
    </div>
</div>


    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-8">
                    <div class="venue-options-styled">
                        <a href="#overview" class="option-button">Chọn Khung Giờ</a>
                        <a href="#includes" class="option-button">Thuê Dài Hạn</a>
                        <a href="#rules" class="option-button">Quy Tắc</a>
                        <a href="#amenities" class="option-button">Tiện Nghi</a>
                        <a href="#gallery" class="option-button">Phòng Trưng Bày</a>
                        <a href="#reviews" class="option-button">Đánh Giá</a>
                        <a href="#location" class="option-button">Địa Điểm</a>
                    </div>
                    
                    <!-- Time Slots -->
                    <div class="accordion" id="accordionPanel">
                        <div class="accordion-item mb-4" id="overview">
                            <h4 class="accordion-header" id="panelsStayOpen-overview">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                    Chọn Khung Giờ
                                </button>
                            </h4>
                            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-overview">
                                <div class="accordion-body">
                                    @php
                                        $soLuongSan = $thongtinsan->quantity_court;
                                    @endphp

                                    <ul class="nav nav-tabs" id="sanTabs" role="tablist">
                                        @for ($i = 1; $i <= $soLuongSan; $i++)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $i == 1 ? 'active' : '' }}"
                                                    id="san{{ $i }}-tab" data-bs-toggle="tab"
                                                    data-bs-target="#san{{ $i }}" type="button" role="tab">
                                                    Sân {{ $i }}
                                                </button>
                                            </li>
                                        @endfor
                                    </ul>

                                    <div class="tab-content" id="sanTabsContent">
                                        @for ($i = 1; $i <= $soLuongSan; $i++)
                                            <div class="tab-pane fade {{ $i == 1 ? 'show active' : '' }}" id="san{{ $i }}" role="tabpanel">

                                                <div style="max-height: 500px; overflow-y: auto;">
                                                    <table class="fixed-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="sticky-col">Khung giờ</th>
                                                                @foreach ($dates as $d)
                                                                    <th>{{ $thuTiengViet[date('D', strtotime($d))] }} {{ date('d/m', strtotime($d)) }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($timeSlots as $slot)
                                                                <tr>
                                                                    <td class="sticky-col">
                                                                        {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}
                                                                    </td>

                                                                    @foreach ($dates as $d)
                                                                        @php
                                                                            $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                                                                            $slotDateTime = \Carbon\Carbon::parse($d . ' ' . $slot->start_time, 'Asia/Ho_Chi_Minh');

                                                                            $isPast = $slotDateTime->lt($now);
                                                                            $isBooked = isset($bookingsData[$d][$slot->time_slot_id][$i]);

                                                                            $unitPrice = (strtotime($slot->start_time) >= strtotime('05:00:00') && strtotime($slot->start_time) < strtotime('16:00:00'))
                                                                                ? $thongtinsan->courtPrice->default_price
                                                                                : $thongtinsan->courtPrice->special_price;
                                                                        @endphp

                                                                        <td>
                                                                            @if ($isPast)
                                                                                <span class="het-han">Quá hạn</span>
                                                                            @elseif ($isBooked)
                                                                                <span class="da-chon">Đã đặt</span>
                                                                            @elseif (auth()->check())
                                                                                <button type="button" class="slot-btn" 
                                                                                    data-user="{{ auth()->id() }}"
                                                                                    data-facility="{{ $thongtinsan->facility_id }}"
                                                                                    data-court="{{ $i }}"
                                                                                    data-date="{{ \Carbon\Carbon::parse($d)->format('d-m-Y') }}"
                                                                                    data-slot="{{ $slot->time_slot_id }}"
                                                                                    data-price="{{ $unitPrice/2 }}"
                                                                                    data-start_time="{{ substr($slot->start_time,0,5) }}"
                                                                                    data-end_time="{{ substr($slot->end_time,0,5) }}">
                                                                                </button>

                                                                            @else
                                                                                <a href="{{ route('login') }}" onclick="alert('Vui lòng đăng nhập để đặt sân')">Đăng nhập</a>
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Long-term Booking Form -->
                        <div class="accordion-item mb-4" id="includes">
                            <h4 class="accordion-header" id="panelsStayOpen-includes">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="true" aria-controls="panelsStayOpen-collapseTwo">
                                    Thuê Dài Hạn
                                </button>
                            </h4>
                            <div id="panelsStayOpen-collapseTwo"
                                class="accordion-collapse collapse show" 
                                aria-labelledby="panelsStayOpen-includes">
                                <div class="accordion-body">
                                    <form method="POST" action="{{ route('contract_bookings') }}">
                                        @csrf
                                        <div class="mb-10">
                                            <label for="name" class="form-label">Họ tên</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $customer->fullname ?? '' }}">
                                        </div>
                                        <div class="mb-10">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ $customer->email ?? '' }}">
                                        </div>
                                        <div class="mb-10">
                                            <label for="phonenumber" class="form-label">Số điện thoại</label>
                                            <input type="text" class="form-control" id="phonenumber" name="phonenumber"
                                                value="{{ $customer->phone ?? '' }}">
                                        </div>
                                        <div class="mb-10">
                                            <label for="date_start" class="form-label">Ngày bắt đầu</label>
                                            <input type="date" class="form-control" id="date_start" name="date_start">
                                        </div>
                                        <div class="mb-10">
                                            <label for="date_end" class="form-label">Ngày kết thúc</label>
                                            <input type="date" class="form-control" id="date_end" name="date_end">
                                        </div>
                                        <div class="form-check d-flex justify-content-start align-items-center policy">
                                            <input class="form-check-input" type="checkbox" value="1" id="policy" name="policy" checked>
                                            <label class="form-check-label" for="policy">
                                                Bằng cách nhấp vào 'Gửi yêu cầu', tôi đồng ý với Chính sách bảo mật và Điều khoản sử dụng của Dreamsport
                                            </label>
                                        </div>
                                        <div class="d-grid btn-block">
                                            <input type="hidden" name="facility_id" id="facility_id" value="{{ $thongtinsan->facility_id }}">
                                            <input type="hidden" name="user_id" id="user_id" value="{{ $customer->user_id }}">
                                            <button type="submit"
                                                class="btn btn-secondary d-inline-flex justify-content-center align-items-center"
                                                name="btnthemyeucau">
                                                Gửi Yêu Cầu
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Các accordion khác (rules, amenities, gallery, reviews, location) tương tự -->
                        <div class="accordion-item mb-4" id="rules">
							    <h4 class="accordion-header" id="panelsStayOpen-rules">
							      	<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
							        	Quy Tắc
							      	</button>
							    </h4>
							    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-rules">
							      	<div class="accordion-body">
							        	<ul>
							        		<li><p><i class="feather-alert-octagon"></i>Giày không để lại dấu được khuyến khích sử dụng nhưng không bắt buộc khi chơi cầu lông.</p></li>
							        		<li><p><i class="feather-alert-octagon"></i>Số lượng thành viên tối đa cho mỗi lần đặt chỗ trên mỗi sân cầu lông được Nhà cung cấp địa điểm chấp nhận.</p></li>
							        		<li><p><i class="feather-alert-octagon"></i>Không nuôi thú cưng, không hạt giống, không kẹo cao su, không thủy tinh, không đánh hoặc đu đưa bên ngoài lồng.</p></li>
							        	</ul>
							      	</div>
							    </div>
							</div>
							<div class="accordion-item mb-4" id="amenities">
							    <h4 class="accordion-header" id="panelsStayOpen-amenities">
							      	<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
							        	 Tiện Nghi
							      	</button>
							    </h4>
							    <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-amenities">
							      	<div class="accordion-body">
							        	<ul class="d-md-flex justify-content-between align-items-center">
							        		<li><i class="fa fa-check-circle" aria-hidden="true"></i>Bãi đậu xe</li>
							        		<li><i class="fa fa-check-circle" aria-hidden="true"></i>Nước uống</li>
							        		<li><i class="fa fa-check-circle" aria-hidden="true"></i>Sơ cứu</li>
							        		<li><i class="fa fa-check-circle" aria-hidden="true"></i>Phòng thay đồ</li>
							        		<li><i class="fa fa-check-circle" aria-hidden="true"></i>Vòi sen</li>
							        	</ul>
							      	</div>
							    </div>
							</div>
							<div class="accordion-item mb-4" id="gallery">
							    <h4 class="accordion-header" id="panelsStayOpen-gallery">
							      	<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
							        	 Phòng Trưng Bày
							      	</button>
							    </h4>
							    <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-gallery">
							      	<div class="accordion-body">
							        	<div class="owl-carousel gallery-slider owl-theme">
							        		<a class="corner-radius-10" href="{{ asset('img/gallery/gallery2/gallery-thumb-01.jpg') }}" data-fancybox="gallery3">
												<img class="img-fluid corner-radius-10" alt="Image" src="{{ asset('img/gallery/gallery2/gallery-01.jpg') }}">
											</a>
							        		<a class="corner-radius-10" href="{{ asset('img/gallery/gallery2/gallery-thumb-02.jpg') }}" data-fancybox="gallery3">
												<img class="img-fluid corner-radius-10" alt="Image" src="{{ asset('img/gallery/gallery2/gallery-02.jpg') }}">
											</a>
							        		<a class="corner-radius-10" href="{{ asset('img/gallery/gallery2/gallery-thumb-03.jpg') }}" data-fancybox="gallery3">
												<img class="img-fluid corner-radius-10" alt="Image" src="{{ asset('img/gallery/gallery2/gallery-03.jpg') }}">
											</a>
							        		{{-- <a class="corner-radius-10" href="{{ asset('img/gallery/gallery2/gallery-thumb-01.jpg') }}" data-fancybox="gallery3">
												<img class="img-fluid corner-radius-10" alt="Image" src="{{ asset('img/gallery/gallery2/gallery-01.jpg') }}">
											</a>
							        		<a class="corner-radius-10" href="{{ asset('img/gallery/gallery2/gallery-thumb-02.jpg') }}" data-fancybox="gallery3">
												<img class="img-fluid corner-radius-10" alt="Image" src="{{ asset('img/gallery/gallery2/gallery-02.jpg') }}">
											</a>
							        		<a class="corner-radius-10" href="{{ asset('img/gallery/gallery2/gallery-thumb-03.jpg') }}" data-fancybox="gallery3">
												<img class="img-fluid corner-radius-10" alt="Image" src="{{ asset('img/gallery/gallery2/gallery-03.jpg') }}">
											</a> --}}
							        	</div>
							      	</div>
							    </div>
							</div>
							<div class="accordion-item mb-4" id="reviews">
							    <div class="accordion-header" id="panelsStayOpen-reviews">
							      	<div class="accordion-button d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSix" aria-controls="panelsStayOpen-collapseSix">
							        	<span class="w-75 mb-0">
							        		 Đánh Giá
							        	</span>
							        	<a href="javascript:void(0);" class="btn btn-gradient pull-right write-review add-review" data-bs-toggle="modal" data-bs-target="#add-review">Viết một đánh giá</a>
							      	</div>
							    </div>
							    <div id="panelsStayOpen-collapseSix" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-reviews">
							      	<div class="accordion-body">
							        	<div class="row review-wrapper">
							        		<div class="col-lg-3">
								        		<div class="ratings-info corner-radius-10 text-center">
								        			<h3>4.8</h3>
								        			<span>out of 5.0</span>
								        			<div class="rating">
														<i class="fas fa-star filled"></i>
														<i class="fas fa-star filled"></i>
														<i class="fas fa-star filled"></i>
														<i class="fas fa-star filled"></i>
														<i class="fas fa-star filled"></i>
												   </div>
								        		</div>
								        	</div>
								        	<div class="col-lg-9">
								        		<div class="recommended">
								        			<h5>Recommended by 97% of Players</h5>
								        			<div class="row">
								        				<div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-3">
								        					<p class="mb-0">Quality of service</p>
								        					<ul>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><span>5.0</span></li>
								        					</ul>
								        				</div>
								        				<div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-3">
								        					<p class="mb-0">Quality of service</p>
								        					<ul>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><span>5.0</span></li>
								        					</ul>
								        				</div>
								        				<div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-3">
								        					<p class="mb-0">Quality of service</p>
								        					<ul>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><span>5.0</span></li>
								        					</ul>
								        				</div>
								        				<div class="col-12 col-sm-12 col-md-4 col-lg-4">
								        					<p class="mb-0">Quality of service</p>
								        					<ul>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><span>5.0</span></li>
								        					</ul>
								        				</div>
								        				<div class="col-12 col-sm-12 col-md-4 col-lg-4">
								        					<p class="mb-0">Quality of service</p>
								        					<ul>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><i></i></li>
								        						<li><span>5.0</span></li>
								        					</ul>
								        				</div>
								        			</div>
								        		</div>
								        	</div>
							        	</div>
							        	<!-- Review Box -->
							        	<div class="review-box d-md-flex">
							        		<div class="review-profile">
							        			<img src="{{ asset('img/profiles/avatar-01.jpg') }}" alt="User">
							        		</div>
							        		<div class="review-info">
							        			<h6 class="mb-2 tittle">Amanda Booked on 06/04/2023</h6>
							        			<div class="rating">
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<span class="">5.0</span>
											   </div>
							        			<span class="success-text"><i class="feather-check"></i>Yes, I would book again.</span>
							        			<h6>Absolutely perfect</h6>
							        			<p>If you are looking for a perfect place for friendly matches with your friends or a competitive match, It is the best place.</p>
							        			<ul class="review-gallery clearfix">
							        				<li>
														<a href="{{ asset('img/gallery/gallery-thumb-01.jpg') }}" data-fancybox="gallery">
															<img class="img-fluid" alt="Image" src="{{ asset('img/gallery/gallery-01.jpg') }}">
													  	</a>
							        				</li>
							        				<li>
														<a href="{{ asset('img/gallery/gallery-thumb-02.jpg') }}" data-fancybox="gallery">
															<img class="img-fluid" alt="Image" src="{{ asset('img/gallery/gallery-02.jpg') }}">
													  	</a>
							        				</li>
							        				<li>
														<a href="{{ asset('img/gallery/gallery-thumb-03.jpg') }}" data-fancybox="gallery">
															<img class="img-fluid" alt="Image" src="{{ asset('img/gallery/gallery-03.jpg') }}">
													  	</a>
							        				</li>
							        				<li>
														<a href="{{ asset('img/gallery/gallery-thumb-04.jpg') }}" data-fancybox="gallery">
															<img class="img-fluid" alt="Image" src="{{ asset('img/gallery/gallery-04.jpg') }}">
													  	</a>
							        				</li>
							        				<li>
														<a href="{{ asset('img/gallery/gallery-thumb-05.jpg') }}" data-fancybox="gallery">
															<img class="img-fluid" alt="Image" src="{{ asset('img/gallery/gallery-05.jpg') }}">
													  	</a>
							        				</li>
							        			</ul>
							        			<span class="post-date">Sent on 11/03/2023</span>
							        		</div>
							        	</div>
							        	<!-- /Review Box -->

							        	<!-- Review Box -->
							        	<div class="review-box d-md-flex">
							        		<div class="review-profile">
							        			<img src="{{ asset('img/profiles/avatar-06.jpg') }}" alt="User">
							        		</div>
							        		<div class="review-info">
							        			<h6 class="mb-2 tittle">Amanda Booked on 06/04/2023</h6>
							        			<div class="rating">
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<span class="">5.0</span>
											   </div>
							        			<span class="warning-text"><i class="feather-x"></i>No, I dont want to book again.</span>
							        			<h6>Awesome. Its very convenient to play.</h6>
							        			<p>If you are looking for a perfect place for friendly matches with your friends or a competitive match, It is the best place.</p>
							        			<div class="dull-bg">
							        				<p>Experience badminton excellence at Badminton Academy. Top-notch facilities, well-maintained courts, and a friendly atmosphere. Highly recommended for an exceptional playing experience</p>
							        			</div>
							        		</div>
							        	</div>
							        	<!-- /Review Box -->
							        	<div class="d-flex justify-content-center mt-1">
							        		<button type="button" class="btn btn-load-more d-flex justify-content-center align-items-center">Load More<i class="feather-plus-square"></i></button>
							        	</div>
							      	</div>
							    </div>
							</div>
							<div class="accordion-item" id="location">
							    <h4 class="accordion-header" id="panelsStayOpen-location">
							      	<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSeven" aria-expanded="false" aria-controls="panelsStayOpen-collapseSeven">
							        	 Địa Điểm
							      	</button>
							    </h4>
							    <div id="panelsStayOpen-collapseSeven" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-location">
							      	<div class="accordion-body">
							        	<div class="google-maps">
										    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2967.8862835683544!2d-73.98256668525309!3d41.93829486962529!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89dd0ee3286615b7%3A0x42bfa96cc2ce4381!2s132%20Kingston%20St%2C%20Kingston%2C%20NY%2012401%2C%20USA!5e0!3m2!1sen!2sin!4v1670922579281!5m2!1sen!2sin" height="445" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
										</div>
										<div class="dull-bg d-flex justify-content-start align-items-center mt-3">
											<div class="white-bg me-2">
												<i class="fas fa-location-arrow"></i>
											</div>
											<div class="">
												<h6>Our Venue Location</h6>
												<p>70 Bright St New York, USA</p>
											</div>
										</div>
							      	</div>
							    </div>
							</div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="col-12 col-sm-12 col-md-12 col-lg-4 theiaStickySidebar" style="margin-top: 49px;">
    {{-- THÔNG TIN GIÁ --}}
    <div class="white-bg book-court" style="border-radius: 10px;" align="center">
        <h4 style="padding-top: 10px;">Đặt sân trực tiếp</h4>
        <h5 class="d-inline-block" style="text-align: center">{{ $thongtinsan->facility_name }}</h5>
        <p class="d-inline-block">, có sẵn ngay bây giờ</p>

        <ul class="d-sm-flex align-items-center justify-content-evenly">
            <li>
                <h3 class="d-inline-block primary-text">{{ number_format($thongtinsan->courtPrice->default_price) }}</h3><span>/hr</span>
                <p>Giá Mặc Định</p>
            </li>
            <li><span><i class="feather-plus"></i></span></li>
            <li>
                <h4 class="d-inline-block primary-text">{{ number_format($thongtinsan->courtPrice->special_price) }}</h4><span>/hr</span>
                <p>Giá Giờ Vàng</p>
            </li>
        </ul>
    </div>

    {{-- THÔNG TIN ĐẶT SÂN --}}
    <div class="white-bg" style="padding-top: 30px;">
        <h4 style="text-align: center;">Thông tin đặt sân</h4>
            @if($success_message)
                <div class="alert alert-success">
                    <p>{{ $success_message }}</p>
                </div>
            @else 
            @endif
        <div class="text-end mb-3">
            <strong>Tổng tiền: </strong>
            <span id="total-price" style="color: red; font-size: 20px; font-weight: bold;"><b>0 đ</b></span>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr class="text-center">
                    <th>Sân số</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Ngày</th>
                </tr>
            </thead>
            <tbody >
                {{-- Body sẽ được JS render --}}
            </tbody>
        </table>
        <form id="paymentForm" action="{{ route('thanh.toan') }}" method="POST">
        @csrf
        {{-- THÔNG TIN NGƯỜI ĐẶT --}}
            <div class="mb-3" style="padding: 0px 10px;">
                <h6 class="form-label">Họ tên:</h6>
                <input type="text" name="fullname" class="form-control"
                    value="{{ old('fullname', auth()->user()->fullname ?? '') }}">
            </div>

            <div class="mb-3" style="padding: 0px 10px;">
                <h6 class="form-label">Số điện thoại:</h6>
                <input type="text" name="phone" class="form-control"
                    value="{{ old('phone', auth()->user()->phone ?? '') }}">
            </div>

            <div class="mb-3" style="padding: 0px 10px;">
                <h6 class="form-label">Email:</h6>
                <input type="email" name="email" class="form-control"
                    value="{{ old('email', auth()->user()->email ?? '') }}">
            </div>

            <div class="d-grid">
                <input type="hidden" name="slots" id="slotsInput">
                <input type="hidden" name="user_id" value="{{ $customer->user_id }}">
                <input type="hidden" name="facility_id" value="{{ $thongtinsan->facility_id }}">
                <button type="submit" class="btn btn-secondary d-flex justify-content-center align-items-center" style="width: 100%; margin: 5px 3px; height: 60px;">Thanh toán <i class="feather-arrow-right-circle ms-2"></i></button>
            </div>
        </form>
    </div>
</aside>

            </div>
        </div>
    </div>
<script>
let selectedSlots = []; // lưu các slot đã chọn

function updateAsideTable() {
    const tbody = document.querySelector('.book-court + .white-bg tbody');
    tbody.innerHTML = '';
    let total = 0;

    selectedSlots.forEach((slot, index) => {
        total += slot.price;

        const tr = document.createElement('tr');
        tr.classList.add('text-center');
        tr.innerHTML = `
            <td>${slot.court}</td>
            <td>${slot.start_time}</td>
            <td>${slot.end_time}</td>
            <td>${slot.date}</td>
            
            <td><button type="button" class="btn btn-sm btn-danger remove-slot" data-index="${index}">X</button></td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('total-price').textContent = total.toLocaleString() + ' đ';

    // Thêm sự kiện xóa slot
    document.querySelectorAll('.remove-slot').forEach(btn => {
        btn.addEventListener('click', function() {
            const idx = parseInt(this.dataset.index);
            // Bỏ class selected trên nút slot tương ứng
            if (selectedSlots[idx] && selectedSlots[idx].btnElement) {
                selectedSlots[idx].btnElement.classList.remove('selected');
            }
            // Xóa slot khỏi mảng
            selectedSlots.splice(idx, 1);
            // Render lại bảng
            updateAsideTable();
        });
    });
}


document.querySelectorAll('.slot-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const slotData = {
            court: this.dataset.court,
            date: this.dataset.date,
            start_time: this.dataset.start_time,
            end_time: this.dataset.end_time,
            time_slot_id: this.dataset.slot,
            price: parseFloat(this.dataset.price),
            btnElement: this // Lưu nút để xóa class later
        };

        const existsIndex = selectedSlots.findIndex(s =>
            s.court == slotData.court &&
            s.date == slotData.date &&
            s.start_time == slotData.start_time
        );

        if (existsIndex === -1) {
            selectedSlots.push(slotData);
            this.classList.add('selected');
        } else {
            selectedSlots.splice(existsIndex, 1);
            this.classList.remove('selected');
        }

        updateAsideTable();
    });
});

// Gắn dữ liệu selectedSlots vào input ẩn khi bấm nút "Thanh toán"
    document.getElementById('paymentForm').addEventListener('submit', function (e) {
        document.getElementById('slotsInput').value = JSON.stringify(selectedSlots);
    });
</script>

@endsection