<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Message;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        // broadcast(new MessageSent($message->message));
        // broadcast(new MessageSent($message->message));
    // \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($message) {
    //     broadcast(new MessageSent($message));
    // });
        // event(new MessageSent($message));
          if ($message->source == 'laravel') {
        broadcast(new MessageSent($message));
    }
        // event(new MessageSent($message, 'laravel-events'));


    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}
