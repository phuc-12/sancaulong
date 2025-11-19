@extends('layouts.staff')

@section('staff_content')
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
            background: url('{{ asset($thongtinsan->image) }}') center/cover no-repeat;
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
    <h1 class="h3 mb-4">Đặt lịch trực tiếp</h1>

    <div class="row" style="width:70%; float: left;">
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
    <!-- Sidebar -->
            <aside class="col-12 col-sm-12 col-md-12 col-lg-4 theiaStickySidebar" style="width: 30%; float: right;">
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
                    <form id="paymentForm" action="{{ route('staff.addInvoice') }}" method="POST">
                    @csrf
                    @php
                        $invoice_detail_id = 'new_' . $thongtinsan->facility_id . '_' . date('Ymd_His') .'_'. rand(1000, 9999);
                    @endphp
                    {{-- THÔNG TIN NGƯỜI ĐẶT --}}
                        <div class="mb-3" style="padding: 0px 10px;">
                            <h6 class="form-label">Họ tên:</h6>
                            <input type="text" name="fullname" class="form-control"
                                value="" required>
                        </div>

                        <div class="mb-3" style="padding: 0px 10px;">
                            <h6 class="form-label">Số điện thoại:</h6>
                            <input type="text" name="phone" class="form-control"
                                value="" required>
                        </div>

                        <div class="d-grid">
                            <input type="hidden" name="slots" id="slotsInput">
                            <input type="hidden" name="user_id" value="">
                            <input type="hidden" name="invoice_detail_id" value="{{ $invoice_detail_id }}">
                            <input type="hidden" name="facility_id" value="{{ $thongtinsan->facility_id }}">
                            <button type="submit" class="btn btn-secondary d-flex justify-content-center align-items-center" style="width: 100%; margin: 5px 3px; height: 60px;">Lưu lịch <i class="feather-arrow-right-circle ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </aside>       
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