<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasswordResetController;

//Trang chu
Route::get('/', [HomeController::class, 'index'])->name('index');
//Tim kiếm
Route::get('/search', [HomeController::class, 'search'])->name('search.results');

//Dat san
Route::prefix('/')->controller(HomeController::class)
    ->group(function () {
        Route::get('/', 'index')->name('trang_chu');
        Route::get('/listing-grid', 'listing_grid')->name('danh_sach_san');
        Route::get('/load-more-san', 'loadMoreSan')->name('load.more.san');
        Route::post('/venue', 'show')->name('chi_tiet_san');
        Route::post('/thanh-toan', 'payments')->name('thanh.toan');
        // Route::get('/thanh-toan', function () {
        //     return redirect()->route('trang_chu'); // Hoặc tên route trang chủ của bạn
        // });
    
        Route::post('/booking/add-slot', 'addSlot')->name('booking.addSlot');
        Route::post('/booking/remove-slot', 'removeSlot')->name('booking.removeSlot');
        Route::post('/thanh-toan/thanh-toan-complete', 'payments_complete')->name('payments_complete');
        Route::post('/contract_bookings', 'contract_bookings')->name('contract_bookings');
        Route::post('/contracts_preview', 'contracts_preview')->name('contracts.preview');
        Route::match(['get', 'post'], '/payment_contract', 'payment_contract')->name('payment_contract');
        Route::post('/thanh-toan/thanh-toan-contract-complete', 'payments_contract_complete')->name('payments_contract_complete');
        Route::get('/list_Invoices', 'list_Invoices')->name('lich_dat_san');
        Route::get('/list_Contracts', 'list_Contracts')->name('lich_co_dinh');
        Route::post('/invoice_details', 'invoice_details')->name('chi_tiet_hd');
        Route::post('/cancel_invoice', 'cancel_invoice')->name('cancel_invoice');
        Route::post('/contract_details', 'contract_details')->name('chi_tiet_ct');
        Route::post('/cancel_contract', 'cancel_contract')->name('cancel_contract');
    });

Route::post('/export-invoice', [InvoiceController::class, 'exportInvoice_cus'])->name('export_invoice');

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
//Cap nhat thong tin
Route::middleware('auth')->group(function () {
    Route::get('/profile/{id}', [HomeController::class, 'profile'])->name('user.profile');
    Route::get('/my-courts', [HomeController::class, 'myCourts'])->name('user.courts');
});
//Quen mat khau
// Route hiển thị trang quên mật khẩu
Route::get('/forgot-password', function () {
    return view('auth.forgotpassword');
})->name('forgot-password');

Route::post('/password/send-code', [PasswordResetController::class, 'sendCode'])->name('password.send-code');
Route::post('/password/verify-code', [PasswordResetController::class, 'verifyCode'])->name('password.verify-code');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
//=============================================================================================================
//admin
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/revenue-chart-data', [AdminController::class, 'getRevenueData'])->name('revenueChartData');
    // GET: Hiển thị trang danh sách cơ sở
    Route::get('/facilities', [AdminController::class, 'manageFacilities'])->name('facilities.index');

    // === ROUTE DUYỆT/TỪ CHỐI ===
    // Route để DUYỆT (Approve Facility) - dùng {facility}
    Route::post('/facilities/approve/{facility}', [AdminController::class, 'approve'])
        ->name('facility.approve');

    // Route để TỪ CHỐI (Deny Facility) - dùng {facility}
    Route::post('/facilities/deny/{facility}', [AdminController::class, 'reject'])
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
    // Lưu thông tin cơ sở (không gửi duyệt)
    Route::post('/owner/facility/update-info', [OwnerController::class, 'updateInfo'])->name('facility.updateInfo');
    // Gửi duyệt cơ sở
    Route::post('/owner/facility/request-approval', [OwnerController::class, 'requestApproval'])->name('facility.requestApproval');
    Route::get('/facility/{id}/edit', [OwnerController::class, 'edit'])->name('facility.edit');
    Route::post('/facility/{id}/update', [OwnerController::class, 'update'])->name('facility.update');
    Route::post('/facility/{id}/send-activate-request', [OwnerController::class, 'sendActivateRequest'])
        ->name('facility.sendActivateRequest');

    // Trang Quản lý Nhân viên
    Route::get('/staff', [OwnerController::class, 'staff'])->name('staff');
    // POST: Lưu nhân viên mới
    Route::post('/staff', [OwnerController::class, 'storeStaff'])->name('staff.store');
    // PUT/PATCH: Cập nhật thông tin nhân viên
    Route::put('/staff/{staff}', [OwnerController::class, 'updateStaff'])->name('staff.update');
    // DELETE: Xóa nhân viên
    Route::delete('/staff/{staff}', [OwnerController::class, 'destroyStaff'])->name('staff.destroy');

    //Báo cáo thống kê
    // Trang dashboard báo cáo
    Route::get('/report', [ReportController::class, 'index'])->name('report');
    Route::get('/courts', [OwnerController::class, 'getCourts'])->name('getCourts');

    // API trả dữ liệu KPI (dùng AJAX)
    Route::get('/report/kpi-data', [ReportController::class, 'kpiData'])->name('report.kpiData');
    // API biểu đồ doanh thu theo thời gian (Line Chart)
    Route::get('/report/revenue-chart', [ReportController::class, 'revenueChart'])->name('report.revenueChart');
    // API biểu đồ đặt sân theo giờ (Bar Chart)
    Route::get('/report/bookings-by-hour', [ReportController::class, 'bookingsByHour'])->name('report.bookingsByHour');
    // API biểu đồ doanh thu theo sân con (Pie Chart)
    Route::get('/report/revenue-by-court', [ReportController::class, 'revenueByCourt'])->name('report.revenueByCourt');
    // API so sánh hiệu suất các sân
    Route::get('/report/courts-comparison', [ReportController::class, 'courtsComparison'])->name('report.courtsComparison');
    // API top khách hàng
    Route::get('/report/top-customers', [ReportController::class, 'topCustomers'])->name('report.topCustomers');

    // Xuất báo cáo Excel
    Route::get('report/bookings/export', [ReportController::class, 'exportExcel'])->name('report.exportExcel');
    // Xuất báo cáo PDF
    Route::get('/report/export-pdf', [ReportController::class, 'exportPdf'])->name('report.exportPdf');
});

