<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $message, public $channel = 'laravel-events') {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['redis'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'message'   => $this->message->message,
            'channel'   => $this->channel,
            'timestamp' => now()->toISOString(),
            'source'    => $this->message->source ?? 'laravel',
        ];
    }

    /**
     * Get the Redis representation of the notification.
     */
    public function toRedis($notifiable): array
    {
        return [
            'event'     => 'message.sent',
            'id'        => $this->message->id,
            'message'   => $this->message->message,
            'source'    => $this->message->source ?? 'laravel',
            'channel'   => $this->channel,
            'timestamp' => now()->toISOString(),
        ];
    }
}
