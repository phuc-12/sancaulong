@extends('layouts.manager') {{-- Kế thừa layout Manager --}}

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
    <h1 class="h3 mb-4">Quản lý Sân Bãi & Lịch Đặt</h1>

    {{-- Hiển thị thông báo thành công (nếu có) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Phần 1: Cập nhật Trạng thái Sân --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Cập nhật Trạng thái Sân</h5>
        </div>
        <div class="card-body">
            @if($courts->isEmpty())
                <p class="text-muted">Chưa có sân nào được thêm vào cơ sở này.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Tên Sân</th>
                                <th class="text-center">Trạng thái hiện tại</th>
                                <th>Hành động (Chọn trạng thái mới & Cập nhật)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Lặp qua danh sách sân con --}}
                            @foreach ($courts as $court)
                            <tr>
                                {{-- Tên Sân --}}
                                <td><strong>{{ $court->court_name }}</strong></td>
                                
                                {{-- Trạng thái hiện tại (dùng Badge) --}}
                                <td class="text-center">
                                    @if($court->status == 'Hoạt động')
                                        <span class="badge bg-success">{{ $court->status }}</span>
                                    @elseif($court->status == 'Bảo trì')
                                        <span class="badge bg-warning text-dark">{{ $court->status }}</span>
                                     @elseif($court->status == 'Đóng cửa')
                                        <span class="badge bg-danger">{{ $court->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $court->status ?? 'Chưa rõ' }}</span>
                                    @endif
                                </td>
                                
                                {{-- Hành động (Form cập nhật) --}}
                                <td>
                                    {{-- Mỗi hàng là một form riêng biệt --}}
                                    <form action="{{ route('manager.courts.updateStatus', [
                                        'court' => $court->court_id
                                    ]) }}" method="POST" class="d-flex align-items-center">
                                        @csrf {{-- Token bảo mật --}}
                                        @method('PUT') {{-- Giả lập phương thức PUT --}}
                                        
                                        {{-- Dropdown chọn trạng thái mới --}}
                                        <select class="form-select form-select-sm w-auto me-2" name="status" required>
                                            {{-- Lặp qua các trạng thái hợp lệ từ Controller --}}
                                            @foreach($validStatuses as $statusOption)
                                                <option value="{{ $statusOption }}" 
                                                        {{ $court->status == $statusOption ? 'selected' : '' }}> 
                                                    {{ $statusOption }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                        {{-- Nút Cập nhật --}}
                                        <button type="submit" class="btn btn-sm btn-primary" style="width: 100px;">Cập nhật</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Phần 2: Điều chỉnh Lịch Đặt (Placeholder) --}}
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Quản lý & Điều chỉnh Lịch Đặt</h5>
        {{-- Bạn có thể thêm nút "Tạo lịch đặt mới" ở đây nếu cần --}}
        {{-- <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addBookingModal"><i class="bi bi-plus-circle me-1"></i> Tạo Lịch Đặt</button> --}}
        </div>
        <div class="content" style="padding-top: 30px;">
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
<script>
document.addEventListener('change', function(e) {
    if (e.target.matches('#date_start') || e.target.matches('#date_end')) {
        const dateStart = document.getElementById('date_start').value;
        const dateEnd = document.getElementById('date_end').value;
        if (!dateStart || !dateEnd) return;

        fetch(`{{ route('manager.courts') }}?start=${dateStart}&end=${dateEnd}`)
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


@endsection
