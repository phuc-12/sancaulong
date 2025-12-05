<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $table = 'chat_histories';

    protected $fillable = [
        'user_id',
        'message',
        'reply',
        'intent',
        'entities',
        'session_key',
        'ip',
        'user_agent',
    ];

    // Tự động ép kiểu dữ liệu
    protected $casts = [
        'reply' => 'array',
        'entities' => 'array', // Tự động decode JSON thành mảng PHP khi lấy ra và ngược lại
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Một lịch sử chat có thể thuộc về một User (nếu user_id không null)
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    // Scope để lọc theo user
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope lấy số lượng gần nhất
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}