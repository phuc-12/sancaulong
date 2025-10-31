@extends('layouts.main')

@section('contract_content')
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
.slot-btn {
    margin: 3px;
    border-radius: 8px;
    border: 2px solid #ccc;
    padding: 8px 14px;
    background: white;
    color: #000;
}
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
                <h3 class="primary-text d-inline-block">{{ $thongtinsan->Court_prices->default_price }}<span>/giờ</span></h3>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <form id="bookingForm" method="POST" action="{{ route('contracts.preview') }}">
    @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="start_date" class="text-white">Ngày bắt đầu:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $dateStart }}" readonly>
                
            </div>
            <div class="col-md-6">
                <label for="end_date" class="text-white">Ngày kết thúc:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $dateEnd }}" readonly>
            </div>
        </div>

        <h3 class="mb-3 text-white">Lịch đặt sân</h3>
        {{-- Chọn thứ trong tuần --}}
        <div class="mb-3 text-white">
            <label><input type="checkbox" name="dayofweek[]" value="2"> Thứ 2</label>
            <label><input type="checkbox" name="dayofweek[]" value="3"> Thứ 3</label>
            <label><input type="checkbox" name="dayofweek[]" value="4"> Thứ 4</label>
            <label><input type="checkbox" name="dayofweek[]" value="5"> Thứ 5</label>
            <label><input type="checkbox" name="dayofweek[]" value="6"> Thứ 6</label>
            <label><input type="checkbox" name="dayofweek[]" value="7"> Thứ 7</label>
            <label><input type="checkbox" name="dayofweek[]" value="8"> Chủ Nhật</label>
        </div>

        {{-- Lưới chọn khung giờ --}}
        <div class="court-grid">
            @foreach ($timeSlots as $slot)
                <button type="button" class="slot-btn"
                    data-start="{{ $slot->start_time }}"
                    data-end="{{ $slot->end_time }}"
                    data-timeslotid="{{ $slot->time_slot_id }}">
                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                </button>
            @endforeach
        </div>

        {{-- Chọn sân --}}
        <div class="mt-3">
            <h5 class="text-white">Chọn sân</h5>
            <div class="d-flex flex-wrap justify-content-start">
                @foreach ($courts as $court)
                    <div class="court-item text-center mx-2">
                        <button type="button" class="court-btn" data-court="{{ $court->court_id }}">
                            🏸 {{ $court->court_name }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="text-center mt-4">
            <input type="hidden" name="default_price" id="default_price" value="{{ $thongtinsan->Court_prices->default_price }}">
            <input type="hidden" name="special_price" id="special_price" value="{{ $thongtinsan->Court_prices->special_price  }}">
            <input type="hidden" name="facility_id" id="facility_id" value="{{ $thongtinsan->facility_id }}">
            <input type="hidden" name="user_id" id="user_id" value="{{ $customer->user_id }}">
            <button type="submit" class="btn btn-warning px-5 py-2">XÁC NHẬN VÀ THANH TOÁN</button>
        </div>
    </form>
</div>
<script>
    const timeSlots = @json($timeSlots); 
    document.addEventListener('DOMContentLoaded', () => {
    const msg = localStorage.getItem('conflict_message');
    const conflicts = localStorage.getItem('conflicts');

    if (conflicts) {
        const parsed = JSON.parse(conflicts);

        const form = document.getElementById('bookingForm');
        const h3 = form.querySelector('h3');
        let conflictContainer = document.getElementById('conflictContainer');

        if (!conflictContainer) {
            conflictContainer = document.createElement('div');
            conflictContainer.id = 'conflictContainer';
            h3.insertAdjacentElement('afterend', conflictContainer);
        }

        const alert = document.createElement('div');
        alert.classList.add('alert', 'alert-danger', 'mt-3');
        alert.innerHTML = `<strong>${msg}</strong>`;

        // 💥 Tạo bảng hiển thị khung giờ trùng
        const table = document.createElement('table');
        table.classList.add('table', 'table-bordered', 'table-sm', 'mt-2');
        table.innerHTML = `
            <thead class="table-dark">
                <tr>
                    <th>Ngày</th>
                    <th>Sân</th>
                    <th>Khung giờ</th>
                </tr>
            </thead>
            <tbody>
                ${parsed.map(item => {
                    // Lấy thông tin khung giờ tương ứng từ biến timeSlots (JS)
                    const slot = timeSlots.find(s => s.time_slot_id == item.time_slot_id);
                    const timeRange = slot ? `${slot.start_time} - ${slot.end_time}` : 'N/A';
                    return `
                        <tr>
                            <td>${item.date}</td>
                            <td>Sân số ${item.court_id}</td>
                            <td>${timeRange}</td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
        `;

        alert.appendChild(table);
        conflictContainer.innerHTML = '';
        conflictContainer.appendChild(alert);

        localStorage.removeItem('conflicts');
        localStorage.removeItem('conflict_message');
    }
});



document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bookingForm');
    const btnSubmit = document.querySelector('.btn-warning');

    // Toggle chọn khung giờ
    document.querySelectorAll('.slot-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('selected');
        });
    });

    // Toggle chọn sân (nhiều sân)
    document.querySelectorAll('.court-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('selected');
        });
    });

    // Lưu chọn thứ
    const selectedDays = () => Array.from(document.querySelectorAll('input[name="dayofweek[]"]:checked')).map(c => c.value);

    // Lưu khung giờ
    const selectedSlots = () => Array.from(document.querySelectorAll('.slot-btn.selected')).map(btn => ({
        start: btn.dataset.start,
        end: btn.dataset.end,
        timeslotid: btn.dataset.timeslotid
    }));

    // Lưu sân
    const selectedCourts = () => Array.from(document.querySelectorAll('.court-btn.selected')).map(btn => btn.dataset.court);

    // Khi bấm "TIẾP TỤC THANH TOÁN"
    btnSubmit.addEventListener('click', (e) => {
        e.preventDefault();
        
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);
        const facility_id = document.getElementById('facility_id').value;
        const user_id = document.getElementById('user_id').value;
        const default_price = parseFloat(document.getElementById('default_price').value) || 0;
        const special_price = parseFloat(document.getElementById('special_price').value) || 0;
        const days = selectedDays();
        const slots = selectedSlots();
        const courts = selectedCourts();

        if (!startDate || !endDate || days.length === 0 || slots.length === 0 || courts.length === 0) {
            alert('Vui lòng chọn đầy đủ ngày, thứ, khung giờ và sân!');
            return;
        }

        // ✅ Sinh ra các ngày thực tế theo thứ người dùng chọn (kèm slot & court)
        let actualDates = [];
        let current = new Date(startDate);

        while (current <= endDate) {
            const dayOfWeek = current.getDay() === 0 ? 8 : current.getDay() + 1;
            if (days.includes(dayOfWeek.toString())) {
                actualDates.push({
                    date: current.toISOString().split('T')[0],
                    time_slots: slots.slice(0, -1).map(s => s.timeslotid ?? null),
                    courts: courts.map(c => parseInt(c))
                });
            }
            current.setDate(current.getDate() + 1);
        }

        console.log({
            startDate: startDate.toISOString().split('T')[0],
            endDate: endDate.toISOString().split('T')[0],
            selectedDays: days,
            selectedSlots: slots,
            selectedCourts: courts,
            actualDates: actualDates,
            default_price: default_price,
            special_price: special_price,
            facility_id: facility_id,
            user_id: user_id
        });

        // ✅ Gửi dữ liệu sang server
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                start_date: startDate.toISOString().split('T')[0],
                end_date: endDate.toISOString().split('T')[0],
                day_of_weeks: days,
                time_slots: slots,
                courts: courts,
                actual_dates: actualDates,
                default_price: default_price,
                special_price: special_price,
                facility_id: facility_id,
                user_id: user_id
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log('✅ Server trả về:', data);

            if (data.reload) {
                // 🧠 Lưu dữ liệu trùng tạm vào localStorage
                localStorage.setItem('conflicts', JSON.stringify(data.conflicts));
                localStorage.setItem('conflict_message', data.message);

                // 🔁 Reload lại trang
                window.location.reload();
            } else {
                console.log('Không có xung đột');
            }
        })
        .catch(err => {
            console.error('❌ Lỗi gửi dữ liệu:', err);
        });
    });

});
</script>


<!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-8"></div>
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
                                                                                ? $thongtinsan->Court_prices->default_price
                                                                                : $thongtinsan->Court_prices->special_price;
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
                    </div>
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

    document.getElementById('btnFilter').addEventListener('click', function() {
    const selectedCourt = document.getElementById('filterCourt').value;
    const selectedDay = document.getElementById('filterDay').value;
    const selectedTime = document.getElementById('filterTime').value;

    document.querySelectorAll('.slot-btn').forEach(btn => {
        const btnDate = btn.dataset.date;
        const btnCourt = btn.dataset.court;
        const btnStart = btn.dataset.start_time;
        const weekday = new Date(btnDate.split('-').reverse().join('-')).toLocaleDateString('vi-VN', { weekday: 'long' });

        let show = true;
        if (selectedCourt !== 'all' && btnCourt !== selectedCourt) show = false;
        if (selectedDay !== 'all' && weekday.toLowerCase() !== selectedDay.toLowerCase()) show = false;
        if (selectedTime !== 'all' && btnStart !== selectedTime) show = false;

        btn.style.display = show ? 'block' : 'none';
    });
});
</script>
@endsection