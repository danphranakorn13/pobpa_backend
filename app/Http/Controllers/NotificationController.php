<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\VideoConference;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Notification::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validation
        $request->validate([
            'video_conference_id'=> 'required|integer',
            'email'=> 'required|email'
        ]);

        //check video_conference_id staus
        $videoConference = VideoConference::find( $request->video_conference_id );
        if( !$videoConference ){
            return response([
                'status' => 'failed',
                'message' => "video_conference_id: $request->video_conference_id is not found",
            ], 200 );
        }

        // check this client's ip address is available
        $user = User::where( 'public_ip', $request->ip() )->first();
        if( !$user ){
            return response( [
                'status' => 'failed',
                'message' => "user is not available",
            ], 200 );
        }
        
        $recordingStatus = $videoConference->recording_status;
        if( $recordingStatus !== 'recording'){
            return response([
                'status' => 'failed',
                'message' => "video_conference_id: $request->video_conference_id is not recording",
            ], 200 );
        }
        $newNotification = Notification::firstOrCreate([
            'video_conference_id' => $request->video_conference_id,
            'user_id'=> $user->id,
            'email'=> $request->email
        ]);

        $res = [
            'status' => 'succeed',
            'message' => "new notification is created",
            'newNotification' => $newNotification
        ];
        return response( $res, 200 );
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
