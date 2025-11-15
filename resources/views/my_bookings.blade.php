@extends('layouts.main')

@section('my_bookings_content')

		<!-- Breadcrumb -->
		<section class="breadcrumb breadcrumb-list mb-0">
			<span class="primary-right-round"></span>
			<div class="container">
				<h1 class="text-white">Lịch Sử Giao Dịch</h1>
				<ul>
					<li><a href="index.html">Trang Chủ</a></li>
					<li >Lịch Sử Giao Dịch</li>
				</ul>
			</div>
		</section>

		<div class="content court-bg">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="court-tab-content">
							<div class="card card-tableset">
								<div class="card-body">
									<div class="coache-head-blk">
										<div class="row align-items-center">
											<div class="col-md-5">
												<div class="court-table-head">
													<h4>Giao Dịch Của Bạn</h4>
													<p>Theo dõi và quản lý các sân đã hoàn thành của bạn</p>
												</div>
											</div>
											<div class="col-md-7">
											</div>
										</div>
									</div>
									<div>
										<div>
											@if($success_message)
												<div class="alert alert-danger">
													<p>{{ $success_message }}</p>
												</div>
											@else 
											@endif
											<table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Tên sân</th>
                                                        <th>Khách hàng</th>
                                                        <th>Ngày đặt</th>
                                                        <th>Tổng tiền</th>
														<th>Ngày áp dụng</th>
														<th>Sử dụng</th>
														<th>Tình trạng</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $index=0 @endphp
                                                    @forelse ($invoices as $invoice)
                                                        <tr>
                                                            <td>{{ $index+=1 }}</td>
                                                            <td>{{ $invoice->facility_name }}</td>
                                                            <td>{{ $invoice->fullname }}</td>
                                                            <td>{{ $invoice->issue_date }}</td>
                                                            <td>{{ $invoice->final_amount }}</td>
															<td>
																@php
																	$firstBooking = $mybooking_details[$invoice->invoice_detail_id]->first() ?? null;
																	$bookingDate = $firstBooking->booking_date ?? null;
																@endphp
																{{ $bookingDate }}
															</td>
															<td>
																@php
																	$firstBooking = $mybooking_details[$invoice->invoice_detail_id]->first() ?? null;
																	$bookingDate = $firstBooking->booking_date ?? null;
																	$isExpired = $bookingDate ? \Carbon\Carbon::parse($bookingDate)->lt(\Carbon\Carbon::today()) : false;
																@endphp
																@if ($isExpired)
																	<p class="text-warning pt-3">Đã quá hạn</p>
																@else
																	<p class="text-primary pt-3">Chưa sử dụng</p>
																@endif
															</td>
                                                            <td>
                                                                <form action="{{ route('chi_tiet_hd') }}" method="POST">
																@csrf
																	<input type="hidden" name="facility_id" value="{{ $invoice->facility_id }}">
																	<input type="hidden" name="user_id" value="{{ $invoice->customer_id }}">
																	<input type="hidden" name="slots" value='@json($mybooking_details[$invoice->invoice_detail_id] ?? [])'>
                                                                    <input type="hidden" name="invoice_detail_id" value="{{ $invoice->invoice_detail_id }}">
																	<input type="hidden" name="invoices" value="{{ $invoices }}">
																	@if ($invoice->payment_status === 'Đã Hủy')
																		<p class="text-danger">Đã hủy</p>
																	@elseif ($invoice->payment_status === 'Đã sử dụng')
																		<p class="text-primary">Đã sử dụng</p>
																	@else 
																		<button type="submit" class="btn btn-success">
																			Chi tiết
																		</button>
																	@endif
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
														<tr>
															<td colspan="6" class="text-center text-muted">
																Chưa có lịch đặt nào
															</td>
														</tr>
													@endforelse
                                                </tbody>
                                            </table>
										</div>
										
									</div>
									
								</div>
							</div> 
						</div>
					</div>
				</div>

			</div>
		</div>
		<!-- /Page Content -->

@endsection