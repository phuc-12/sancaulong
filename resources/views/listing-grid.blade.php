@extends('layouts.main')

@section('listing-grid_content')
    {{-- Khai báo biến cần thiết cho JS. Giả định Controller đã truyền:
         $danhsachsan: Danh sách sân đã tải lần đầu (ví dụ: 10 sân)
         $hasMoreData: Boolean kiểm tra xem còn dữ liệu để tải nữa hay không --}}

    <section class="breadcrumb breadcrumb-list mb-0">
        <span class="primary-right-round"></span>
        <div class="container">
            <h1 class="text-white">Sân Cầu Lông</h1>
            <ul>
                <li><a href="index.php">Trang Chủ</a></li>
                <li>Sân Cầu Lông</li>
            </ul>
        </div>
    </section>
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="sortby-section">
                        <div class="sorting-info">
                            <div class="row d-flex align-items-center">
                                <div class="col-xl-4 col-lg-3 col-sm-12 col-12">
                                    <div class="count-search">
                                        {{-- Cập nhật tổng số sân thực tế nếu có biến $total_count --}}
                                        <p><span>{{ $total_count ?? 'Nhiều' }}</span> sân đang hoạt động</p> 
                                    </div>
                                </div>
                                <div class="col-xl-8 col-lg-9 col-sm-12 col-12">
                                    <div class="sortby-filter-group">
                                        <div class="grid-listview">
                                            {{-- Giữ nguyên code view --}}
                                        </div>
                                        <div class="sortbyset">
                                            <span class="sortbytitle">Sort By</span>
                                            <div class="sorting-select">
                                                <select class="form-control select">
                                                    <option>Liên Quan</option>
                                                    <option>Giá</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- THÊM ID CHO CONTAINER ĐỂ CHÈN DỮ LIỆU MỚI --}}
            <div class="justify-content-center" id="san-cau-long-list"> 
                @isset($danhsachsan)
                    @forelse ($danhsachsan as $thongtin)
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
                                                {{ number_format($thongtin->courtPrice->default_price ?? 0) }}
                                                <span>/Giờ</span>
                                            </h5>

                                        </div>
                                    </div>
                                    <div class="listing-content" style="height: 317px;">
                                        <div class="list-reviews">
                                            <div class="d-flex align-items-center">
                                                <span class="rating-bg">4.2</span><span>300 Reviews</span>
                                            </div>
                                            <a href="javascript:void(0)" class="fav-icon">
                                                <i class="feather-heart"></i>
                                            </a>
                                        </div>
                                        <h3 class="listing-title">
                                            <button type="submit" style="background-color: white; border: 1px solid white;">
                                                {{ $thongtin->facility_name }}
                                            </button>
                                        </h3>
                                        <div class="listing-details-group">
                                            <p style="height: 48px;">{{ $thongtin['description'] }}</p>
                                            <ul>
                                                <li>
                                                    <span style="height: 48px;">
                                                        <i class="feather-map-pin"></i>{{ $thongtin['address'] }}
                                                        
                                                    </span>
                                                    
                                                </li>
                                                <li>
                                                    @php
                                                            $open = \Carbon\Carbon::parse($thongtin['open_time'])->format('H:i');
                                                            $close = \Carbon\Carbon::parse($thongtin['close_time'])->format('H:i');
                                                        @endphp

                                                        <i class="fa fa-clock-o"></i> {{ $open }} - {{ $close }} 
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="listing-button">
                                            <div class="listing-venue-owner">
                                                <button class="btn btn-success">Đặt sân</button>
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

                    {{-- Nút "TẢI THÊM SÂN CẦU" --}}
                    <div class="col-12 text-center">
                        <div class="more-details">
                            <a href="javascript:void(0)" 
                            id="load-more-btn" 
                            class="btn btn-load"
                            style="display: {{ (isset($hasMoreData) && !$hasMoreData) ? 'none' : 'inline-flex' }}; clear: both;">
                                    TẢI THÊM SÂN CẦU
                                <img src="{{ asset('img/icons/u_plus-square.svg') }}" class="ms-2" alt="img">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    {{-- PHẦN SCRIPT XỬ LÝ AJAX LOAD MORE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const loadMoreBtn = document.getElementById('load-more-btn');
            const sanListContainer = document.getElementById('san-cau-long-list');

            // 1. KHỞI TẠO TRẠNG THÁI
            let currentOffset = {{ count($danhsachsan ?? []) }};
            let isLoading = false;
            const limit = 10; 

            // 2. HÀM TẠO HTML CHO MỘT ITEM SÂN
            function createSanCard(san) {
                // CHÚ Ý: Đã sửa 'giaMocua' (trong JS cũ) thành 'giaMacDinh' (trong Blade/DB)
                const giaFormatted = new Intl.NumberFormat('vi-VN').format(san.giaMacDinh); 

                return `
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
                            <div class="listing-content" style="height: 317px;">
                                <div class="list-reviews">
                                    <div class="d-flex align-items-center">
                                        <span class="rating-bg">4.2</span><span>300 Reviews</span>
                                    </div>
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>
                                </div>
                                <h3 class="listing-title">
                                    <button type="submit" style="background-color: white; border: 1px solid white;">
                                        {{ $thongtin->facility_name }}
                                    </button>
                                </h3>
                                <div class="listing-details-group">
                                    <p style="height: 48px;">{{ $thongtin['description'] }}</p>
                                    <ul>
                                        <li>
                                            <span style="height: 48px;">
                                                <i class="feather-map-pin"></i>{{ $thongtin['address'] }}
                                                
                                            </span>
                                            
                                        </li>
                                        <li>
                                            @php
                                                    $open = \Carbon\Carbon::parse($thongtin['open_time'])->format('H:i');
                                                    $close = \Carbon\Carbon::parse($thongtin['close_time'])->format('H:i');
                                                @endphp

                                                <i class="fa fa-clock-o"></i> {{ $open }} - {{ $close }} 
                                        </li>
                                    </ul>
                                </div>
                                <div class="listing-button">
                                    <div class="listing-venue-owner">
                                        <button class="btn btn-success">Đặt sân</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    </script>

@endsection