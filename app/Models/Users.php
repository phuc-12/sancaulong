<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    

    protected $fillable = [
        'fullname', 'email', 'password','role_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function Facilities()
    {
        return $this->belongsTo(Facilities::class, 'user_id');
    }
}