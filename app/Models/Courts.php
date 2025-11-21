<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courts extends Model
{
    use HasFactory;

    protected $table = 'courts';
    protected $primaryKey = 'court_id';
    public $incrementing = false;

    protected $fillable = [
        'court_id',
        'facility_id',
        'court_name',
        'status',
    ];

    // Override getKeyName để trả về array
    public function getKeyName()
    {
        return ['court_id', 'facility_id'];
    }

    // Override setKeysForSaveQuery để xử lý composite key
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    // Override getKeyForSaveQuery
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    public function facility()
    {
        return $this->belongsTo(Facilities::class, 'facility_id', 'facility_id');
    }
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'court_id', 'court_id');
    }
}