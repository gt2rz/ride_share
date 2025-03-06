<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request){
        // validate the phone number
        $request->validate([
            'phone' => 'bail|required|numeric|min:13'
        ]);

        // find or create the user with the phone number
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if(!$user){
            return response()->json([
                'message' => 'Could not process a user with that phone number.',
                401
            ]);
        }

        // send the user one-time use code
        $user->notify(new LoginNeedsVerification());

        // return back a message
        return response()->json([
            'message' => 'Verification code sent.'
        ]);
    }

    public function verify(Request $request){
        // validate the incoming request
        $request->validate([
            'phone' => 'bail|required|numeric|min:13',
            'code' => 'bail|required|numeric|between:100000,999999'
        ]);

        // find the user with the phone number
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->code)
            ->first();

        // check if the code is correct. If so, return a token
        if($user){

            // clear the login code
            $user->login_code = null;

            return $user->createToken($request->code)->plainTextToken;

            // return response()->json([
            //     'token' => $token
            // ]);
        } 

        // if not, return back a message
        return response()->json([
            'message' => 'Invalid verification code.',
            401
        ]); 
    }
}
