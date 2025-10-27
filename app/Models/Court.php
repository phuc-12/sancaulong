<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'court_id';
    protected $fillable = [
        'facility_id', 
        'court_name', 
        'status',
        
    ];
}
