<?php

use App\Http\Controllers\SendMessageToNodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API route for polling messages (fallback when WebSockets are not available)
Route::get('/messages/latest', function () {
    // This is a simple fallback - in a real app you might store messages in database
    return response()->json([
        [
            'message' => 'Polling fallback message',
            'timestamp' => now()->toISOString(),
            'user_id' => 'system'
        ]
    ]);
});



// Route::post('/send-to-node', function (Request $request) {


// $channel ="laravel-events";
// $message="";

// if($request->has('message'))
// $message= $request->message  . "\n";
// else
// $message= "welcome \n";

// Redis::publish($channel,$message);

// if($request->has('message'))
// echo $request->message  . "\n";
// else
// echo "welcome \n";



//     // return view('welcome');




// });
// Route::post('/send-to-node', function (Request $request) {
//     $channel = "laravel-events";

//     $payload = [
//         'message' => $request->input('message', 'welcome'),
//         'source'  => 'laravel',
//     ];

//     // Publish as JSON
//     Redis::publish($channel, json_encode($payload));

//     // Optional response to API caller
//     return response()->json($payload);
// });
Route::post('/send-to-node', SendMessageToNodeController::class);
