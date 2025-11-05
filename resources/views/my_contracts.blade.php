@extends('layouts.main')

@section('my_contracts_content')

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
                <!-- Sort By -->
				{{-- <div class="row">
					<div class="col-lg-12">
						<div class="sortby-section court-sortby-section">
							<div class="sorting-info">
								<div class="row d-flex align-items-center">
									<div class="col-xl-7 col-lg-7 col-sm-12 col-12">
										<div class="coach-court-list">
											<ul class="nav">
												<li>
                                                    <form method="POST" action="{{ route('lich_dat_san') }}">
                                                    @csrf
                                                        <input type="hidden" name="user_id" value="{{ $user_id }}">
                                                        <button type="submit" class="active">Hóa đơn đặt</button>
                                                    </form>
                                                </li>
												<li>
                                                    <form method="POST" action="{{ route('lich_co_dinh') }}">
                                                    @csrf
                                                        <input type="hidden" name="user_id" value="{{ $user_id }}">
                                                        <button type="submit">Hợp đồng dài hạn</button>
                                                    </form>
                                                </li>
											</ul>
										</div>
									</div>
									<div class="col-xl-5 col-lg-5 col-sm-12 col-12">
										<div class="sortby-filter-group court-sortby">
											<div class="sortbyset week-bg">
												<div class="sorting-select">
													<select class="form-control select">
														<option>This Week</option>
														<option>One Day</option>
													</select>
												</div>
											</div>
											<div class="sortbyset">
												<span class="sortbytitle">Sort By</span>
												<div class="sorting-select">
													<select class="form-control select">
														<option>Relevance</option>
														<option>Price</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div> --}}
				<!-- Sort By -->

				<div class="row">
					<div class="col-sm-12">
						<div class="court-tab-content">
							<div class="card card-tableset">
								<div class="card-body">
									<div class="coache-head-blk">
										<div class="row align-items-center">
											<div class="col-md-5">
												<div class="court-table-head">
													<h4>Giao dịch thuê cố định của bạn</h4>
													<p>Theo dõi và quản lý các sân đã hoàn thành của bạn</p>
												</div>
											</div>
											<div class="col-md-7">
											</div>
										</div>
									</div>
									<div>
										
										<div>
											<table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Tên sân</th>
                                                        <th>Khách hàng</th>
                                                        <th>Ngày đặt</th>
                                                        <th>Tổng tiền</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $index=0 @endphp
                                                    @foreach ($long_term_contracts as $ct)
                                                        <tr>
                                                            <td>{{ $index+=1 }}</td>
                                                            <td>{{ $ct->facility_name }}</td>
                                                            <td>{{ $ct->fullname }}</td>
                                                            <td>{{ $ct->issue_date }}</td>
                                                            <td>{{ $ct->final_amount }}</td>
                                                            <td>
                                                                <form action="{{ route('chi_tiet_ct') }}" method="POST">
																@csrf
                                                                    <input type="hidden" name="invoice_detail_id" value="{{ $ct->invoice_detail_id }}">
																	<input type="hidden" name="slots" value='@json($mycontract_details[$ct->invoice_detail_id] ?? [])'>
                                                                    <button type="submit" class="btn btn-success">Chi tiết</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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