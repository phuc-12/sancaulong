@extends('layouts.main')

@section('search_content')
    <div class="container" style="margin-top: 150px; margin-bottom: 50px;">

        {{-- Tiêu đề kết quả --}}
        <div class="mb-4">
            <h2 class="h3">Kết quả tìm kiếm cho: "{{ $keyword }}"</h2>
            <p class="text-muted">Tìm thấy {{ $results->total() }} cơ sở phù hợp.</p>
        </div>

        {{-- Danh sách kết quả --}}
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
                                            <button type="submit">
                                                <input type="hidden" name="facility_id" value="{{ $thongtin['facility_id'] }}">
                                                <img src="{{ asset('img/venues/' . $thongtin->image) }}" alt="">
                                            </button>
                                            <div class="fav-item-venues">
                                                <span class="tag tag-blue">Đang Hoạt Động</span>

                                                <h5 class="tag tag-primary">
                                                    <!-- $thongtin->Court_prices -->
                                                    {{ number_format($thongtin->Court_prices->default_price ?? 0) }}
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
                                                <a
                                                    href="{{ route('chi_tiet_san', ['idSan' => $thongtin->facility_id]) }}">{{ $thongtin->facility_name }}</a>
                                            </h3>
                                            <div class="listing-details-group">
                                                <p>{{ $thongtin['description'] }}</p>
                                                <ul>
                                                    <li>
                                                        <span>
                                                            <i class="feather-map-pin"></i>{{ $thongtin['address'] }}
                                                        </span>
                                                    </li>
                                                    <li>
                                                        {{-- <span>
                                                            <i class="feather-calendar"></i>Giờ mở cửa: <span
                                                                class="primary-text">{{ $thongtin['gioMoCua'] }}</span>
                                                        </span> --}}
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="listing-button">
                                                <div class="listing-venue-owner">
                                                </div>
                                                <a href="{{ route('chi_tiet_san', ['idSan' => $thongtin->facility_id]) }}"
                                                    class="user-book-now">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning text-center" role="alert">
                                    <h4 class="alert-heading">Không tìm thấy sân!</h4>
                                    <p>Không tìm thấy cơ sở nào phù hợp với từ khóa "{{ $keyword }}". Vui lòng thử lại với từ khóa
                                        khác.</p>
                                    <hr>
                                    <a href="{{ route('trang_chu') }}" class="btn btn-secondary mb-0">Quay lại Trang chủ</a>
                                </div>
                            </div>
                        @endforelse
                    @else
                        <p>Dữ liệu chưa được tải.</p>
                    @endisset
                </div>
            </div>
        </div>

        {{-- Phân trang --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $results->appends(request()->query())->links() }}
        </div>

    </div>

@endsection