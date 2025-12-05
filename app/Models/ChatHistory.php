<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'entities' => 'array',
        'reply'     => 'array', // vì reply có thể là mảng nhiều tin
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}
