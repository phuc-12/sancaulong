{{-- File: resources/views/users/search_results.blade.php --}}
@extends('layouts.main') {{-- Kế thừa layout chung --}}

@section('search_content') {{-- Sử dụng @yield('content') --}}

    <div class="container" style="margin-top: 150px; margin-bottom: 50px;">

        {{-- Tiêu đề kết quả --}}
        <div class="mb-4">
            <h2 class="h3">Kết quả tìm kiếm cho: "{{ $keyword }}"</h2>
            <p class="text-muted">Tìm thấy {{ $sancaulong->total() }} cơ sở phù hợp.</p>
        </div>

        <div class="row">
            @isset($sancaulong)
                @forelse ($sancaulong as $thongtin)
                    <form method="POST" action="{{ route('chi_tiet_san') }}">
                        @csrf
                        <div class="featured-venues-item aos" data-aos="fade-up"
                            style="width: 380px; height: 582.8px; margin: 10px; float: left;">
                            <div class="listing-item mb-0">
                                <div class="listing-img">
                                    <button type="submit" style="border: white;">
                                        <input type="hidden" name="facility_id" value="{{ $thongtin->facility_id }}">
                                        <img src="{{ asset($thongtin->image) }}" alt="">
                                    </button>
                                    <div class="fav-item-venues">
                                        <span class="tag tag-blue">Đang Hoạt Động</span>
                                        <h5 class="tag tag-primary">
                                            @if(!empty($thongtin->default_price) && $thongtin->default_price !== 'Chưa có giá')
                                                {{ number_format($thongtin->default_price ?? 0) }}
                                                <span>/Giờ</span>
                                            @else
                                                <p><em>Chưa cập nhật</em></p>
                                            @endif
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
                                        <button type="submit" style="background-color: white; border: 1px solid white;">
                                            {{ $thongtin->facility_name }}
                                        </button>
                                    </h3>
                                    <div class="listing-details-group">
                                        <p>{{ $thongtin->description }}</p>
                                        <ul>
                                            <li>
                                                <span>
                                                    <i class="feather-map-pin"></i>{{ $thongtin->address }}
                                                </span>
                                            </li>
                                            <li>
                                                {{-- <span>
                                                    <i class="feather-calendar"></i>Giờ mở cửa: <span class="primary-text">{{
                                                        $thongtin['gioMoCua'] }}</span>
                                                </span> --}}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="listing-button">
                                        <div class="listing-venue-owner">
                                            <button class="btn btn-success">ĐẶT SÂN</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center" role="alert">
                            <h4 class="alert-heading">Không tìm thấy sân!</h4>
                            <p>Không tìm thấy cơ sở nào phù hợp với từ khóa "{{ $keyword }}". Vui lòng thử lại với từ khóa khác.
                            </p>
                            <hr>
                            <a href="{{ route('trang_chu') }}" class="btn btn-secondary mb-0">Quay lại Trang chủ</a>
                        </div>
                    </div>
                @endforelse
            @else
                <p>Dữ liệu chưa được tải.</p>
            @endisset
        </div>

        {{-- Phân trang --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $sancaulong->appends(request()->query())->links() }}
        </div>

    </div>

@endsection