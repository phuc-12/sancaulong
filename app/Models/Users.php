<?php

namespace App\Models;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';


    protected $fillable = [
        'fullname',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'role_id',
        'facility_id',
        'status',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function Facilities()
    {
        return $this->belongsTo(Facilities::class, 'user_id');
    }
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
    ];
    protected static function boot()
    {
        parent::boot(); // Gọi hàm boot của lớp cha

        // Lắng nghe sự kiện "creating" (TRƯỚC KHI insert vào CSDL)
        static::creating(function ($user) {
            // Ghi log toàn bộ thuộc tính ($attributes) của user sắp được tạo
            Log::info('User creating event - Attributes:', $user->getAttributes()); 
        });

        // (Bạn cũng có thể thêm static::saving(...) để debug cả khi update)
        // static::saving(function ($user) {
        //     Log::info('User saving event - Attributes:', $user->getAttributes());
        // });
    }
}