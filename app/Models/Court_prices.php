<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Court_prices extends Model
{
    protected $table = 'court_prices';

    protected $primaryKey = 'court_price_id';

    public function Facilities()
    {
        return $this->belongsTo(Facilities::class, 'facility_id');
    }

}
