<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    //trang chính của nhân viên
    public function index()
    {
        // Lấy danh sách đặt sân CỦA HÔM NAY
        // $bookingsToday = Booking::whereDate('booking_date', today())->get();
        // return view('staff.index', compact('bookingsToday'));
        return view('staff.index');
    }
    
    //Trang Thanh toán tại quầy & In hóa đơn
    public function payment()
    {
        // return view('staff.payment');
        return view('staff.payment');
    }
}