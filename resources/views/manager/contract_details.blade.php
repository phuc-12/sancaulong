@extends('layouts.manager')

@section('manager_content')
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0 auto;
            max-width: 1300px;
            background: #fff;
            color: #000;
        }
        h2, h3, p { margin: 4px 0; }
        .header_invoice {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header_invoice h2 { font-size: 18px; text-transform: uppercase; }
        .info, .customer-info { margin-bottom: 10px; }
        .info p, .customer-info p { line-height: 1.5; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            font-size: 13px;
        }
        table th {
            background: #f3f3f3;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 15px;
            margin-top: 15px;
        }
        .footer {
            border-top: 1px solid #ccc;
            padding-top: 8px;
            text-align: left;
            font-size: 13px;
            margin-top: 25px;
        }
        .highlight { color: green; font-weight: bold; }
    </style>
</head>
		<!-- Page Content -->
		<div class="content">
			<div class="">
				<section>
					<div class="row checkout">
						<div class="col-12 col-sm-12 col-md-12 col-lg-12">
							<div class="header_invoice">
								<h2>BẢNG BÁO GIÁ HỢP ĐỒNG</h2>
								<h3>{{ $facilities->facility_name }}</h3>
								<p><strong>Địa chỉ:</strong> {{ $facilities->address }}</p>
								<p><strong>Liên hệ:</strong> {{ $facilities->phone }}</p>
							</div>

							<div class="customer-info">
								<p><strong>Khách hàng:</strong> {{ $customer->fullname ?? '---' }}</p>
								<p><strong>Số điện thoại:</strong> {{ $customer->phone ?? '---' }}</p>
								<p><strong>Thời gian:</strong> 
									Từ {{ \Carbon\Carbon::parse($long_term_contracts->start_date)->format('d/m/Y') }} 
									đến {{ \Carbon\Carbon::parse($long_term_contracts->end_date)->format('d/m/Y') }}
								</p>
								<p>
                                    <strong>Ngày trong tuần:</strong>
                                    {{-- Kiểm tra: Nếu $daysOfWeek là 8, hiển thị 'CN', nếu không, hiển thị 'Thứ ' + giá trị của biến --}}
                                    {{ $daysOfWeek == 8 ? 'CN' : 'Thứ ' . $daysOfWeek }}
                                </p>
                                <p><strong>Sân:</strong> {{ $courts }}</p>
							</div>

							<table>
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Sân</th>
                                        <th>Giờ bắt đầu</th>
                                        <th>Giờ kết thúc</th>
                                        <th>Tổng thời lượng (giờ)</th>
                                        <th>Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($slots as $slot)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($slot['booking_date'])->format('d/m') }}</td>
                                            <td>Sân {{ $slot['court_id'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($slot['start_time'])->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($slot['end_time'])->format('H:i') }}</td>
                                            <td>{{ $slot['total_duration'] }} giờ</td>
                                            <td>{{ number_format($slot['total_price'], 0, ',', '.') }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


						    <p class="total">Tổng cộng: <span class="highlight">{{ number_format($long_term_contracts->total_amount, 0, ',', '.') }} đ</span></p>
							@if (!empty($long_term_contracts) && !empty($long_term_contracts->description) && !empty($long_term_contracts->value))
                                <div class="text-right mt-3">
                                    <h5 class="text-lg font-semibold">Khuyến mãi: {{ $long_term_contracts->description }}</h5>
                                </div>
                                <div class="text-right mt-3">
                                    <h5 class="text-lg font-semibold">Giảm: {{ $long_term_contracts->value*100 . '%' }}</h5>
                                </div>
                            @endif
							<p class="total">Số tiền cần thanh toán: <span class="highlight" style="font-size: 30px">{{ number_format($long_term_contracts->final_amount, 0, ',', '.') }} đ</span></p>
							
							<div>
								<form action="{{ route('manager.cancel_contract') }}" method="POST">
									@csrf
									<input type="hidden" name="invoice_detail_id" value="{{ $long_term_contracts->invoice_detail_id }}">
									<input type="hidden" name="user_id" value="{{ $customer->user_id }}">
									<input type="hidden" name="userMana" value="{{ $userMana }}">
                                    <input type="hidden" name="facility_id" value="{{ $facilities->facility_id }}">
									<div class="d-flex justify-content-center gap-2">
										<button type="submit" class="btn btn-danger btn-sm w-100 course_item_btn" style="width: 100%; height: 60px;">
											HỦY HỢP ĐỒNG
										</button>
									</div>

									<p style="text-align: center">
										Khi hủy, vui lòng liên hệ chủ sân qua số điện thoại hoặc email để được hoàn tiền.
									</p>
								</form>
							</div>
							
						</div>
					</div>
				</section>
			</div>
			<!-- Container -->
		</div>
		<!-- /Page Content -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelector('form[action="{{ route('manager.cancel_contract') }}"]').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Xác nhận hủy hợp đồng?',
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
</script>

@endsection