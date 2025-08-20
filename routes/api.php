<?php

use App\Http\Controllers\Payment\PaymentController;
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










Route::post('/payment/process/V2', [PaymentController::class, 'paymentProcessV2']);
Route::match(['GET','POST'],'/payment/callback/V2', [PaymentController::class, 'callBackV2']);


Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);
