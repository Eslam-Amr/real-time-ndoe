<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent 
{
    use Dispatchable, SerializesModels;

//     public $timestamp;

//     /**
//      * Create a new event instance.
//      */
//     public function __construct(public $message,public  $channel = 'laravel-events')
//     {
//         $this->timestamp = now()->toISOString();
//     }

//     /**
//      * Get the channels the event should broadcast on.
//      *
//      * @return array<int, \Illuminate\Broadcasting\Channel>
//      */
//     // public function broadcastOn(): array
//     public function broadcastOn(): Channel
//     {
//         // return [
//         //     new Channel($this->channel),
//         // ];
//                 return new Channel($this->channel);

//     }

//     /**
//      * The event's broadcast name.
//      */
//     public function broadcastAs(): string
//     {
//         return 'message';
//     }

//     /**
//      * Get the data to broadcast.
//      */
//     public function broadcastWith()
//     // public function broadcastWith(): array
//     {
//           return   $this->message;
//         // return [
//         //     'message' => $this->message,
//         //     'timestamp' => $this->timestamp,
//         //     'channel' => $this->channel
//         // ];
//     }
// }
// class MessageSent
// {
    public function __construct(public $message, public $channel = 'laravel-events') {}
}
