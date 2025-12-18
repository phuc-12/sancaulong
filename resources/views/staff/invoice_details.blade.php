@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4">Thanh Toán Tại Quầy & In Hóa Đơn</h1>

    <div class="row">
        <!-- Page Content -->
		<div class="content" style="padding: 0;">
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
												<i class="feather-calendar me-2"></i>
												{{ $uniqueDates }}
											</li>

											{{-- THỜI GIAN DUY NHẤT --}}
											<li>
												<i class="feather-clock me-2"></i>
												{{ $uniqueTimes }}
											</li>

											{{-- TỔNG THỜI GIAN --}}
											<li>
												<i class="feather-users me-2"></i>Tổng thời gian : {{ $result }}
											</li>
										</ul>
									</div>
								</ul>
								
							</div>
							<div class="card booking-details" style="margin-bottom: 10px;">
								<h3 class="border-bottom">Thông tin khách hàng</h3>
								<ul>
									<div style="float:left; width: 350px;">
										<li><i class="feather-user me-2"></i>Tên: {{ $customer_name ?? $customer->fullname }}</li>
										<li><i class="feather-phone me-2"></i>SĐT: {{ $customer_phone ?? $customer->phone }}</li>
										<li><i class="feather-mail me-2"></i>Email: {{ $customer_email ?? $customer->email }}</li>
									</div>
								</ul>
								
							</div>
							<div class="card booking-details">
								<h3 class="border-bottom">Thông tin nhân viên</h3>
								<ul>
									<div style="float:left; width: 100%;">
										<li><i class="feather-user me-2"></i>Mã: <input type="text" name="user_id_nv" value="{{ auth()->user()->user_id }}" style="border: white"></li>
										<li><i class="feather-phone me-2"></i>Tên: <input type="text" name="fullname_nv" value="{{ auth()->user()->fullname }}" style="border: white"></li>
										<li>
											<div style="width: 100%;">
												<i class="feather-credit-card me-2"></i>Thanh toán:
												<select id="payment_status_select" class="form-select form-select-sm" style="width: 180px; display:inline-block; margin-left:10px;">
													<option value="unpaid" {{ $invoices->payment_status == 'Chưa thanh toán' ? 'selected' : '' }}>Chưa thanh toán</option>
													<option value="paid" {{ $invoices->payment_status == 'Đã thanh toán' ? 'selected' : '' }}>Đã thanh toán</option>
												</select>
												<button id="confirm_payment_status_btn" class="btn btn-success btn-sm ms-2">Xác nhận</button>
												<div id="payment_status_alert" class="mt-2"></div>
											</div>
										</li>
										<li>
											<div style="width: 100%;">
												<i class="feather-dollar-sign me-2"></i>Sử dụng:
												<select id="payment_method_select" class="form-select form-select-sm" style="width: 180px; display:inline-block; margin-left:10px;">
													<option value="1" {{ $invoices->payment_method == '1' ? 'selected' : '' }}>Chưa sử dụng</option>
													<option value="2" {{ $invoices->payment_method == '2' ? 'selected' : '' }}>Đã sử dụng</option>
												</select>
												<input type="hidden" id="invoice_detail_id" value="{{ $invoice_detail_id }}">
												<button id="confirm_payment_method_btn" class="btn btn-success btn-sm ms-2">Xác nhận</button>
												<div id="payment_method_alert" class="mt-2"></div>
											</div>
										</li>
									</div>
								</ul>
							</div>
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-5 p-0">
							<aside class="card payment-modes">
								<h3 class="border-bottom">Xác nhận thông tin thanh toán</h3>

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
											@php $total += $slot['unit_price']; @endphp
											<tr class="text-center">
												<td>{{ $slot['court_id'] }}</td>
												<td>{{ $slot['start_time'] }}</td>
												<td>{{ $slot['end_time'] }}</td>
												<td>{{ $slot['booking_date'] }}</td>
												<td>{{ number_format($slot['unit_price']) }} đ</td>
											</tr>
										@endforeach
									</tbody>
								</table>

								{{-- Thành tiền --}}
                                <div class="mt-3">
                                    <h5>Thành tiền: <span id="totalAmount">{{ number_format($total) }}</span> đ</h5>
                                    <input type="hidden" id="thanhtien" value="{{ $total }}">
                                </div>

                                {{-- Dropdown promotions --}}
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

                                {{-- Tổng hóa đơn --}}
                                <div class="mt-3">
                                    <h5>Tổng hóa đơn: <span id="finalTotal">{{ number_format($total) }}</span> đ</h5>
                                    {{-- <input type="" id="tongtien" name="total_amount" value="{{ $total }}"> --}}
                                </div>

								{{-- <button class="btn btn-success mt-4">Xác nhận thanh toán</button> --}}
							@else
								<p>Không có dữ liệu khung giờ nào!</p>
							@endif

								<form action="{{ route('staff.cancel_invoice') }}" method="POST" class="mb-3">
									@csrf
									<input type="hidden" name="invoice_detail_id" value="{{ $invoice_detail_id }}">
									
                                    <div class="d-flex justify-content-center gap-2">
										<input type="hidden" name="user_id" value="{{ $customer->user_id}}">
										{{-- <input type="hidden" name="invoices" value="{{ $invoices }}"> --}}
                                        <button type="submit" class="btn btn-danger btn-sm w-100 course_item_btn" style="width: 100%; height: 60px;">HỦY LỊCH</button>
                                    </div>
									{{-- <p style="text-align: center">Khi hủy, vui lòng liên hệ chủ sân qua số điện thoại hoặc email để được hoàn tiền.</p> --}}
								</form>
                                <form action="{{ route('staff.export_invoice') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="slots" value="{{ json_encode($slots) }}">
                                    <input type="hidden" name="total" value="{{ $total }}">
									<input type="hidden" name="facility_id" value="{{ $facilities->facility_id }}">
									<input type="hidden" name="user_id_nv" value="{{ auth()->user()->user_id }}">
									<input type="hidden" name="fullname_nv" value="{{ auth()->user()->fullname }}">
									<input type="hidden" name="user_id" value="{{ $customer->user_id }}">
                                    <input type="hidden" name="invoice_detail_id" value="{{ $invoice_detail_id }}">
									<input type="hidden" name="promotion_id" id="selectedPromotion" value="">
                                	<input type="hidden" id="tongtien" name="total_final" value="{{ $total }}"> <!-- Giữ lại để lấy dữ liệu khác nếu cần -->
                                    <button type="submit" class="btn btn-primary btn-sm w-100 course_item_btn" style="width: 100%; height: 60px;">XUẤT HÓA ĐƠN</button>
                                </form>
							</aside>
						</div>
					</div>
				</section>
			</div>
			<!-- Container -->
		</div>
		<!-- /Page Content -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const promotionSelect = document.getElementById('promotion_id');
    const finalTotalEl = document.getElementById('finalTotal');
    const totalInput = document.getElementById('tongtien');

    let originalTotal = parseFloat(document.getElementById('thanhtien').value);

    if (promotionSelect) {
        promotionSelect.addEventListener('change', function() {
            const selectedOption = promotionSelect.options[promotionSelect.selectedIndex];
            const promoValue = parseFloat(selectedOption.dataset.value || 0);
            const promoType = selectedOption.dataset.type;

            let newTotal = originalTotal;

            if (promoValue > 0) {
                // Nếu là giảm % (value < 1)
                if (promoValue < 1) {
                    newTotal = originalTotal - (originalTotal * promoValue);
                } else {
                    // Nếu là giảm tiền cố định
                    newTotal = originalTotal - promoValue;
                }
            }

            if (newTotal < 0) newTotal = 0;

            // Cập nhật hiển thị và input form
            finalTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(newTotal);
            totalInput.value = newTotal;

            // Gửi promotion_id cùng form
            const selectedPromotionInput = document.getElementById('selectedPromotion');
            selectedPromotionInput.value = selectedOption.value;
        });
    }
});

