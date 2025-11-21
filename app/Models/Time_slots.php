<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time_slots extends Model
{
    protected $table = 'time_slots';
    protected $time_slot_id = 'time_slot_id';
    public $timestamps = false;
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'time_slot_id', 'time_slot_id');
    }
}
