<?php

namespace App\Http\Controllers;

use App\Models\Satisfaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SatisfactionController extends Controller
{
    public function index()
    {
        return Satisfaction::all();
    }

    public function store(Request $request)
    {
        //validation
        $request->validate([
            'video_conference_id'=> 'required|integer',
            // 'email'=> 'email',
            'ease'=> 'required|in:1,2,3,4,5',
            'stability'=> 'required|in:1,2,3,4,5',
            'sharpness'=> 'required|in:1,2,3,4,5'
        ]);

        // check this client's ip address is available
        $user = User::where( 'public_ip', $request->ip() )->first();
        if( !$user ){
            return response( [
                'status' => 'failed',
                'message' => "user is not available",
            ], 200 );
        }
        
        // store new satisfaction to database
        $newSatisfaction = Satisfaction::create([
            'user_id'=> $user->id,
            'video_conference_id'=> $request->video_conference_id,
            'ease'=> $request->ease,
            'stability'=> $request->stability,
            'sharpness'=> $request->sharpness
        ]);
        
        return response( $newSatisfaction, 200 );
        
    }

    public function search()
    {
        # code...
    }

}
