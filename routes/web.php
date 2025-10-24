<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('index');
});

Route::prefix('/')->controller(HomeController::class)
->group(function () {
    Route::get('/','index')->name('trang_chu');
    Route::get('/listing-grid','listing_grid')->name('danh_sach_san');
    Route::get('/api/load-more-san','load_more_san')->name('api.load_san');
    // Route::get('/venue-details','venue_details')->name('chi_tiet_san');
    Route::get('/venue/{idSan}', 'show')->name('chi_tiet_san');
    Route::post('/booking-process', 'processBooking')->name('booking.process');
    Route::get('/payments','payments')->name('payment');
    // web.php
    Route::post('/booking/add-slot', 'addSlot')->name('booking.addSlot');
    Route::post('/booking/remove-slot', 'removeSlot')->name('booking.removeSlot');
});
//
Route::prefix('users')->controller(UserController::class)
    ->name('users.')->group(function(){
        Route::get('/', 'index')->name('index');

        Route::get('/create', 'create')->name('create');

        Route::post('/store', 'store')->name('store');

    });

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile/{id}', [HomeController::class, 'profile'])->name('user.profile');
    Route::get('/my-courts', [HomeController::class, 'myCourts'])->name('user.courts');
});
