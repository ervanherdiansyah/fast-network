<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserKomisiHistory extends Controller
{
    //
    public function getKomisiHistory(Request $request){
        try{
            $user_id = Auth::user()->id;
            $userKomisiHistory = UserKomisiHistory::where('affiliate_id', $user_id)->get();
            return response()->json(['data' => $userKomisiHistory, 'message' => 'Success'], 200); 
        }
        catch(\Throwable $th){
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
