<?php

namespace App\Models;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Facility;

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
        return $this->belongsTo(Facilities::class, 'facility_id', 'facility_id');
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
    ];
    protected static function boot()
    {
        parent::boot(); 

        static::creating(function ($user) {
            Log::info('User creating event - Attributes:', $user->getAttributes()); 
        });

    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

}