<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPoinHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPoinHistoryController extends Controller
{
    //
    public function getPointHistory(Request $request){
        try{
            $user_id = Auth::user()->id;
            $userPoinHistoryAsUser = UserPoinHistory::where('user_id', $user_id);
            return response()->json(['data' => $userPoinHistoryAsUser, 'message' => 'Success'], 200); 
        }
        catch(\Throwable $th){
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
