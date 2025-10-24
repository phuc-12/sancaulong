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
            <div class="row justify-content-center" id="san-cau-long-list"> 

                @isset($danhsachsan)
                    @forelse ($danhsachsan as $san)
                        <div class="featured-venues-item aos" data-aos="fade-up" style="width: 400px; height: 582.8px; margin: 10px; display:inline-block">
                            <div class="listing-item mb-0"> 
                                <div class="listing-img">
                                    <a href="{{ route('chi_tiet_san', ['idSan' => $san->facility_id]) }}">
                                        <img src="{{ asset('img/venues/'.$san->image) }}" alt="">
                                    </a>
                                    <div class="fav-item-venues">
                                        <span class="tag tag-blue">Đang Hoạt Động</span>
                                        {{-- Định dạng tiền tệ cho đẹp --}}
                                        <h5 class="tag tag-primary">{{ number_format($san->Court_prices->default_price, 0, ',', '.') }}<span>/Giờ</span></h5>
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
                                    <h3 class="listing-title" style="height: 64px;">
                                        <a href="{{ url('venue-details.blade.php?maSan=' . $san->facility_id) }}">{{ $san->facility_name }}</a>
                                    </h3>
                                    <div class="listing-details-group">
                                        {{-- Giả định 'ghiChu' là 'moTa' --}}
                                        <p>{{ $san->description ?? 'Không có mô tả' }}</p> 
                                        <ul>
                                            <li>
                                                <span>
                                                    <i class="feather-map-pin"></i>{{ $san->address }}
                                                </span>
                                            </li>
                                            <li>
                                                {{-- <span>
                                                    <i class="feather-calendar"></i>Giờ mở cửa: <span class="primary-text">{{ $san->gioMoCua }}</span>
                                                </span> --}}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="listing-button">
                                        <a href="{{ url('venue-details.blade.php?maSan=' . $san->facility_id) }}" class="user-book-now"><span><i class="feather-calendar me-2"></i></span>Đặt Ngay</a>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <p>Danh sách hiện tại đang trống</p>
                        </div>
                    @endforelse
                @else
                    <div class="col-12 text-center">
                        <p>Dữ liệu chưa được tải.</p>
                    </div>
                @endisset

                {{-- Nút "TẢI THÊM SÂN CẦU" --}}
                <div class="col-12 text-center">
                    <div class="more-details">
                        {{-- THÊM ID VÀ ẨN NÚT NẾU KHÔNG CÒN DỮ LIỆU --}}
                        <a href="javascript:void(0)" 
                           id="load-more-btn" 
                           class="btn btn-load"
                           style="display: {{ (isset($hasMoreData) && !$hasMoreData) ? 'none' : 'inline-flex' }}">
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
        // document.addEventListener('DOMContentLoaded', function () {
            
        //     const loadMoreBtn = document.getElementById('load-more-btn');
        //     const sanListContainer = document.getElementById('san-cau-long-list');
            
        //     // 1. KHỞI TẠO TRẠNG THÁI
        //     let currentOffset = {{ count($danhsachsan ?? []) }};
        //     let isLoading = false;
        //     const limit = 10; 

        //     // 2. HÀM TẠO HTML CHO MỘT ITEM SÂN
        //     function createSanCard(san) {
        //         // CHÚ Ý: Đã sửa 'giaMocua' (trong JS cũ) thành 'giaMacDinh' (trong Blade/DB)
        //         const giaFormatted = new Intl.NumberFormat('vi-VN').format(san.giaMacDinh); 

        //         return `
        //             <div class="featured-venues-item aos" data-aos="fade-up" style="width: 400px; height: 582.8px; margin: 10px; display:inline-block">
        //                 <div class="listing-item mb-0">
        //                     <div class="listing-img">
        //                         <a href="venue-details.blade.php?maSan=${san.maSan}">
        //                             <img src="{{ asset('img/venues/') }}/${san.hinhAnh}" alt="">
        //                         </a>
        //                         <div class="fav-item-venues">
        //                             <span class="tag tag-blue">${san.trangThai || 'Đang Hoạt Động'}</span>
        //                             <h5 class="tag tag-primary">${giaFormatted}<span>/Giờ</span></h5>
        //                         </div>
        //                     </div>
        //                     <div class="listing-content">
        //                         <div class="list-reviews">
        //                             <div class="d-flex align-items-center">
        //                                 <span class="rating-bg">4.2</span><span>300 Reviews</span>
        //                             </div>
        //                             <a href="javascript:void(0)" class="fav-icon">
        //                                 <i class="feather-heart"></i>
        //                             </a>
        //                         </div>
        //                         <h3 class="listing-title" style="height: 64px;">
        //                             <a href="venue-details.blade.php?maSan=${san.maSan}">${san.tenSan}</a>
        //                         </h3>
        //                         <div class="listing-details-group">
        //                             <p>${san.ghiChu || 'Không có mô tả'}</p>
        //                             <ul>
        //                                 <li>
        //                                     <span>
        //                                         <i class="feather-map-pin"></i>${san.diaChi}
        //                                     </span>
        //                                 </li>
        //                                 <li>
        //                                     <span>
        //                                         <i class="feather-calendar"></i>Giờ mở cửa: <span class="primary-text">${san.gioMoCua}</span>
        //                                     </span>
        //                                 </li>
        //                             </ul>
        //                         </div>
        //                         <div class="listing-button">
        //                             <a href="venue-details.blade.php?maSan=${san.maSan}" class="user-book-now"><span><i class="feather-calendar me-2"></i></span>Đặt Ngay</a>
        //                         </div>
        //                     </div>
        //                 </div>
        //             </div>
        //         `;
        //     }

            
    </script>

@endsection