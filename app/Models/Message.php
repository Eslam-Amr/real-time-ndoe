<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'channel',
        'source',
        'timestamp'
    ];

    protected $casts = [
        'timestamp' => 'datetime'
    ];

    // Scope to get recent messages
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // Scope to get messages from external sources
    public function scopeExternal($query)
    {
        return $query->where('source', '!=', 'laravel');
    }

    // Scope to get messages from a specific channel
    public function scopeChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }
}
