<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Court_prices extends Model
{
    protected $table = 'court_prices';
    public $timestamps = false;

    protected $primaryKey = 'court_price_id';

    protected $fillable = [
        'facility_id',
        'default_price',
        'special_price',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'default_price' => 'decimal:2',
        'special_price' => 'decimal:2',
    ];

    public function facility()
    {
        return $this->belongsTo(Facilities::class, 'facility_id', 'facility_id');
    }


}
