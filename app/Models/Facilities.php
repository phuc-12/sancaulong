<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Court_prices;

class Facilities extends Model
{
    use HasFactory;

    protected $table = 'facilities'; // nếu chưa có
    protected $primaryKey = 'facility_id';
    public $timestamps = false; // Nếu bảng không dùng created_at / updated_at

    protected $fillable = [
        'owner_id',
        'facility_name',
        'address',
        'account_no',        
        'account_bank', 
        'account_name',
        'phone',
        'open_time',
        'close_time',
        'description',
        'default_price',
        'special_price',
        'image',
        'business_license',
        'status',
        'quantity_court'
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

}

