@extends('layouts.manager') {{-- Kế thừa layout Manager --}}

@section('manager_content')
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
                                    <form action="{{ route('manager.courts.updateStatus', $court->court_id) }}" method="POST" class="d-flex align-items-center">
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
                                        <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
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
         <div class="card-body">
            {{-- Thẻ div này sẽ chứa Lịch --}}
            <div id='bookingCalendar'></div>
         </div>
    </div>
@endsection

{{-- Đẩy CSS và JS của FullCalendar vào layout --}}
@push('scripts')
    {{-- FullCalendar Core và Plugins cần thiết --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

    {{-- JavaScript khởi tạo FullCalendar cho quản lý lịch đặt --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('bookingCalendar'); // Lấy thẻ div chứa lịch

            if (calendarEl) { // Kiểm tra thẻ có tồn tại không
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    // === CẤU HÌNH GIAO DIỆN ===
                    initialView: 'timeGridWeek', // Bắt đầu với view Tuần (có giờ)
                    headerToolbar: { // Các nút điều khiển
                        left: 'prev,next today', // Nút qua lại, hôm nay
                        center: 'title',          // Tiêu đề (Tháng Năm, Ngày...)
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' // Nút chuyển đổi các view
                    },
                    locale: 'vi', // Ngôn ngữ Tiếng Việt
                    buttonText: { // Dịch chữ trên nút
                        today:    'Hôm nay',
                        month:    'Tháng',
                        week:     'Tuần',
                        day:      'Ngày',
                        list:     'Danh sách'
                    },
                    slotMinTime: '05:00:00', // Giờ sớm nhất hiển thị trên lịch
                    slotMaxTime: '23:00:00', // Giờ muộn nhất hiển thị
                    allDaySlot: false,       // Ẩn dòng "Cả ngày"
                    expandRows: true,        // Mở rộng chiều cao các dòng giờ
                    nowIndicator: true,      // Hiển thị vạch chỉ giờ hiện tại
                    handleWindowResize: true,// Tự điều chỉnh kích thước khi cửa sổ thay đổi
                    height: 'auto',          // Chiều cao tự động theo nội dung

                    // === LẤY DỮ LIỆU SỰ KIỆN (BOOKINGS) ===
                    events: {
                        url: '{{ route("manager.bookings.data") }}', // API đã tạo để lấy JSON bookings
                        failure: function(error) {
                            console.error("Lỗi tải lịch đặt:", error);
                            alert('Không thể tải dữ liệu lịch đặt. Vui lòng thử lại.');
                        }
                    },

                    // === TƯƠNG TÁC: KÉO THẢ, RESIZE ===
                    editable: true,       // Bật tính năng kéo thả, thay đổi kích thước
                    eventDrop: function(info) { // Xử lý sau khi kéo thả xong
                        console.log("Event dropped:", info.event);
                        if (!confirm("Bạn có chắc muốn di chuyển lịch đặt này?")) {
                            info.revert(); // Hoàn tác nếu không xác nhận
                        } else {
                            updateBookingTime(info.event); // Gọi hàm gửi cập nhật lên server
                        }
                    },
                    eventResize: function(info) { // Xử lý sau khi thay đổi thời gian xong
                        console.log("Event resized:", info.event);
                         if (!confirm("Bạn có chắc muốn thay đổi thời gian đặt này?")) {
                            info.revert();
                        } else {
                            updateBookingTime(info.event);
                        }
                    },
                    // eventOverlap: false, // Ngăn các sự kiện đè lên nhau (nếu cần)

                    // === XỬ LÝ KHI CLICK VÀO SỰ KIỆN ===
                    eventClick: function(info) {
                        console.log("Event clicked:", info.event);
                        // Ví dụ: Hiển thị thông tin chi tiết trong Modal
                        alert('Khách hàng: ' + info.event.title + '\n' +
                              'Thời gian: ' + info.event.start.toLocaleString() + ' - ' + info.event.end.toLocaleString() + '\n' +
                              'ID: ' + info.event.id);
                        // Bạn có thể mở modal chỉnh sửa chi tiết ở đây
                        // info.jsEvent.preventDefault(); // Ngăn các hành động mặc định (nếu event có URL)
                    },

                     // === THAY ĐỔI MÀU SẮC SỰ KIỆN ===
                    eventDidMount: function(info) {
                      // Bạn có thể thêm class hoặc style dựa trên trạng thái booking (nếu API trả về)
                      // Ví dụ: if (info.event.extendedProps.status === 'pending') { ... }
                    }

                });

                calendar.render(); // Vẽ lịch ra màn hình

                // === HÀM GỬI CẬP NHẬT LÊN SERVER (AJAX) ===
                function updateBookingTime(event) {
                    // Chuẩn bị dữ liệu gửi đi (Start, End theo ISO 8601)
                    const updatedData = {
                        start: event.start.toISOString(),
                        end: event.end.toISOString(),
                        _token: '{{ csrf_token() }}' // Gửi kèm CSRF token (quan trọng)
                    };

                    // Lấy URL từ route name, thay thế :id bằng ID của booking
                    let updateUrl = '{{ route("manager.bookings.updateTime", ["booking" => ":id"]) }}';
                    updateUrl = updateUrl.replace(':id', event.id);

                    // Gửi yêu cầu PUT bằng Fetch API
                    fetch(updateUrl, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Thêm CSRF token vào header
                        },
                        body: JSON.stringify(updatedData) // Chuyển dữ liệu thành JSON string
                    })
                    .then(response => {
                        if (!response.ok) { // Kiểm tra nếu server báo lỗi (vd: 422 Validation Error)
                           return response.json().then(errorData => {
                               throw new Error(errorData.message || 'Lỗi không xác định từ server.');
                           });
                        }
                        return response.json(); // Lấy dữ liệu JSON nếu thành công
                    })
                    .then(data => {
                        console.log('Cập nhật thành công:', data);
                        // Có thể hiển thị thông báo thành công ngắn gọn (Toast)
                        // Ví dụ: bootstrap.Toast(document.getElementById('successToast')).show();
                        // Không cần calendar.refetchEvents() vì FullCalendar tự cập nhật sau khi kéo thả thành công
                    })
                    .catch((error) => {
                        console.error('Lỗi khi cập nhật:', error);
                        alert('Đã xảy ra lỗi khi cập nhật lịch đặt: ' + error.message);
                        // Quan trọng: Hoàn tác thay đổi trên giao diện nếu server báo lỗi
                        event.revert();
                    });
                }
            } // Kết thúc if (calendarEl)
        });
    </script>
@endpush