<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CheckReferral extends Controller
{
    //
    public function userReferral(Request $request){
        try{
            $user_referral = User::where('referral', $request->referral_use)->first();
            return response()->json(['data' => $user_referral, 'message' => 'success'], 200);
        }
        catch(\Throwable $th){
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

    }
    
}
