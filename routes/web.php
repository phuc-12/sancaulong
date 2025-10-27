<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OwnerController;
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
        Route::get('/', 'index')->name('trang_chu');
        Route::get('/listing-grid', 'listing_grid')->name('danh_sach_san');
        Route::get('/api/load-more-san', 'load_more_san')->name('api.load_san');
        // Route::get('/venue-details','venue_details')->name('chi_tiet_san');
        Route::get('/venue/{idSan}', 'show')->name('chi_tiet_san');
        Route::post('/booking-process', 'processBooking')->name('booking.process');
        Route::get('/payments', 'payments')->name('payment');
        // web.php
        Route::post('/booking/add-slot', 'addSlot')->name('booking.addSlot');
        Route::post('/booking/remove-slot', 'removeSlot')->name('booking.removeSlot');
    });

//tạo user
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
Route::get('/admin', [AdminController::class, 'index'])
    ->name('admin.index');
// Route này sẽ cung cấp dữ liệu JSON cho biểu đồ
Route::get('/admin/revenue-chart-data', [AdminController::class, 'getRevenueData'])
    ->name('admin.revenueChartData');
// Route để DUYỆT (Approve)
Route::post('/owners/approve/{id}', [AdminController::class, 'approveOwner'])
    ->name('owner.approve');
// Route để TỪ CHỐI (Deny)
Route::post('/owners/deny/{id}', [AdminController::class, 'denyOwner'])
    ->name('owner.deny');
    
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
    // Route::post('/staff', [OwnerController::class, 'storeStaff'])->name('staff.store');
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