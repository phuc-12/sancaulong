<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;
class Facility extends Model
{
    use HasFactory;

    protected $primaryKey = 'facility_id'; // Khóa chính
    protected $fillable = [
        'owner_id', 
        'facility_name', 
        'address', 
        'phone', 
        'open_time', 
        'close_time', 
        'description', 
        'business_license_path', 
        'status'
    ];
    public function owner()
    {
        // belongsTo(User::class, 'foreign_key', 'owner_key')
        // Liên kết cột 'owner_id' (của bảng facilities) với cột 'user_id' (của bảng users)
        return $this->belongsTo(Users::class, 'owner_id', 'user_id'); 
    }
    public function courtPrice()
{
    return $this->hasOne(Court_prices::class, 'facility_id', 'facility_id')->orderBy('effective_date', 'desc');
}
}