document.querySelector('form[action="{{ route('staff.cancel_invoice') }}"]').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Xác nhận hủy lịch này?',
        text: 'Hành động này không thể hoàn tác! Vui lòng liên hệ sân để hoàn tiền.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Có, hủy ngay!',
        cancelButtonText: 'Không, quay lại'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});

$(document).ready(function() {
    // Xác nhận trạng thái thanh toán
    $('#confirm_payment_status_btn').click(function(e) {
        e.preventDefault();
        let payment_status = $('#payment_status_select').val();
        let invoice_detail_id = $('#invoice_detail_id').val();

        $.ajax({
            url: "{{ route('staff.confirm_payment') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                payment_status: payment_status,
                invoice_detail_id: invoice_detail_id
            },
            success: function(response) {
                $('#payment_status_alert').html(
                    '<div class="alert alert-success">' + response.message + '</div>'
                );
            },
            error: function(xhr) {
                $('#payment_status_alert').html(
                    '<div class="alert alert-danger">Có lỗi xảy ra, vui lòng thử lại!</div>'
                );
            }
        });
    });

    // Xác nhận phương thức sử dụng
    $('#confirm_payment_method_btn').click(function(e) {
        e.preventDefault();
        let payment_method = $('#payment_method_select').val();
        let invoice_detail_id = $('#invoice_detail_id').val();

        $.ajax({
            url: "{{ route('staff.confirm_payment_method') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                payment_method: payment_method,
                invoice_detail_id: invoice_detail_id
            },
            success: function(response) {
                $('#payment_method_alert').html(
                    '<div class="alert alert-success">' + response.message + '</div>'
                );
            },
            error: function(xhr) {
                $('#payment_method_alert').html(
                    '<div class="alert alert-danger">Có lỗi xảy ra, vui lòng thử lại!</div>'
                );
            }
        });
    });
});
</script>
    </div>
@endsection