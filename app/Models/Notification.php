<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'title', 'message', 'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function send($userId, $title, $message)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'is_read' => false
        ]);
    }
}