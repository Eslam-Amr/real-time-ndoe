<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Notifications\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        // Log::info('Sending message notification');
        // // Send notification through custom Redis channel
        // Notification::route('redis', 'laravel-events')
        //     ->notify(new MessageNotification($event->message, $event->channel));
    }
}
