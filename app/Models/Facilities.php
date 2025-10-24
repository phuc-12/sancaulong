<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilities extends Model
{
    protected $table = 'facilities';

    protected $facifity_id = 'facility_id';

    public function Court_prices()
    {
        return $this->belongsTo(Court_prices::class, 'facility_id');
    }

}

