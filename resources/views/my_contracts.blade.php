@extends('layouts.main')

@section('my_contracts_content')
<section class="breadcrumb breadcrumb-list mb-0">
    <span class="primary-right-round"></span>
    <div class="container">
        <h1 class="text-white">Lịch Sử Giao Dịch</h1>
        <ul>
            <li><a href="/">Trang Chủ</a></li>
            <li>Lịch Sử Giao Dịch</li>
        </ul>
    </div>
</section>

<div class="content court-bg py-4">
    <div class="container">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-success text-white py-3">
                <h4 class="mb-0">Giao dịch thuê cố định của bạn</h4>
                <p class="mb-0 text-light">Theo dõi và quản lý các sân đã hoàn thành của bạn</p>
            </div>

            <div class="card-body p-4">
                @if($success_message)
                    <div class="alert alert-danger">
                        {{ $success_message }}
                    </div>
                @endif

                <div class="table-responsive rounded-3 mt-3">
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
											<form action="{{ route('chi_tiet_ct') }}" method="POST">
												@csrf
												<input type="hidden" name="invoice_detail_id" value="{{ $ct->invoice_detail_id }}">
												<input type="hidden" name="slots" value='@json($details ?? [])'>
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

            </div>
        </div>
    </div>
</div>
@endsection