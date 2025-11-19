@extends('layouts.main')

@section('payment_content')
<div class="container mt-4">
    <div class="breadcrumb mb-0">
    <span class="primary-right-round"></span>
    <div class="container" style="margin-top: 40px;">
        <h1 class="text-white">Hoàn Thành Đặt Sân</h1>
        <ul>
            <li><a href="index.html">Trang Chủ</a></li>
            <li>Thanh Toán</li>
        </ul>
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
                                    {{-- SÂN SỐ DUY NHẤT --}}
                                    <li style="color: red; font-weight: 700;">
                                        <img src="{{ asset('img/icons/venue-type.svg') }}" alt="" class="me-2" width="54" style="background-color: green; border-radius: 100px;">
                                        Địa điểm: {{ $facilities->facility_name }}<br>
                                        Địa chỉ: {{ $facilities->address }} <br>
                                        Sân số: {{ $uniqueCourts }}
                                    </li>

                                    {{-- NGÀY ĐẶT DUY NHẤT --}}
                                    <li>
                                        <i class="feather-calendar me-2"></i> {{ $uniqueDates }}
                                    </li>

                                    {{-- THỜI GIAN DUY NHẤT --}}
                                    <li>
                                        <i class="feather-clock me-2"></i> {{ $uniqueTimes }}
                                    </li>

                                    {{-- TỔNG THỜI GIAN --}}
                                    <li>
                                        <i class="feather-users me-2"></i>Tổng thời gian : {{ $result }}
                                    </li>
                                </ul>
                            </div>
                        </ul>
                    </div>

                    <div class="card booking-details">
                        <h3 class="'border-bottom">Thông tin khách hàng</h3>
                        <ul>
                            <div style="float:left; width: 350px;">
                                <li><i class="feather-user me-2"></i>Tên: {{ $customer_name ?? $customer->fullname }}</li>
                                <li><i class="feather-phone me-2"></i>SĐT: {{ $customer_phone ?? $customer->phone }}</li>
                                <li><i class="feather-mail me-2"></i>Email: {{ $customer_email ?? $customer->email }}</li>
                            </div>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-12 col-lg-5">
                    <aside class="card payment-modes">
                        <h3 class="border-bottom">Xác nhận thông tin thanh toán</h3>

                        @php
                            $invoice_detail_id = $customer->user_id . '_' . $facilities->facility_id . '_' . date('Ymd_His') .'_'. rand(1000, 9999);
                        @endphp

                        @if (!empty($slots))
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
                                    @foreach ($slots as $slot)
                                        @php $total += $slot['price']; @endphp
                                        <tr class="text-center">
                                            <td>{{ $slot['court'] }}</td>
                                            <td>{{ $slot['start_time'] }}</td>
                                            <td>{{ $slot['end_time'] }}</td>
                                            <td>{{ $slot['date'] }}</td>
                                            <td>{{ number_format($slot['price']) }} đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="text-right mt-3">
                                <h3 class="text-lg font-semibold">Tổng tiền: {{ number_format($total) }} đ</h3>
                                <input type="hidden" id="tongtien" value="{{ $total }}">
                            </div>

                            {{-- <button class="btn btn-success mt-4">Xác nhận thanh toán</button> --}}
                        @else
                            <p>Không có dữ liệu khung giờ nào!</p>
                        @endif

                        <div class="form-check d-flex justify-content-start align-items-center policy">
                            <label>Sau khi click vào "Chuyển Khoản" sẽ hiển thị mã QR bên dưới. <br> Vui lòng quét mã để thanh toán.</label>
                        </div>

                        <div class="text-center mt-4">
							

							<form id="paymentCompleteForm" action="{{ route('payments_complete') }}" method="POST" align="center">
								@csrf
								<input type="hidden" name="customer_name" value="{{ $customer_name ?? $customer->fullname }}">
								<input type="hidden" name="customer_phone" value="{{ $customer_phone ?? $customer->phone }}">
								<input type="hidden" name="customer_email" value="{{ $customer_email ?? $customer->email }}">
								<input type="hidden" name="slots" id="slots_input" value='@json($slots)'>
								<input type="hidden" name="invoice_details_id" id="invoice_details_id" value="{{ $invoice_detail_id }}">
								<input type="hidden" name="facility_id" id="facility_id" value="{{ $facilities->facility_id }}">
								<input type="hidden" name="user_id" id="user_id" value="{{ auth()->id() }}">
								<button 
									type="button" 
									class="btn btn-primary btn-lg" 
									id="showQRBtn"
									data-bank="{{ $facilities->account_bank }}"
									data-account="{{ $facilities->account_no }}"
									data-name="{{ $facilities->account_name }}"
                                    style="width:300px;"
									>
										THANH TOÁN
									</button>

									<div class="mt-4" align="center">
										<img id="qrImage" style="width:300px; display:none;">
									</div>
								</div>
							</form>
						</div>

                        
                    </aside>
                </div>
            </div>
        </section>
    </div> 
</div> 
<!-- Success Modal -->
<div id="successModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius: 20px;">
            <div class="text-success mb-3" style="font-size: 60px;">
                ✓
            </div>
            <h4 class="fw-bold">Thanh Toán Thành Công!</h4>
            <p class="mt-2 mb-3">Cảm ơn bạn đã hoàn tất giao dịch.</p>
            <p class="mt-2 mb-3">Sẽ tự chuyển về trang chi tiết sau vài giây.</p>
            <button class="btn btn-primary px-4" data-bs-dismiss="modal">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('showQRBtn');
    const qrImage = document.getElementById('qrImage');
    const total = {{ $total }};
    let intervalId = null, isPaid = false;

    btn.addEventListener('click', function() {
        const bank = btn.dataset.bank || 'VCB';
        const account = btn.dataset.account || '9704366899999';
        const name = btn.dataset.name || 'SAN CAU LONG DEMO';

        const qrUrl = `https://img.vietqr.io/image/${bank}-${account}-compact2.png?amount=${total}&addInfo=Thanh toan dat san&accountName=${encodeURIComponent(name)}`;
        qrImage.src = qrUrl;
        qrImage.style.display = 'block';

        if (intervalId) clearInterval(intervalId);
        intervalId = setInterval(checkPayment, 8000);
    });

    async function checkPayment() {
        if (isPaid) return;

        try {
            const res = await fetch("https://script.google.com/macros/s/AKfycbwIKNqvZftMggqULAy8J-rPGwEsw1HVvJbJK5jfKkNJJ-EMf6km5_xJibYyLs04wM0xFQ/exec");
            const data = await res.json();
            const last = data.data[data.data.length - 1];
            const value = parseInt(last["Giá trị"]);

            if (value >= total) {
                clearInterval(intervalId);
                isPaid = true;

                // Hiển thị modal thành công
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();

                // Tự submit sau 1.5s
                setTimeout(() => {
                    document.getElementById('paymentCompleteForm').submit();
                }, 1500);
            }
        } catch (err) {
            console.error(err);
        }
    }
});
</script>

@endsection
