<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        return view('messages.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $channel = 'laravel-events';
        $message = $request->input('message');
        Redis::publish($channel, json_encode([
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id() ?? 'anonymous',
            'source' => 'LARAVEL'
        ]));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully to channel: ' . $channel,
                'data' => [
                    'message' => $message,
                    'channel' => $channel,
                    'timestamp' => now()->toISOString()
                ]
            ]);
        }

        return back()->with('success', 'Message sent successfully to channel: ' . $channel);
    }
}
