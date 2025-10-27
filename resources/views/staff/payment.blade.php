@extends('layouts.staff')

@section('staff_content')
    <h1 class="h3 mb-4">Thanh Toán Tại Quầy & In Hóa Đơn</h1>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Thông tin Hóa đơn</h5></div>
                <div class="card-body">
                    <form action="#" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tìm kiếm Khách hàng / SĐT / Mã đặt</label>
                            <input type="text" class="form-control" placeholder="Nhập SĐT khách hàng...">
                        </div>
                        <hr>
                        <h5>Thông tin thanh toán:</h5>
                        <p><strong>Khách hàng:</strong> Nguyễn Văn A</p>
                        <p><strong>Sân:</strong> Sân 2 (18:00 - 19:00)</p>
                        <p><strong>Tổng tiền:</strong> <span class="fs-4 text-danger">100.000đ</span></p>

                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select class="form-select">
                                <option value="cash">Tiền mặt (Tại quầy)</option>
                                <option value="transfer">Chuyển khoản (Tại quầy)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg">Xác nhận Thanh toán</button>
                        <button type="button" class="btn btn-outline-secondary btn-lg ms-2">In Hóa đơn</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                 <div class="card-header"><h5 class="mb-0">Tìm kiếm Hóa đơn cũ</h5></div>
                <div class="card-body">
                    <p>Nhập mã hóa đơn để in lại:</p>
                     <input type="text" class="form-control" placeholder="Mã HĐ: 1256...">
                     <button class="btn btn-primary mt-2">Tìm & In lại</button>
                </div>
            </div>
        </div>
    </div>
@endsection