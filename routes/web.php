<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;



//Trang chu
Route::get('/', function () {
    return view('index');
});

//Dat san
Route::prefix('/')->controller(HomeController::class)

->group(function () {
    Route::get('/','index')->name('trang_chu');
    Route::get('/listing-grid','listing_grid')->name('danh_sach_san');
    Route::get('/api/load-more-san','load_more_san')->name('api.load_san');
    // Route::get('/venue-details','venue_details')->name('chi_tiet_san');
    Route::post('/venue', 'show')->name('chi_tiet_san');
    // Route::post('/booking-process', 'processBooking')->name('booking.process');
    Route::post('/thanh-toan', 'payments')->name('thanh.toan');
    Route::post('/booking/add-slot', 'addSlot')->name('booking.addSlot');
    Route::post('/booking/remove-slot', 'removeSlot')->name('booking.removeSlot');
    Route::post('/thanh-toan/thanh-toan-complete','payments_complete')->name('payments_complete');
    Route::post('/contract_bookings','contract_bookings')->name('contract_bookings');
    Route::post('/contracts_preview', 'contracts_preview')->name('contracts.preview');
    Route::match(['get', 'post'],'/payment_contract', 'payment_contract')->name('payment_contract');
    Route::post('/thanh-toan/thanh-toan-contract-complete','payments_contract_complete')->name('payments_contract_complete');
});

Route::prefix('users')->controller(UserController::class)
    ->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('/create', 'create')->name('create');

        Route::post('/store', 'store')->name('store');

    });

//Dang ky
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');
//Dang nhap
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');
//Dang xuat
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile/{id}', [HomeController::class, 'profile'])->name('user.profile');
    Route::get('/my-courts', [HomeController::class, 'myCourts'])->name('user.courts');
});
//=============================================================================================================
//admin
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/revenue-chart-data', [AdminController::class, 'getRevenueData'])->name('revenueChartData');
    // GET: Hiển thị trang danh sách cơ sở
    Route::get('/facilities', [AdminController::class, 'manageFacilities'])->name('facilities.index');

    // === ROUTE DUYỆT/TỪ CHỐI ===
    // Route để DUYỆT (Approve Facility) - dùng {facility}
    Route::post('/facilities/approve/{facility}', [AdminController::class, 'approveFacility'])
        ->name('facility.approve');

    // Route để TỪ CHỐI (Deny Facility) - dùng {facility}
    Route::post('/facilities/deny/{facility}', [AdminController::class, 'denyFacility'])
        ->name('facility.deny');

    // POST: Tạm khóa cơ sở
    Route::post('/facilities/suspend/{facility}', [AdminController::class, 'suspendFacility'])
        ->name('facility.suspend');

    // POST: Kích hoạt lại cơ sở
    Route::post('/facilities/activate/{facility}', [AdminController::class, 'activateFacility'])
        ->name('facility.activate');

    // === ROUTE KHÁCH HÀNG ===
    Route::get('/customers', [AdminController::class, 'listCustomers'])
        ->name('customers.index');

    // GET: Hiển thị form để sửa thông tin một khách hàng
    // {user} sẽ tự động tìm Model Users (vì bạn đã khai báo $primaryKey = 'user_id')
    Route::get('/customers/{user}/edit', [AdminController::class, 'editCustomer'])
        ->name('customers.edit');

    // PUT/PATCH: Xử lý cập nhật thông tin khách hàng
    Route::put('/customers/{user}', [AdminController::class, 'updateCustomer'])
        ->name('customers.update');
});
//=============================================================================================================
//Chủ sân (owner)
Route::prefix('owner')->name('owner.')->middleware(['auth'])->group(function () {
    // Trang Tổng Quan
    Route::get('/', [OwnerController::class, 'index'])->name('index');
    // Trang Quản lý Cơ sở
    Route::get('/facility', [OwnerController::class, 'facility'])->name('facility');
    Route::post('/facility', [OwnerController::class, 'storeFacility'])->name('facility.store');
    // Trang Quản lý Nhân viên
    Route::get('/staff', [OwnerController::class, 'staff'])->name('staff');

    // POST: Lưu nhân viên mới
    Route::post('/staff', [OwnerController::class, 'storeStaff'])->name('staff.store');

    // GET: Lấy dữ liệu nhân viên để sửa (cho AJAX hoặc form riêng)
    // Route::get('/staff/{staff}/edit', [OwnerController::class, 'editStaff'])->name('staff.edit'); 

    // PUT/PATCH: Cập nhật thông tin nhân viên
    // {staff} sẽ là User model nhờ Route Model Binding (cần khai báo binding nếu tên model khác User)
    Route::put('/staff/{staff}', [OwnerController::class, 'updateStaff'])->name('staff.update');

    // DELETE: Xóa nhân viên
    Route::delete('/staff/{staff}', [OwnerController::class, 'destroyStaff'])->name('staff.destroy');
});

//=============================================================================================================
//Quản lý sân
Route::prefix('manager')->name('manager.')->middleware(['auth'])->group(function () {
    // Trang tổng quan của Manager
    Route::get('/', [ManagerController::class, 'index'])->name('index');

    // Quản lý hợp đồng thuê dài hạn
    Route::get('/contracts', [ManagerController::class, 'contracts'])->name('contracts');

    // Quản lý sân bãi
    Route::get('/courts', [ManagerController::class, 'courts'])->name('courts');
    Route::put('/courts/{court}/status', [ManagerController::class, 'updateCourtStatus'])
        ->name('courts.updateStatus');

    // GET: Cung cấp dữ liệu Bookings cho Calendar (JSON)
    Route::get('/bookings/data', [ManagerController::class, 'getBookingsData'])
        ->name('bookings.data');
    // PUT/PATCH: Xử lý cập nhật Booking (khi kéo-thả)
    // {booking} là route model binding
    Route::put('/bookings/update/{booking}', [ManagerController::class, 'updateBookingTime'])
        ->name('bookings.updateTime');
});

//=============================================================================================================
//Nhân viên sân
Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
    // Trang chính: Lịch đặt sân & Check-in
    Route::get('/', [StaffController::class, 'index'])->name('index');

    // Trang thanh toán & In hóa đơn
    Route::get('/payment', [StaffController::class, 'payment'])->name('payment');
});

//=============================================================================================================
//Khách hàng
// === HỒ SƠ KHÁCH HÀNG ===
Route::middleware(['auth'])->group(function () {
    // GET: Hiển thị form chỉnh sửa hồ sơ
    Route::get('/profile/{id}', [ProfileController::class, 'edit'])->name('user.profile');
    // PUT/PATCH: Xử lý cập nhật thông tin hồ sơ (gửi từ form)
    Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
});