@extends('layouts.manager')

@section('manager_content')
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

        table.fixed-table {
                min-width: 1000px;
                border-collapse: collapse;
            }

            table.fixed-table th,
            table.fixed-table td {
                border: 1px solid #ccc;
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
            td div.het-han {
                width: 100%;
                background-color: gray;
                font-weight: 500;
            }

            /* Ô đã được chọn */
            td div.da-chon {
                width: 100%;
                background-color: red;
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
        width: 70px;
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

            .container {
    background: #006d3b;
    padding: 20px;
    border-radius: 15px;
}
/* .slot-btn {
    margin: 3px;
    border-radius: 8px;
    border: 2px solid #ccc;
    padding: 8px 14px;
    background: white;
    color: #000;
} */
.slot-btn.selected { background: #1976d2; color: white; }
.slot-btn.booked { background: #f44336; color: white; }
.slot-btn.pending { background: #ffc107; color: white; }
.slot-btn.locked { background: #9e9e9e; color: white; }

/* Nút chọn sân */
.court-btn {
    border-radius: 10px;
    background: white;
    color: #000;
    border: 2px solid #ccc;
    padding: 10px 20px;
    transition: 0.2s;
}
.court-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
.court-btn.selected {
    background: #1976d2;
    color: white;
    border-color: #1976d2;
}

.legend-item {
    display: inline-block;
    width: 20px; height: 20px;
    margin-right: 6px; border-radius: 4px;
}
.legend-item.trống { background: white; border: 1px solid #ccc; }
.legend-item.chọn { background: #1976d2; }
.legend-item.xácnhận { background: #ffc107; }
.legend-item.đặt { background: #f44336; }
.legend-item.khóa { background: #9e9e9e; }

.slot-btn:hover, .court-btn:hover {
    transform: scale(1.05);
    transition: 0.2s ease;
}
    </style>
        {{-- Phần 2: Điều chỉnh Lịch Đặt (Placeholder) --}}
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quản lý & Điều chỉnh Lịch Đặt</h5>
               
            </div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addContractModal">
                <i class="bi bi-plus-circle-fill me-1"></i>
                Tạo Hợp đồng mới
            </button>
            <div class="content" style="padding-top: 10px;">
            {{-- lưới sân --}}
            <div class="" style="margin: 0; width: 100%;">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-8"></div>
                        <div class="accordion" id="accordionPanel">
                            <div class="accordion-item mb-4" id="overview">
                                <h4 class="accordion-header" id="panelsStayOpen-overview" style="padding: 10px 20px;">
                                    <div class="mb-10">
                                        <label for="date_start" class="form-label" style="font-size: 15px">Ngày bắt đầu</label>
                                        <input type="date" class="form-control" id="date_start" name="date_start" value="{{ $dateStart }}">
                                    </div>
                                    <div class="mb-10">
                                        <label for="date_end" class="form-label" style="font-size: 15px">Ngày kết thúc</label>
                                        <input type="date" class="form-control" id="date_end" name="date_end" value="{{ $dateEnd }}">
                                    </div>
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

                                                                            
                                                                                @if ($isPast)
                                                                                    <td style="background-color: grey">
                                                                                        <div class="het-han"></div>
                                                                                @elseif ($isBooked)
                                                                                    <td style="background-color: red">
                                                                                        <div class="da-chon"></div>
                                                                                @elseif (auth()->check())
                                                                                    <td style="background-color: white">
                                                                                        <div class="slot-btn" style="width: 100%;"
                                                                                            data-user="{{ auth()->id() }}"
                                                                                            data-facility="{{ $thongtinsan->facility_id }}"
                                                                                            data-court="{{ $i }}"
                                                                                            data-date="{{ \Carbon\Carbon::parse($d)->format('d-m-Y') }}"
                                                                                            data-slot="{{ $slot->time_slot_id }}"
                                                                                            data-price="{{ $unitPrice/2 }}"
                                                                                            data-start_time="{{ substr($slot->start_time,0,5) }}"
                                                                                            data-end_time="{{ substr($slot->end_time,0,5) }}">
                                                                                        </div>

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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Modal Tạo Hợp Đồng -->
<div class="modal fade" id="addContractModal" tabindex="-1" aria-labelledby="addContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addContractModalLabel">Tạo Hợp đồng Dài hạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <!-- Accordion Form Thuê Dài Hạn -->
                <div class="accordion-item mb-4" id="includes">

                    <div id="contractFormCollapse" class="accordion-collapse collapse show"
                        aria-labelledby="panelsStayOpen-includes">
                        <div class="accordion-body">

                            <form method="POST" action="{{ route('contract_bookings') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="">
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="">
                                </div> --}}

                                <div class="mb-3">
                                    <label for="phonenumber" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" id="phonenumber" name="phonenumber"
                                        value="">
                                </div>

                                <div class="mb-4">
                                    <label for="contract_date_start" class="form-label fw-bold">
                                        Ngày bắt đầu <small class="text-muted">(Cách ngày hiện tại 1 tuần)</small>
                                    </label>
                                    <input type="date" class="form-control shadow-sm" id="contract_date_start" name="date_start"
                                        style="border-radius: 12px; padding: 12px; border: 1px solid #ddd;">
                                </div>

                                <div class="mb-4">
                                    <label for="contract_date_end" class="form-label fw-bold">
                                        Ngày kết thúc <small class="text-muted">(Chọn từ 2 tuần trở lên)</small>
                                    </label>
                                    <input type="date" class="form-control shadow-sm" id="contract_date_end" name="date_end"
                                        style="border-radius: 12px; padding: 12px; border: 1px solid #ddd;">
                                </div>


                                <input type="hidden" name="facility_id" value="{{ $thongtinsan->facility_id }}">
                                <input type="hidden" name="user_id" value="{{ $customer->user_id }}">

                                <button type="submit" class="btn btn-secondary w-100">
                                    Gửi Yêu Cầu
                                </button>

                            </form>
                            <script>

                                // Lấy các input
                                const startInput = document.getElementById('contract_date_start');
                                const endInput = document.getElementById('contract_date_end');

                                // Ngày hiện tại + 1 tuần
                                const today = new Date();
                                const minStart = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 8);
                                startInput.min = minStart.toISOString().split('T')[0]; // định dạng YYYY-MM-DD

                                // Khi thay đổi ngày bắt đầu
                                startInput.addEventListener('change', () => {
                                    const startDate = new Date(startInput.value);
                                    if (startDate) {
                                        // Ngày kết thúc tối thiểu = ngày bắt đầu + 2 tuần
                                        const minEnd = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 16);
                                        endInput.min = minEnd.toISOString().split('T')[0];
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<a id="list">
    @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</a>
<div class="table-responsive rounded-3 mt-3">
    <form method="GET" action="#list" class="mb-3 d-flex justify-content-between align-items-center border">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control w-50" placeholder="Tìm theo khách hàng, sân, tình trạng hoặc tổng tiền...">
        <button type="submit" class="btn btn-primary ms-2">Tìm kiếm</button>
    </form>

    <table class="table table-hover table-bordered align-middle">
        <thead class="table-primary text-center">
            <tr>
                <th>STT</th>
                <th>Tên sân</th>
                <th>Khách hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Ngày bắt đầu</th>
                <th>Sử dụng</th>
                <th>Tình trạng</th>
            </tr>
        </thead>
        <tbody>
            @php $index = 0; @endphp
            @forelse ($long_term_contracts as $ct)
                @php
                    // Kiểm tra xem invoice_detail_id có tồn tại trong mảng chi tiết không
                    $details = isset($mycontract_details[$ct->invoice_detail_id]) ? $mycontract_details[$ct->invoice_detail_id] : null;
                    $firstBooking = ($details && count($details)) ? $details->first() : null;
                    $bookingDate = $firstBooking->booking_date ?? null;
                    $isExpired = $bookingDate ? \Carbon\Carbon::parse($bookingDate)->lt(\Carbon\Carbon::today()) : false;
                @endphp

                <tr class="text-center">
                    <td>{{ ++$index }}</td>
                    <td class="fw-semibold">{{ $ct->facility_name ?? '---' }}</td>
                    <td>{{ $ct->fullname ?? '---' }}</td>
                    <td>{{ $ct->issue_date ? \Carbon\Carbon::parse($ct->issue_date)->format('d/m/Y H:i:s') : '---' }}</td>
                    <td class="fw-bold text-success">{{ $ct->final_amount ? number_format($ct->final_amount, 0, ',', '.') . '₫' : '---' }}</td>
                    <td>{{ $bookingDate ? \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') : '---' }}</td>
                    <td>
                        @if($isExpired)
                            <span class="badge bg-warning text-dark">Đã quá hạn</span>
                        @else
                            <span class="badge bg-info text-dark">Chưa sử dụng</span>
                        @endif
                    </td>
                    <td>
                        @if ($ct->payment_status === 'Đã Hủy')
                            <span class="badge bg-danger">Đã hủy</span>
                        @elseif ($ct->payment_status === 'Đã sử dụng')
                            <span class="badge bg-primary">Đã sử dụng</span>
                        @else
                            <form action="{{ route('manager.chi_tiet_ct') }}" method="POST">
                                @csrf
                                <input type="hidden" name="invoice_detail_id" value="{{ $ct->invoice_detail_id }}">
                                <input type="hidden" name="slots" value='@json($details ?? [])'>
                                <input type="hidden" name="userMana" value="{{ auth()->id() }}">
                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">Chi tiết</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                        Chưa có lịch đặt nào
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
    <!-- Phân trang -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $long_term_contracts->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>
    <script>
    document.addEventListener('change', function(e) {
        if (e.target.matches('#date_start') || e.target.matches('#date_end')) {
            const dateStart = document.getElementById('date_start').value;
            const dateEnd = document.getElementById('date_end').value;
            if (!dateStart || !dateEnd) return;

            fetch(`{{ route('manager.contracts') }}?start=${dateStart}&end=${dateEnd}`)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(html, 'text/html');
                    const newContent = newDoc.querySelector('.content');
                    document.querySelector('.content').innerHTML = newContent.innerHTML;
                })
                .catch(err => console.error(err));
        }
    });
    </script>
    {{-- (Thêm Modal "Tạo Hợp đồng mới) --}}
@endsection