<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'user_id'; // ✅ thêm dòng này
    public $timestamps = false; // Nếu bảng không dùng created_at / updated_at
}
