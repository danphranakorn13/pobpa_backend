<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoConferenceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SatisfactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resource('videoconference', VideoConferenceController::class);
Route::put('/updateRecorded', [VideoConferenceController::class, 'updateRecorded']);
Route::get('/downloadvideofile/{token}', [VideoConferenceController::class, 'downloadVideoFile']);
Route::get('/detailvideofile/{meeting_name}', [VideoConferenceController::class, 'detailVideoFile']);
Route::put('/updaterecordingtime/{meeting_name}', [VideoConferenceController::class, 'updateRecordingTime']);

// payment
Route::post( 'payment/createPaymentQRCode', [PaymentController::class, 'createPaymentQRCode']);
Route::post( 'payment/verifyPayment', [PaymentController::class, 'verifyPayment']);
Route::post( 'payment/createPaymentCreditCard', [PaymentController::class, 'createPaymentCreditCard']);
Route::post( 'payment/backgroundUrl', [PaymentController::class, 'backgroundUrl']);
Route::post( 'payment/send3DSecure', [PaymentController::class, 'send3DSecure']);

// Satisfaction
Route::get( 'satisfaction', [SatisfactionController::class, 'index']);
Route::post( 'satisfaction', [SatisfactionController::class, 'store']);