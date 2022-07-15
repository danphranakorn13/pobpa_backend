<?php

namespace App\Http\Controllers;

use App\Models\VideoConference;
use App\Models\TemporaryDownloadLink;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VideoConferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return VideoConference::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'room_name'=> 'required'
        ]);
        
        // meeting name format
        $current = Carbon::now('Asia/Bangkok')->format('d-m-Y-H-i-s-u');
        $meetingName = $request->room_name . "-" . $current; 
        
        // check this meetingName is availible or not ?
        $recording_file_name = VideoConference::where('recording_file_name', $meetingName)->first();

        // if this meetingName is availible
        if(!$recording_file_name){
            $newMeetingName = VideoConference::create([
                'recording_file_name' => $meetingName
            ]);
            $response['status'] = 'succeed';
            $response['detail'] = $meetingName;
        }else{
            $response['status'] = 'failed';
            $response['detail'] = $meetingName . ' is not available';
        }

        return response( $response, 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VideoConference  $videoConference
     * @return \Illuminate\Http\Response
     */
    public function show(VideoConference $videoConference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VideoConference  $videoConference
     * @return \Illuminate\Http\Response
     */
    public function edit(VideoConference $videoConference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VideoConference  $videoConference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VideoConference $videoConference)
    {
        //
    }

    public function updateRecorded(Request $request)
    {
        //validation
        $request->validate([
            'recording_file_name'=> 'required',
            'recording_file_size' => 'required',
        ]);

        $current = Carbon::now('Asia/Bangkok')->toDateTimeString();

        $result = VideoConference::where('recording_file_name', $request->recording_file_name)
                            ->update([
                                'recording_file_size' => $request->recording_file_size,
                                'recording_status' => 'recorded',
                                'price' => 1,
                                'recorded_at' => $current
                            ]);

        return response( $result, 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VideoConference  $videoConference
     * @return \Illuminate\Http\Response
     */
    public function destroy(VideoConference $videoConference)
    {
        //
    }

    public function downloadVideoFile( $token )
    {
        // check the token is available
        $temporaryDownloadLink = TemporaryDownloadLink::where('token', $token)->first();
        
        if(!$temporaryDownloadLink){
            $responseData = [
                "status" => "failed",
                "message" => "Sorry, Token: $token is not available"
            ];
        }else{
            $recordingFileName = $temporaryDownloadLink->videoConference->recording_file_name;
            $dowloadLink = env('HUAWEI_FPS_DOMAIN') . $recordingFileName . '.mp4';
            $responseData = [
                "status" => "succeed",
                "link" => $dowloadLink,
                "transaction" => $temporaryDownloadLink->transaction,
                "videoConference" => $temporaryDownloadLink->videoConference
            ];
        }
        
        return response( $responseData, 200 );
    }

    public function detailVideoFile($meeting_name)
    {
        //validation
        if(!$meeting_name){
           return response( '404 not found', 404 );
        }

        $videoDetail = VideoConference::where('recording_file_name', $meeting_name)->first();
        
        if($videoDetail){
            return response( $videoDetail, 200 );
        }else{
            return response( 'Recoding file('. $meeting_name . ') is not found', 404 );
        }
    }

    public function updateRecordingTime($meeting_name)
    {
        //validation
        if(!$meeting_name){
            return response( '404 not found', 404 );
        }
        

        $videoDetail = VideoConference::where('recording_file_name', $meeting_name)->first();
        $current = Carbon::now('Asia/Bangkok')->toDateTimeString();
        
        if($videoDetail){
            $updateResponse = VideoConference::where('recording_file_name', $meeting_name)
                                ->update([
                                    'recording_status' => 'recording',
                                    'recording_at' => $current
                                ]);
            return $updateResponse;
        }else{
            return response( 'The ' . $meeting_name . ' is not found', 404 );
        }
    }
}
