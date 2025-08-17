<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SendMessageToNodeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // $channel = "laravel-events";

        $message = Message::create(
            [
                'message' => $request->input('message', 'welcome'),
                'source' => 'LARAVEL',
            ]
        );

        // $payload = [
        //     'message' => $request->input('message', 'welcome'),
        //     'source'  => 'laravel',
        // ];
        // broadcast(new MessageSent($payload));

        // // Publish as JSON
        // Redis::publish($channel, json_encode($payload));

        // Optional response to API caller
        return response()->json($message);
    }
}
