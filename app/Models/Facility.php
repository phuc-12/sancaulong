<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    // Bổ sung $fillable để updateOrCreate hoạt động
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
    protected $primaryKey = 'facility_id';
}
