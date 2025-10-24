<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilities extends Model
{
    protected $table = 'facilities'; // nếu chưa có
    protected $primaryKey = 'facility_id'; // ✅ thêm dòng này
    public $timestamps = false; // Nếu bảng không dùng created_at / updated_at

    public function Court_prices()
    {
        return $this->belongsTo(Court_prices::class, 'facility_id');
    }

    public function Users()
    {
        return $this->belongsTo(Users::class, 'owner_id');
    }

}

