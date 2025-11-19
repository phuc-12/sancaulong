@extends('layouts.main')

@section('listing-grid_content')
<section class="breadcrumb breadcrumb-list mb-0">
    <span class="primary-right-round"></span>
    <div class="container">
        <h1 class="text-white">Sân Cầu Lông</h1>
        <ul>
            <li><a href="{{ route('trang_chu') }}">Trang Chủ</a></li>
            <li>Sân Cầu Lông</li>
        </ul>
    </div>
</section>

<div class="content">
    <div class="container">

        {{-- Tìm kiếm --}}
        <div class="row mb-4 justify-content-center">
            <div class="col-md-8">
                <form action="{{ route('search.results') }}" method="GET" class="d-flex">
                    <input type="search" name="keyword" class="form-control me-2 rounded" 
                           placeholder="Tìm theo tên sân, địa chỉ..." autocomplete="off">
                    <button type="submit" class="btn btn-gradient">Tìm Kiếm</button>
                </form>
            </div>
        </div>

        {{-- Tổng số sân --}}
        <div class="row mb-3">
            <div class="col text-center">
                <p class="fs-5"><strong>{{ $total_count ?? 'Nhiều' }}</strong> sân đang hoạt động</p>
            </div>
        </div>

        {{-- Danh sách sân --}}
        <div class="row" id="san-cau-long-list">
            @isset($danhsachsan)
                @forelse ($danhsachsan as $thongtin)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <form method="POST" action="{{ route('chi_tiet_san') }}">
                            @csrf
                            <input type="hidden" name="facility_id" value="{{ $thongtin->facility_id }}">
                            <div class="featured-venues-item aos" style="height: 582.8px;">
                                <div class="listing-item mb-0 h-100 shadow-sm">
                                    
                                    {{-- Hình ảnh & Tag --}}
                                    <div class="listing-img position-relative">
                                        <button type="submit" style="border: none; background: transparent; width: 100%; padding: 0;">
                                            <img src="{{ asset($thongtin->image) }}" alt="{{ $thongtin->facility_name }}" style="width: 100%; height: 205px; object-fit: cover;">
                                            <input type="hidden" name="facility_id" value="{{ $thongtin->facility_id }}">
                                        </button>
                                        <div class="fav-item-venues position-absolute top-0 start-0 m-2">
                                            <span class="tag tag-blue">Đang Hoạt Động</span>
                                        </div>
                                        <div class="fav-item-venues position-absolute top-0 end-0 m-2">
                                            <h5 class="tag tag-primary">
                                                {{ number_format($thongtin->courtPrice->default_price ?? 0) }} <span>/Giờ</span>
                                            </h5>
                                        </div>
                                    </div>

                                    {{-- Nội dung --}}
                                    <div class="listing-content p-3" style="height: 317px; display: flex; flex-direction: column;">
                                        <div class="list-reviews d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="rating-bg">4.2</span>
                                                <span class="ms-2">300 Reviews</span>
                                            </div>
                                            <a href="javascript:void(0)" class="fav-icon">
                                                <i class="feather-heart"></i>
                                            </a>
                                        </div>

                                        <h3 class="listing-title mb-2">
                                            <button type="submit" style="background-color: white; border: none; padding: 0; text-align: left; font-size: 1.1rem;">
                                                {{ $thongtin->facility_name }}
                                            </button>
                                        </h3>

                                        <p style="flex-grow: 1; min-height: 50px;">{{ $thongtin->description }}</p>

                                        <ul class="mb-3 list-unstyled">
                                            <li><i class="feather-map-pin me-1"></i>{{ $thongtin->address }}</li>
                                            @php
                                                $open = \Carbon\Carbon::parse($thongtin->open_time)->format('H:i');
                                                $close = \Carbon\Carbon::parse($thongtin->close_time)->format('H:i');
                                            @endphp
                                            <li><i class="fa fa-clock-o me-1"></i>{{ $open }} - {{ $close }}</li>
                                        </ul>

                                        <div class="mt-auto">
                                            <button class="btn btn-success w-100">Đặt sân</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p>Danh sách hiện tại đang trống</p>
                    </div>
                @endforelse
            @else
                <div class="col-12 text-center py-5">
                    <p>Dữ liệu chưa được tải.</p>
                </div>
            @endisset
        </div>

        @if(isset($hasMore) && $hasMore)
            <div class="row">
                <div class="col text-center mt-3">
                    <button id="load-more-btn" class="btn btn-outline-primary">Tải thêm sân cầu</button>
                </div>
            </div>
        @endif

    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const sanListContainer = document.getElementById('san-cau-long-list');

    let offset = {{ count($danhsachsan ?? []) }};
    let isLoading = false;

    function createSanCard(san) {
        const open = new Date('1970-01-01T' + san.open_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        const close = new Date('1970-01-01T' + san.close_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        const gia = new Intl.NumberFormat('vi-VN').format(san.courtPrice?.default_price ?? 0);

        return `
        <div class="col-lg-4 col-md-6 mb-4">
            <form method="POST" action="{{ route('chi_tiet_san') }}">
                @csrf
                <input type="hidden" name="facility_id" value="${san.facility_id}">
                <div class="featured-venues-item aos" style="height: 582.8px;">
                    <div class="listing-item mb-0 h-100 shadow-sm">
                        <div class="listing-img position-relative">
                            <button type="submit" style="border: none; background: transparent; width: 100%; padding: 0;">
                                <img src="${san.image}" alt="${san.facility_name}" style="width: 100%; height: 205px; object-fit: cover;">
                            </button>
                            <div class="fav-item-venues position-absolute top-0 start-0 m-2">
                                <span class="tag tag-blue">Đang Hoạt Động</span>
                            </div>
                            <div class="fav-item-venues position-absolute top-0 end-0 m-2">
                                <h5 class="tag tag-primary">${gia} <span>/Giờ</span></h5>
                            </div>
                        </div>
                        <div class="listing-content p-3" style="height: 317px; display: flex; flex-direction: column;">
                            <div class="list-reviews d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="rating-bg">4.2</span>
                                    <span class="ms-2">300 Reviews</span>
                                </div>
                                <a href="javascript:void(0)" class="fav-icon">
                                    <i class="feather-heart"></i>
                                </a>
                            </div>
                            <h3 class="listing-title mb-2">
                                <button type="submit" style="background-color: white; border: none; padding: 0; text-align: left; font-size: 1.1rem;">
                                    ${san.facility_name}
                                </button>
                            </h3>
                            <p style="flex-grow: 1; min-height: 50px;">${san.description}</p>
                            <ul class="mb-3 list-unstyled">
                                <li><i class="feather-map-pin me-1"></i>${san.address}</li>
                                <li><i class="fa fa-clock-o me-1"></i>${open} - ${close}</li>
                            </ul>
                            <div class="mt-auto">
                                <button class="btn btn-success w-100">Đặt sân</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        `;
    }

    loadMoreBtn?.addEventListener('click', function() {
        if(isLoading) return;
        isLoading = true;

        fetch(`{{ route('load.more.san') }}?offset=${offset}`)
        .then(res => res.json())
        .then(res => {
            res.data.forEach(san => {
                sanListContainer.insertAdjacentHTML('beforeend', createSanCard(san));
            });

            offset += res.data.length;
            isLoading = false;

            if(!res.hasMore) {
                loadMoreBtn.style.display = 'none';
            }
        })
        .catch(err => {
            console.error(err);
            isLoading = false;
        });
    });
});
</script>

@endsection
