<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;
    protected $table = 'bookings';

    protected $booking_id = 'booking_id';
    protected $fillable = ['invoice_detail_id', 'user_id', 'facility_id', 'court_id', 'time_slot_id', 'booking_date', 'unit_price', 'status'];
    /**
     * Lấy thông tin người dùng (user) đã đặt lịch này.
     */
    public function user()
    {
        // Tên Model User của bạn là 'User' hay 'Users'?
        // Giả sử là 'User' (nếu là 'Users' thì đổi thành Users::class)
        // belongsTo(Model, 'khóa ngoại trên bảng bookings', 'khóa chính trên bảng users')
        return $this->belongsTo(Users::class, 'user_id', 'user_id'); 
    }

    /**
     * Lấy thông tin sân (court) được đặt.
     */
    public function court()
    {
        // Giả sử Model Court của bạn là 'Court'
        return $this->belongsTo(Court::class, 'court_id', 'court_id');
    }
    /**
     * Lấy khung giờ (TimeSlot) của lượt đặt này.
     */
    public function timeSlot()
    {
        return $this->belongsTo(Time_slots::class, 'time_slot_id', 'time_slot_id');
    }
}
