@extends('layouts.main')

@section('payment_content')
{{-- [FIX LỖI] Xử lý mã hóa đơn: Nếu có (từ Chatbot) thì dùng, nếu không (Web) thì tạo mới --}}
@php
    $current_user_id = auth()->id() ?? 0;
    $fac_id = $facilities->facility_id ?? 0;
    // Sử dụng biến invoice_detail_id nếu controller truyền sang, nếu không thì tạo mới
    $final_invoice_id = isset($invoice_detail_id) ? $invoice_detail_id : ($current_user_id . '_' . $fac_id . '_' . date('Ymd_His') .'_'. rand(1000, 9999));
@endphp

<div class="container mt-4">
    <div class="breadcrumb mb-0">
        <span class="primary-right-round"></span>
        <div class="container" style="margin-top: 40px;">
            <h1 class="text-white">Hoàn Thành Đặt Sân</h1>
            <ul>
                <li><a href="/">Trang Chủ</a></li>
                <li>Thanh Toán</li>
            </ul>
        </div>
    </div>
</div>

<div class="content">
    <div class="container">
        <section>
            <div class="row checkout">
                <div class="col-12 col-sm-12 col-md-12 col-lg-7">
                    <div class="card booking-details" style="margin-bottom: 10px;">
                        <h3 class="border-bottom">Thông tin đặt sân</h3>
                        <ul>
                            <div style="float:left;">
                                <ul>
                                    <li style="color: red; font-weight: 700;">
                                        <img src="{{ asset('img/icons/venue-type.svg') }}" alt="" class="me-2" width="54" style="background-color: green; border-radius: 100px;">
                                        Địa điểm: {{ $facilities->facility_name }}<br>
                                        Địa chỉ: {{ $facilities->address }} <br>
                                        Sân số: {{ $uniqueCourts }}
                                    </li>
                                    <li><i class="feather-calendar me-2"></i> {{ $uniqueDates }}</li>
                                    <li><i class="feather-clock me-2"></i> {{ $uniqueTimes }}</li>
                                    <li><i class="feather-users me-2"></i>Tổng thời gian : {{ $result }}</li>
                                </ul>
                            </div>
                        </ul>
                    </div>

                    <div class="card booking-details">
                        <h3 class="border-bottom">Thông tin khách hàng</h3>
                        <ul>
                            <div style="float:left; width: 350px;">
                                <li><i class="feather-user me-2"></i>Tên: {{ $customer_name ?? ($customer->fullname ?? '') }}</li>
                                <li><i class="feather-phone me-2"></i>SĐT: {{ $customer_phone ?? ($customer->phone ?? '') }}</li>
                                <li><i class="feather-mail me-2"></i>Email: {{ $customer_email ?? ($customer->email ?? '') }}</li>
                            </div>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-12 col-lg-5">
                    <aside class="card payment-modes p-3">
                        <h3 class="border-bottom">Xác nhận thông tin thanh toán</h3>

                        @if (!empty($slots))
                            {{-- FORM BẮT ĐẦU TỪ ĐÂY ĐỂ BAO TRỌN CÁC INPUT --}}
                            <form id="paymentCompleteForm" action="{{ route('payments_complete') }}" method="POST">
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="bg-gray-100 text-center">
                                            <th>Sân</th>
                                            <th>Bắt đầu</th>
                                            <th>Kết thúc</th>
                                            <th>Ngày</th>
                                            <th>Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($slots as $index => $slot)
                                            @php $total += $slot['price']; @endphp
                                            <tr class="text-center">
                                                <td>{{ $slot['court'] }}</td>
                                                <td>{{ $slot['start_time'] }}</td>
                                                <td>{{ $slot['end_time'] }}</td>
                                                <td>{{ $slot['date'] }}</td>
                                                <td>{{ number_format($slot['price']) }} đ</td>
                                                
                                                {{-- [QUAN TRỌNG] Input ẩn gửi dữ liệu chi tiết từng slot về Controller --}}
                                                <input type="hidden" name="slots[{{$index}}][court]" value="{{ $slot['court'] }}">
                                                <input type="hidden" name="slots[{{$index}}][start_time]" value="{{ $slot['start_time'] }}">
                                                <input type="hidden" name="slots[{{$index}}][end_time]" value="{{ $slot['end_time'] }}">
                                                <input type="hidden" name="slots[{{$index}}][date]" value="{{ $slot['date'] }}">
                                                <input type="hidden" name="slots[{{$index}}][price]" value="{{ $slot['price'] }}">
                                                {{-- Fix lỗi Undefined array key time_slot_id --}}
                                                <input type="hidden" name="slots[{{$index}}][time_slot_id]" value="{{ $slot['time_slot_id'] ?? '' }}">
                                                <input type="hidden" name="slots[{{$index}}][court_id]" value="{{ $slot['court_id'] ?? '' }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    <h5>Thành tiền: <span id="totalAmount">{{ number_format($total) }}</span> đ</h5>
                                    <input type="hidden" id="thanhtien" value="{{ $total }}">
                                </div>

                                @if(!empty($promotions) && $promotions->isNotEmpty())
                                    <div class="mt-3">
                                        <label class="fw-bold">Chọn Khuyến Mãi Áp Dụng:</label>
                                        <select name="promotion_id" id="promotion_id" class="form-select">
                                            <option value="" data-value="0" data-type="">-- Không áp dụng --</option>
                                            @foreach($promotions as $promo)
                                                <option value="{{ $promo->promotion_id }}" 
                                                        data-value="{{ $promo->value }}" 
                                                        data-type="{{ $promo->discount_type }}">
                                                    {{ Str::limit($promo->description, 50) }} 
                                                    ({{ $promo->discount_type }} - 
                                                    @if($promo->value < 1) {{ $promo->value * 100 }}% @else {{ number_format($promo->value) }}đ @endif)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <h5>Tổng hóa đơn: <span id="finalTotal">{{ number_format($total) }}</span> đ</h5>
                                </div>

                                {{-- CÁC INPUT ẨN CHUNG --}}
                                <input type="hidden" name="customer_name" value="{{ $customer_name ?? ($customer->fullname ?? '') }}">
                                <input type="hidden" name="customer_phone" value="{{ $customer_phone ?? ($customer->phone ?? '') }}">
                                <input type="hidden" name="customer_email" value="{{ $customer_email ?? ($customer->email ?? '') }}">
                                <input type="hidden" name="user_id" id="user_id" value="{{ auth()->id() }}">
                                <input type="hidden" name="facility_id" id="facility_id" value="{{ $facilities->facility_id }}">
                                
                                {{-- Sử dụng biến đã xử lý ở đầu file --}}
                                <input type="hidden" name="invoice_details_id" id="invoice_details_id" value="{{ $final_invoice_id }}">
                                
                                <input type="hidden" name="promotion_id" id="selectedPromotion" value="">
                                <input type="hidden" id="tongtien" name="total_final" value="{{ $total }}">

                                <div class="form-check d-flex justify-content-start align-items-center policy mt-3">
                                    <label>Sau khi click vào "Chuyển Khoản" sẽ hiển thị mã QR bên dưới. <br> Vui lòng quét mã để thanh toán.</label>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-primary btn-lg" id="showQRBtn"
                                            data-bank="{{ $facilities->account_bank }}"
                                            data-account="{{ $facilities->account_no }}"
                                            data-name="{{ $facilities->account_name }}"
                                            style="width:300px;">
                                            THANH TOÁN
                                    </button>
                                    <div class="mt-4" align="center">
                                        <img id="qrImage" style="width:300px; display:none;">
                                    </div>
                                </div>
                            </form>
                        @else
                            <p>Không có dữ liệu khung giờ nào!</p>
                        @endif
                    </aside>
                </div>
            </div>
        </section>
    </div> 
