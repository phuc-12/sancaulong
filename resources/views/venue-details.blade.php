@extends('layouts.main')

@section('venue-details_content')
    <!-- Banner Gallery Section -->
    <div class="bannergallery-section">
        <div class="main-gallery-slider owl-carousel owl-theme">
            <div class="gallery-widget-item">
                <a href="{{ asset('img/venues/' . $thongtinsan->image) }}" data-fancybox="gallery1">
                    <img class="img-fluid" alt="Image" src="{{ asset('img/venues/' . $thongtinsan->image) }}">
                </a>
            </div>
        </div>
        <div class="showphotos corner-radius-10">
            <a href="{{ asset('img/venues/' . $thongtinsan->image) }}" data-fancybox="gallery1">
                <i class="fa-regular fa-images"></i> More Photos
            </a>
        </div>
    </div>

    <!-- Venue Info -->
    <div class="venue-info white-bg d-block">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <h1 class="d-flex align-items-center justify-content-start">
                        {{ $thongtinsan->facility_name }}
                        <span class="d-flex justify-content-center align-items-center"><i class="fas fa-check-double"></i></span>
                    </h1>
                    <ul class="d-sm-flex justify-content-start align-items-center">
                        <li><i class="feather-map-pin"></i>{{ $thongtinsan->address }}</li>
                        <li><i class="feather-phone-call"></i>{{ $thongtinsan->phone }}</li>
                        {{-- <li><i class="feather-mail"></i><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></li> --}}
                    </ul>
                </div>
                <!-- ... (các phần khác tương tự) -->
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-8">
                    <div class="venue-options white-bg mb-4">
                        <ul class="clearfix">
                            <li class="active"><a href="#overview">Chọn Khung Giờ</a></li>
                            <li><a href="#includes">Thuê Dài Hạn</a></li>
                            <!-- ... -->
                        </ul>
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
                                                                $now = now();
                                                                $slotDateTime = new \DateTime("$d {$slot->start_time}");
                                                                $isPast = $slotDateTime < $now;
                                                                $isBooked = isset($bookings[$d][$slot->id]);
                                                            @endphp
                                                            <td>
                                                                @if ($isPast)
                                                                    <span class="het-han" title="Khung giờ đã trôi qua">Quá hạn</span>
                                                                @elseif ($isBooked)
                                                                    <span class="da-chon" title="Đặt lúc: {{ $bookings[$d][$slot->id]['ngayTao'] }}">Đã Chọn</span>
                                                                @elseif (auth()->check())
                                                                    <form method="POST" action="{{ route('booking.process') }}">
                                                                        @csrf
                                                                        <input type="hidden" name="maKH" value="{{ $customer->maKH }}">
                                                                        <input type="hidden" name="maSan" value="{{ $thongtinsan->maSan }}">
                                                                        <input type="hidden" name="time_slot_id" value="{{ $slot->id }}">
                                                                        <input type="hidden" name="ngayDat" value="{{ $d }}">
                                                                        <button type="submit">
                                                                            @if (strtotime($slot->start_time) >= strtotime('05:00:00') && strtotime($slot->start_time) < strtotime('16:00:00'))
                                                                                {{ $thongtinsan->giaMacDinh }} K
                                                                            @else
                                                                                {{ $thongtinsan->giaGioVang }} K
                                                                            @endif
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <script>
                                                                        alert("Vui lòng đăng nhập");
                                                                        window.location = "{{ route('login') }}";
                                                                    </script>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Long-term Booking Form -->
                        <div class="accordion-item mb-4" id="includes">
                            <h4 class="accordion-header" id="panelsStayOpen-includes">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                    Thuê Dài Hạn
                                </button>
                            </h4>
                            <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-includes">
                                <div class="accordion-body">
                                    <form method="POST" action="{{ route('longterm.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-10">
                                            <label for="name" class="form-label">Họ tên</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ $customer->tenKH }}">
                                        </div>
                                        <div class="mb-10">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ $customer->email }}">
                                        </div>
                                        <div class="mb-10">
                                            <label for="phonenumber" class="form-label">Số điện thoại</label>
                                            <input type="text" class="form-control" id="phonenumber" name="phonenumber" value="{{ $customer->soDienThoai }}">
                                        </div>
                                        <div class="mb-10">
                                            <label for="soluong" class="form-label">Số lượng sân</label>
                                            <input type="text" class="form-control" id="soluong" name="soluong" placeholder="... sân">
                                        </div>
                                        <div class="mb-10">
                                            <label for="date_start" class="form-label">Ngày bắt đầu</label>
                                            <input type="date" class="form-control" id="date_start" name="date_start">
                                        </div>
                                        <div class="mb-10">
                                            <label for="date_end" class="form-label">Ngày kết thúc</label>
                                            <input type="date" class="form-control" id="date_end" name="date_end">
                                        </div>
                                        <div class="mb-10">
                                            <label for="comments" class="form-label">Ghi chú</label>
                                            <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Nhập các thứ trong tuần"></textarea>
                                        </div>
                                        <div class="form-check d-flex justify-content-start align-items-center policy">
                                            <input class="form-check-input" type="checkbox" value="1" id="policy" name="policy" checked>
                                            <label class="form-check-label" for="policy">
                                                Bằng cách nhấp vào 'Gửi yêu cầu', tôi đồng ý với Chính sách bảo mật và Điều khoản sử dụng của Dreamsport
                                            </label>
                                        </div>
                                        <div class="d-grid btn-block">
                                            <button type="submit" class="btn btn-secondary d-inline-flex justify-content-center align-items-center" name="btnthemyeucau">Gửi Yêu Cầu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Các accordion khác (rules, amenities, gallery, reviews, location) tương tự -->
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="col-12 col-sm-12 col-md-12 col-lg-4 theiaStickySidebar">
                    <div class="white-bg book-court">
                        <h4 class="border-bottom">Đặt sân trực tiếp</h4>
                        <h5 class="d-inline-block">{{ $thongtinsan->facility_name }}</h5><p class="d-inline-block">, có sẵn ngay bây giờ</p>
                        <ul class="d-sm-flex align-items-center justify-content-evenly">
                            <li>
                                <h3 class="d-inline-block primary-text">{{ $thongtinsan->giaMacDinh }}</h3><span>/hr</span>
                                <p>Giá Mặc Định</p>
                            </li>
                            <li><span><i class="feather-plus"></i></span></li>
                            <li>
                                <h4 class="d-inline-block primary-text">{{ $thongtinsan->giaGioVang }}</h4><span>/hr</span>
                                <p>Giá Giờ Vàng</p>
                            </li>
                        </ul>
                    </div>
                    <!-- Các phần khác của sidebar -->
                </aside>
            </div>
        </div>
    </div>
@endsection