//=============================================================================================================
//Quản lý sân
Route::prefix('manager')->name('manager.')->middleware(['auth'])->group(function () {
    // Trang tổng quan của Manager
    Route::get('/', [ManagerController::class, 'index'])->name('index');

    // Quản lý hợp đồng thuê dài hạn
    Route::get('/contracts', [ManagerController::class, 'contract_manager'])->name('contracts');

    // Quản lý sân bãi
    Route::get('/courts', [ManagerController::class, 'courts'])->name('courts');
    Route::put(
        '/courts/{court:court_id}/status',
        [ManagerController::class, 'updateCourtStatus']
    )->name('courts.updateStatus');

    // GET: Cung cấp dữ liệu Bookings cho Calendar (JSON)
    Route::get('/bookings/data', [ManagerController::class, 'getBookingsData'])
        ->name('bookings.data');
    // PUT/PATCH: Xử lý cập nhật Booking (khi kéo-thả)
    // {booking} là route model binding
    Route::put('/bookings/update/{booking}', [ManagerController::class, 'updateBookingTime'])
        ->name('bookings.updateTime');

    // 1. API Lấy danh sách sân
    Route::get('/api/get-courts', [ManagerController::class, 'getCourts'])->name('api.courts');

    // 2. API Lấy số liệu KPI (Doanh thu, Lượt đặt...)
    Route::get('/api/kpi-data', [ManagerController::class, 'getKpiData'])->name('api.kpi');

    // 3. API Lấy dữ liệu biểu đồ Giờ
    Route::get('/api/bookings-by-hour', [ManagerController::class, 'getBookingsByHour'])->name('api.hourly');

    // 4. API Lấy dữ liệu biểu đồ Sân (Doanh thu)
    Route::get('/api/revenue-by-court', [ManagerController::class, 'getRevenueByCourt'])->name('api.revenue');
    Route::get('/promotions', [ManagerController::class, 'promotions'])
        ->name('promotions');
    Route::post('/promotions/create', [ManagerController::class, 'promotions_create'])
        ->name('promotions.create');
    Route::post('/promotions/update/{id}', [ManagerController::class, 'promotions_update'])
        ->name('promotions.update');
    Route::delete('/promotions/delete/{id}', [ManagerController::class, 'promotions_delete'])
        ->name('promotions.delete');
});

//=============================================================================================================
//Nhân viên sân
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff'])->group(function () {
    // Trang chính: Lịch đặt sân & Check-in
    Route::get('/', [StaffController::class, 'index'])->name('index');

    // 2. Xử lý "Xác nhận đến sân" (Func 2)
    Route::post('/booking/{booking}/confirm', [StaffController::class, 'confirmArrival'])
        ->name('booking.confirm');

    // 3. Trang Thanh Toán (Func 3, 4)
    // GET: Hiển thị trang & kết quả tìm kiếm
    Route::get('/payment', [StaffController::class, 'paymentPage'])
        ->name('payment');
    Route::post('/cancel-invoice', [StaffController::class, 'cancel_invoice'])->name('cancel_invoice');
    // POST: Tìm kiếm booking để thanh toán
    Route::post('/search/booking-today', [StaffController::class, 'searchBooking'])
        ->name('customer.search');

    Route::post('/invoice_details', [StaffController::class, 'invoice_details'])
        ->name('chi_tiet_hd_nv');

    Route::post('/export-invoice', [InvoiceController::class, 'exportInvoice'])->name('export_invoice');
    Route::post('/confirm_payment', [InvoiceController::class, 'confirm_payment'])->name('confirm_payment');

    Route::get('/booking_directly', [StaffController::class, 'booking_directly'])->name('bookDirectly');
    Route::post('/booking_directly/add-slot', [StaffController::class, 'addSlot'])->name('booking.addSlot');
    Route::post('/booking_directly/remove-slot', [StaffController::class, 'removeSlot'])->name('booking.removeSlot');
    Route::post('/booking_directly/add-invoice', [StaffController::class, 'addInvoice'])->name('addInvoice');

    Route::get('/invoice_history', [StaffController::class, 'invoice_history'])->name('invoiceHistory');
    Route::post('/search/history', [StaffController::class, 'searchHistory'])
        ->name('history.search');

    Route::post('/search/invoice', [StaffController::class, 'searchInvoice'])
        ->name('invoice.search');
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

//=============================================================================================================
//ChatBot
Route::match(['get', 'post'], '/botman', [ChatbotController::class, 'handle']);
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.api');

//Tra cứu dữ liệu
Route::get('/chatbot/check-availability', [ChatbotController::class, 'checkAvailability']);
Route::get('/chatbot/booking-info', [ChatbotController::class, 'bookingInfo']);
Route::get('/chatbot/price', [ChatbotController::class, 'price']);

//Đặt sân
Route::middleware(['auth'])->group(function () {
    // Route thanh toán từ chatbot - chuyển đến trang payment hiện có
    Route::get('/chatbot/payment/{booking_id}', [ChatbotController::class, 'showPaymentPage'])
        ->name('chatbot.payment');

});