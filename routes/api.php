<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\VideoConferenceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SatisfactionController;
use App\Http\Controllers\NotificationController;
use App\Mail\SendMail;

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

// Notification
Route::get( 'notification', [NotificationController::class, 'index']);
Route::post( 'notification', [NotificationController::class, 'store']);

// mail ( can remove -> just test )
Route::get('sendMail', function () {
    // $mail = new SendMail([ 'meetingId' => 'dan' ]);
    $mail = new SendMail([ 'meetingId' => 'sendmail-15-07-2022-13-18-53-865312' ]);
    Mail::to('mongkon.du@wolfy-soft.com')->send($mail);
    return 'succeed';
});