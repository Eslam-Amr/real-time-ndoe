<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Redis;

class RedisChannel
{
    public function send($notifiable, Notification $notification): void
    {
        // dd("test");
        $data = $notification->toRedis($notifiable);

        Redis::publish(
            $data['channel'] ?? 'laravel-events',
            json_encode($data)
            // "tet"
        );
    }
}
