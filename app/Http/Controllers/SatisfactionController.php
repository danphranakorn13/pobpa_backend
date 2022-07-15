<?php

namespace App\Http\Controllers;

use App\Models\Satisfaction;
// use App\Models\VideoConference;
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
        
        // store new satisfaction to database
        $newSatisfaction = Satisfaction::create( $request->all() );
        
        return response( $newSatisfaction, 200 );
        
    }

    public function search()
    {
        # code...
    }

}
