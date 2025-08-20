<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Payment\PaymentController;

// Message interface routes
Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');

// Route for external messages (Node.js, Redis CLI, etc.)
Route::post('/messages/external', [MessageController::class, 'storeExternal'])->name('messages.external');

// Default route - redirect to messages
Route::get('/', function () {
    return redirect()->route('messages.index');
});

// Test route for Redis publishing (keeping your original logic)
Route::get('/test-redis', function () {
    $channel = "laravel-events";
    $message = "test\n";

    Redis::publish($channel, $message);

    echo "message sent to channel: " . $channel;
});

// Test route for sending messages from external sources (Node.js, etc.)
Route::post('/test-external', function () {
    $channel = request('channel', 'laravel-events');
    $message = request('message', 'Test message from external source');

    // Publish to Redis
    Redis::publish($channel, json_encode([
        'message' => $message,
        'timestamp' => now()->toISOString(),
        'user_id' => 'external-source',
        'source' => 'nodejs'
    ]));

    return response()->json([
        'success' => true,
        'message' => 'Message published to Redis channel: ' . $channel,
        'data' => [
            'channel' => $channel,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ]
    ]);
});

// Simple test route to simulate Node.js message
Route::get('/test-node-message', function () {
    $channel = "laravel-events";
    $message = "Hello from Node.js! - " . now()->toTimeString();

    // Publish to Redis
    Redis::publish($channel, json_encode([
        'message' => $message,
        'timestamp' => now()->toISOString(),
        'user_id' => 'nodejs-server',
        'source' => 'nodejs'
    ]));

    return response()->json([
        'success' => true,
        'message' => 'Test Node.js message sent to Redis',
        'data' => [
            'channel' => $channel,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ]
    ]);
});






Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment-failed', [PaymentController::class, 'failed'])->name('payment.failed');

// V2 Payment routes
Route::get('/payment-success-v2', [PaymentController::class, 'successV2'])->name('payment.success.v2');
Route::get('/payment-failed-v2', [PaymentController::class, 'failedV2'])->name('payment.failed.v2');
