<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // store new user to database
        $newUser = User::firstOrCreate([
            'public_ip' => $request->ip()
        ]);
        
        return response( $newUser, 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response( $user, 200 );
    }

    public function UserValidate(Request $request)
    {
        $user = User::where( 'public_ip', $request->ip() )->first();
        if( $user ){
            return response([
                'status' => 'succeed',
                'message' => "user is available",
                'user' => $user
            ], 200 );
        }else{
            return response( [
                'status' => 'failed',
                'message' => "user is not available",
            ], 200 );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email'=> 'required|email'
        ]);
        
        $user = User::where( 'public_ip', $request->ip() )->first();
        // if user is availa
        if( $user ){
            $user->update( ['email' => $request->email] );
            return response( [
                'status' => 'succeed',
                'message' => "updated email.",
                'newEmail' => $request->email
            ], 200 );
        }else{
            return response( [
                'status' => 'failed',
                'message' => "user is not available."
            ], 200 );
        }
        
        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
