<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPoinHistory extends Controller
{
    //
    public function getPointHistory(Request $request){
        try{
            $user_id = Auth::user()->id;
            $userPoinHistoryAsAfiliator = UserPoinHistory::where('affiliate_id', $user_id)->get();
            $userPoinHistoryAsUser = UserPoinHistory::where('user_id', $user_id)->get();
             // Merge the two collections
            $mergedUserPoinHistory = $userPoinHistoryAsAfiliator->merge($userPoinHistoryAsUser);
            return response()->json(['data' => $mergedUserPoinHistory, 'message' => 'Success'], 200); 
        }
        catch(\Throwable $th){
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
