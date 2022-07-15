<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VideoConference;
use App\Models\Transaction;
use App\Models\TemporaryDownloadLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{    
    public function createPaymentCreditCard(Request $request)
    {
        $public_key = env('GB_PAYMENT_PUBLIC_KEY');
        $request->validate([
            'recording_file_name' => 'required',
            'credit_card_name'=>'required|string',
            'credit_card_code'=>'required|string',
            'credit_card_expire'=>'required|string',
            'credit_card_ccv'=>'required|string'
        ]);

        // check recording_file_name is available
        $videoConference = VideoConference::where('recording_file_name', $request->recording_file_name)->first();
        if(!$videoConference){
            return response( "ไม่พบไฟล์ $request->recording_file_name", 404);
        }

        // $credit_card_code = str_replace("-", "", $request->credit_card_code );
        $expriredMonth = substr($request->credit_card_expire, 0, 2);
        $expriredYear = substr($request->credit_card_expire, 2, 2);

        $data = [
                "rememberCard"=> false,
                "card" => [
                    "name" => $request->credit_card_name,
                    "number" => $request->credit_card_code,
                    "expirationMonth" => $expriredMonth,
                    "expirationYear" => $expriredYear,
                    "securityCode" => $request->credit_card_ccv,
                ]
        ];

        if (env('APP_DEBUG') == true) {
            $url = env('GB_DEBUG_URL').'/v2/tokens';
        } else {
            $url = env('GB_PRODUCTION_URL').'/v2/tokens';
        }

        $response = Http::withBasicAuth($public_key, '')->withHeaders([
            'Content-Type: application/json',
        ])->post($url,
            $data
        );

        if( $response->getStatusCode() !== 200 || $response['resultCode'] != "00"){
            return response( $response , 400)->header('Content-Type', 'application/json');
        }

        $token = $response['card']['token'];
        $newTransaction = Transaction::create([
            'video_conference_id'=> $videoConference->id,
            'price'=> $videoConference->price,
            'status'=> 'G',  // C = Created
            'payment_method'=> 'C' // C = Credit Card
        ]);
        
        $conferenceRoomId = $videoConference->recording_file_name;
        $secret_key = env('GB_PAYMENT_SECRET_KEY');
        $data2 = [
            "amount" => $newTransaction->price,
            "referenceNo" =>  env('GB_REFERENCE_NO') .  $newTransaction->id,
            "card" => [
                "token" => $token
            ],
            "otp" => "Y",
            "merchantDefined1" => "pobpa",
            "responseUrl" => "https://pobpa.com/payments?id=$conferenceRoomId&transactionId=$newTransaction->id&payment=true",
            // "responseUrl" => "http://localhost:3000/payments?id=$conferenceRoomId&transactionId=$newTransaction->id&payment=true",
            "backgroundUrl" => "https://api.pobpa.com/api/payment/backgroundUrl"
        ];

        if (env('APP_DEBUG') == true) {
            $url = env('GB_DEBUG_URL').'/v2/tokens/charge';
        } else {
            $url = env('GB_PRODUCTION_URL').'/v2/tokens/charge';
        }

        $response = Http::withBasicAuth($secret_key, '')->withHeaders([
            'Content-Type: application/json',
        ])->post($url,
            $data2
        );

        $newTransaction->update(['response' => $response]);

        return response( $response , 200)->header('Content-Type', 'application/json');

    }

    public function send3DSecure(Request $request)
    {
        $public_key = env('GB_PAYMENT_PUBLIC_KEY');

        $request->validate([
            'gbpReferenceNo'=>'required|string',
        ]);

        $gbpReferenceNo = $request->gbpReferenceNo;
        if (env('APP_DEBUG') == true) {
            $url = env('GB_DEBUG_URL').'/v2/tokens/3d_secured';
        } else {
            $url = env('GB_PRODUCTION_URL').'/v2/tokens/3d_secured';
        }
        $data3 = [
            'publicKey' => $public_key,
            'gbpReferenceNo' => $gbpReferenceNo
        ];
        $response2 = Http::asForm()->post($url,
            $data3
        );

        return response($response2, 200)->header('Content-Type', 'text/html');;
    }

    public function backgroundUrl(Request $request)
    {
        $respFile = fopen("resp-log.txt", "w") or die("Unable to open file!");

        $respResultCode = $_POST["resultCode"];
        fwrite($respFile, "resultCode : " . $respResultCode . "\n");
      
        $respAmount = $_POST["amount"];
        fwrite($respFile, "amount : " . $respAmount . "\n");
      
        $respReferenceNo = $_POST["referenceNo"];
        fwrite($respFile, "referenceNo : " . $respReferenceNo . "\n");
      
        $respGbpReferenceNo = $_POST["gbpReferenceNo"];
        fwrite($respFile, "gbpReferenceNo : " . $respGbpReferenceNo . "\n");
      
        $respCurrencyCode = $_POST["currencyCode"];
        fwrite($respFile, "currencyCode : " . $respCurrencyCode . "\n");
      
        fclose($respFile);
    }

    public function createPaymentQRCode(Request $request)
    {
        $request->validate([
            'recording_file_name' => 'required',
        ]);

        // check recording_file_name is available
        $videoConference = VideoConference::where('recording_file_name', $request->recording_file_name)->first();
        if(!$videoConference){
            return response( "ไม่พบไฟล์ $request->recording_file_name", 404);
        }

        $newTransaction = Transaction::create([
            'video_conference_id'=> $videoConference->id,
            'price'=> $videoConference->price,
            'status'=> 'G',  // C = Created
            'payment_method'=> 'Q' // Q = Qr Cash
        ]);

        if (env('APP_DEBUG') == true) {
            $url = env('GB_DEBUG_URL').'/v3/qrcode';
        } else {
            $url = env('GB_PRODUCTION_URL').'/v3/qrcode';
        }

        $pobpaResponse = Http::asForm()->withHeaders([
            'content-type' => 'image/png',
        ])->post($url, [
            'token' => env('GB_PAYMENT_TOKEN'),
            'referenceNo' => env('GB_REFERENCE_NO') . $newTransaction->id,
            'amount' => $newTransaction->price,
            "merchantDefined1" => "pobpa",
        ]);

        if ($pobpaResponse->getStatusCode() != 200){
            $newTransaction->update(['response' => $pobpaResponse]); // update response
            return response( $pobpaResponse, $pobpaResponse->getStatusCode() );
        }

        $pobpaResponseBase64 = base64_encode( $pobpaResponse ); // convert .png to base64
        $newTransaction->update(['response' => $pobpaResponseBase64]); // update response to database

        return response([
            "transaction_id" =>  $newTransaction->id,
            "base64_image_qrcode" => $pobpaResponseBase64
        ], 200 );
    
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer'
        ]);

        // check recording_file_name is available
        $currentTransaction = Transaction::where('id', $request->transaction_id)->first();
        if(!$currentTransaction){
            return response( "ไม่พบรหัสธุระกรรม $request->transaction_id", 404);
        }
        
        $secret_key = env('GB_PAYMENT_SECRET_KEY');

        if (env('APP_DEBUG') == true) {
            $url = env('GB_DEBUG_URL').'/v1/check_status_txn';
        } else {
            $url = env('GB_PRODUCTION_URL').'/v1/check_status_txn';
        }

        $pobpaResponse = Http::withBasicAuth($secret_key, '')->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'referenceNo' => env('GB_REFERENCE_NO') . $request->transaction_id
        ]);

        // return response( $pobpaResponse, 200)->header('Content-Type', 'application/json');
 

        if( $pobpaResponse->getStatusCode() === 200){
            if ( $pobpaResponse['resultCode'] == "00" ){
                
                // if paymentType is QRcode
                if ( $pobpaResponse['txn']['paymentType'] == 'Q' ){
                    if ( $pobpaResponse['txn']['status'] == 'S' ) {
                         // insert new transaction to database
                        $newTransaction = Transaction::create([
                            'video_conference_id'=> $currentTransaction->video_conference_id,
                            'price'=> $pobpaResponse['txn']['totalAmount'],
                            'status'=> $pobpaResponse['txn']['status'],
                            'payment_method'=> $pobpaResponse['txn']['paymentType'], // Q = Qr Cash
                            'response'=> $pobpaResponse
                        ]);
                        
                        // generate token
                        $token =  base64_encode( $newTransaction->id . $newTransaction->video_conference_id . Carbon::now()->timestamp );
                        
                        // insert newTemporary download link to database
                        $newTemporaryDownloadLink = TemporaryDownloadLink::create([
                            'video_conference_id'=> $newTransaction->video_conference_id,
                            'transaction_id'=> $newTransaction->id,
                            'token'=> $token,
                        ]);

                        $responseData = [
                            "status" => "succeed",
                            "token" => $token
                        ];

                        return response( $responseData, 200 );
                    } else {
                        return response(["status" => "preparing"], 200);
                    }
                
                // if paymentType is Credit card
                } elseif ( $pobpaResponse['txn']['paymentType'] == 'C' ) {
                    $pobpaStatus = $pobpaResponse['txn']['status'];
                    if ( $pobpaStatus == 'A' || $pobpaStatus == 'V' || $pobpaStatus == 'D' || $pobpaStatus == 'S') {
                        $newTransaction = Transaction::create([
                            'video_conference_id'=> $currentTransaction->video_conference_id,
                            'price'=> $currentTransaction->price,
                            'status'=> $pobpaResponse['txn']['status'],
                            'payment_method'=> $pobpaResponse['txn']['paymentType'], // C = Credit Card
                            'response'=> $pobpaResponse
                        ]);


                        if( $pobpaStatus == 'A' || $pobpaStatus == 'S' ){
                            // generate token
                            $token =  base64_encode( $newTransaction->id . $newTransaction->video_conference_id . Carbon::now()->timestamp );
                            
                            // insert newTemporary download link to database
                            $newTemporaryDownloadLink = TemporaryDownloadLink::create([
                                'video_conference_id'=> $newTransaction->video_conference_id,
                                'transaction_id'=> $newTransaction->id,
                                'token'=> $token,
                            ]);

                            $responseData = [
                                "status" => "succeed",
                                "token" => $token
                            ];
    
                            return response( $responseData, 200 );
                        } else {
                            return response(["status" => "failed"], 200);
                        }
                        
                    } else {
                        return response(["status" => "preparing"], 200);
                    }
                } else {
                    return response( 'PaymentType is not C or Q', 400);
                }

            } else{
                return response( $pobpaResponse, 400)->header('Content-Type', 'application/json');
            }
        } else {
            return response( $pobpaResponse, $pobpaResponse->getStatusCode() )->header('Content-Type', 'application/json');
        }
    }
}