<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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
    Route::post('/booking-process', 'bookingProcess')->name('booking.process');
    Route::post('/longterm-store', 'longtermStore')->name('longterm.store');
});