</div> 

<div id="successModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius: 20px;">
            <div class="text-success mb-3" style="font-size: 60px;">✓</div>
            <h4 class="fw-bold">Thanh Toán Thành Công!</h4>
            <p class="mt-2 mb-3">Cảm ơn bạn đã hoàn tất giao dịch.</p>
            <button class="btn btn-primary px-4" data-bs-dismiss="modal">Đóng</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const promotionSelect = document.getElementById('promotion_id');
    const finalTotalEl = document.getElementById('finalTotal');
    const totalInput = document.getElementById('tongtien');
    const selectedPromotionInput = document.getElementById('selectedPromotion');
    
    // Check if element exists before accessing value
    const thanhtienEl = document.getElementById('thanhtien');
    let originalTotal = thanhtienEl ? parseFloat(thanhtienEl.value) : 0;

    if (promotionSelect) {
        promotionSelect.addEventListener('change', function() {
            const selectedOption = promotionSelect.options[promotionSelect.selectedIndex];
            const promoValue = parseFloat(selectedOption.dataset.value || 0);
            let newTotal = originalTotal;

            if (promoValue > 0) {
                if (promoValue < 1) { // % discount
                    newTotal = originalTotal - (originalTotal * promoValue);
                } else { // Fixed amount
                    newTotal = originalTotal - promoValue;
                }
            }
            if (newTotal < 0) newTotal = 0;

            finalTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(newTotal);
            totalInput.value = newTotal;
            selectedPromotionInput.value = selectedOption.value;
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('showQRBtn');
    const qrImage = document.getElementById('qrImage');
    
    if(btn) {
        btn.addEventListener('click', function() {
            const totalEl = document.getElementById('tongtien');
            const total = totalEl ? parseFloat(totalEl.value) : 0;
            const bank = btn.dataset.bank || 'VCB';
            const account = btn.dataset.account || '9704366899999';
            const name = btn.dataset.name || 'SAN CAU LONG DEMO';

            const qrUrl = `https://img.vietqr.io/image/${bank}-${account}-compact2.png?amount=${total}&addInfo=Thanh toan dat san&accountName=${encodeURIComponent(name)}`;
            qrImage.src = qrUrl;
            qrImage.style.display = 'block';

            // Giả lập check thanh toán thành công sau 5s (Thay vì gọi Google Script để test nhanh)
            setTimeout(() => {
                 const modal = new bootstrap.Modal(document.getElementById('successModal'));
                 modal.show();
                 setTimeout(() => {
                    document.getElementById('paymentCompleteForm').submit();
                 }, 1500);
            }, 5000); 
        });
    }
});
</script>
@endsection