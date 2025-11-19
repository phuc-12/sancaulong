<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Court_prices;

class Facilities extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $primaryKey = 'facility_id';
    public $timestamps = true;
    protected $fillable = [
        'owner_id',
        'facility_name',
        'address',
        'phone',
        'open_time',
        'close_time',
        'description',
        'status',
        'quantity_court',
        //IMG
        'image',
        'business_license',
        //Giá
        'default_price',
        'special_price',
        //STK - Ngân hàng
        'account_no',
        'account_bank',
        'account_name',
        // trạng thái hoạt động
        'is_active',
        'need_reapprove',
        'pending_request_type',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'need_reapprove' => 'boolean',
    ];
    
    public function courtPrice()
    {
        return $this->hasOne(Court_prices::class, 'facility_id', 'facility_id');
    }

    public function Users()
    {
        return $this->belongsTo(Users::class, 'owner_id');
    }

    public function owner()
    {
        // belongsTo(User::class, 'foreign_key', 'owner_key')
        // Liên kết cột 'owner_id' (của bảng facilities) với cột 'user_id' (của bảng users)
        return $this->belongsTo(Users::class, 'owner_id', 'user_id');
    }

    public function courts()
    {
        return $this->hasMany(Courts::class, 'facility_id', 'facility_id');
    }
}

