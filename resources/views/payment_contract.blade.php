@extends('layouts.main');

@section('payment_contract_content')
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
<!-- Breadcrumb -->
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

		<!-- Page Content -->
		<div class="content">
			<div class="">
				<section>
					<div class="row checkout">
						<div class="col-12 col-sm-12 col-md-12 col-lg-12">
							<div class="header_invoice">
								<h2>BẢNG BÁO GIÁ HỢP ĐỒNG</h2>
								<h3>{{ $userInfo['facility_name'] }}</h3>
								<p><strong>Địa chỉ:</strong> {{ $userInfo['facility_address'] }}</p>
								<p><strong>Liên hệ:</strong> {{ $userInfo['facility_phone'] }}</p>
							</div>

							<div class="customer-info">
								<p><strong>Khách hàng:</strong> {{ $userInfo['user_name'] ?? '---' }}</p>
								<p><strong>Số điện thoại:</strong> {{ $userInfo['phone'] ?? '---' }}</p>
								<p><strong>Thời gian:</strong> 
									Từ {{ \Carbon\Carbon::parse($summary['start_date'])->format('d/m/Y') }} 
									đến {{ \Carbon\Carbon::parse($summary['end_date'])->format('d/m/Y') }}
								</p>
								@php 
									$invoice_detail_id = 'HD_' . $userInfo['user_id'] . '_' . $userInfo['facility_id'] . '_' . date('Ymd_His') .'_'. rand(1000, 9999);
								@endphp
								<p><strong>Ngày trong tuần:</strong>
									@foreach ($summary['selected_days'] as $d)
										{{-- Kiểm tra nếu $d bằng 8 thì hiển thị 'CN', ngược lại hiển thị 'Thứ ' . $d --}}
										{{ $d == 8 ? 'Chủ Nhật' : 'Thứ ' . $d }}@if (!$loop->last), @endif
									@endforeach
								</p>
								
							</div>

							<table>
							<thead>
								<tr>
									<th>Ngày</th>
									<th>Sân</th>
									<th>Tổng thời lượng (giờ)</th>
									<th>Tổng tiền</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($lines as $line)
								<tr>
									<td>{{ $line['date'] }}</td>
									<td>Sân {{ $line['court'] }}</td>
									<td>{{ $line['duration'] }} giờ</td>
									<td>{{ number_format($line['amount'],0,',','.') }}đ</td>
								</tr>
								@endforeach
							</tbody>
						</table>

						<p class="total">Tổng cộng: <span class="highlight">{{ number_format($summary['total_amount'], 0, ',', '.') }} đ</span></p>
							{{-- <p class="total">Tổng cộng: <span class="highlight">{{ $summary['total_amount'] }} đ</span></p> --}}
							<p class="total">Số tiền cần thanh toán: <span class="highlight">{{ number_format($summary['total_amount'], 0, ',', '.') }} đ</span></p>
							
							<div>
								<p><strong>Chủ tài khoản:</strong> {{ $userInfo['account_name'] }}</p>
								<p><strong>Số tài khoản:</strong> {{ $userInfo['account_no'] }}</p>

								<form id="paymentCompleteForm" action="{{ route('payments_contract_complete') }}" method="POST">
									@csrf
									<input type="hidden" name="start_date" id="start_date" value="{{ $summary['start_date'] }} ">
									<input type="hidden" name="end_date" id="end_date" value="{{ $summary['end_date'] }}">
									<input type="hidden" name="tongtien" id="tongtien" value="{{ $summary['total_amount'] }}">
									<input type="hidden" name="details" id="details_input" value='@json($details["actual_dates"])'>
									<input type="hidden" name="invoice_details_id" id="invoice_details_id" value="{{ $invoice_detail_id }}">
									<input type="hidden" name="facility_id" id="facility_id" value="{{ $userInfo["facility_id"] }}">
									<input type="hidden" name="user_id" id="user_id" value="{{ $userInfo["user_id"] }}">
									<input type="hidden" name="slot_details" value='@json($details["slot_details"])'>
									<button 
										type="button" 
										class="btn btn-primary btn-lg" 
										id="showQRBtn"
										data-bank="{{ $userInfo['account_bank'] }}"
										data-account="{{ $userInfo['account_name'] }}"
										data-name="{{ $userInfo['account_name'] }}"
										>
											Hiển thị mã QR
										</button>

										<div class="mt-4" align="center">
											<img id="qrImage" style="width:300px; display:none;">
										</div>
									</div>	
								</form>

							</div>
							
						</div>
					</div>
					{{-- <table> 
						<thead> 
							<tr> 
								<th>T.Gian</th> 
								<th>Dịch vụ</th> 
								<th>SL</th> 
								<th>Đ.Giá</th> 
							</tr> 
						</thead> 
						<tbody> 
							@php $total = 0; @endphp 
							@foreach ($details['actual_dates'] as $item) 
								@php $date = \Carbon\Carbon::parse($item['date'])->format('d/m'); @endphp 
								@foreach ($details['courts'] as $court) 
									@foreach ($details['slot_details'] as $slot) 
										@php $amount = $slot['amount']; $total += $amount; @endphp 
											<tr> 
												<td>{{ $date }}</td> 
												<td>Sân {{ $court }}</td> 
												<td>30 phút</td> 
												<td>{{ number_format($amount, 0, ',', '.') }}đ</td> 
											</tr> 
										@endforeach 
									@endforeach 
								@endforeach 
						</tbody> 
					</table> --}}
				</section>
			</div>
			<!-- Container -->
		</div>
		<!-- /Page Content -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('showQRBtn');
    const qrImage = document.getElementById('qrImage');
    const total = {{ $summary['total_amount'] }};
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
                alert("Thanh toán thành công! "+value);
                document.getElementById('paymentCompleteForm').submit();
            }
        } catch (err) {
            console.error(err);
        }
    }
});
</script>
@endsection