<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AffiliatorPoinHistory;
use App\Models\UserPoinHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPoinHistoryController extends Controller
{
    //
    public function getPointHistory(Request $request){
        try{
            $user_id = Auth::user()->id;
            $userPoinHistoryAsUser = UserPoinHistory::where('user_id', $user_id)->get();
            $userPoinHistoryAsAffiliator = AffiliatorPoinHistory::where('affiliator_id', $user_id)->get();
            if(!$userPoinHistoryAsAffiliator){
                $mergedUserPoinHistory = $userPoinHistoryAsUser->merge($userPoinHistoryAsAffiliator);
                return response()->json(['data' => $mergedUserPoinHistory, 'message' => 'Success'], 200); 
                // return response()->json(['data' => $userPoinHistoryAsUser, 'message' => 'Success'], 200); 
            }
            else{
                // $mergedUserPoinHistory = $userPoinHistoryAsUser->getCollection()->merge($userPoinHistoryAsAffiliator->getCollection());
                $mergedUserPoinHistory = $userPoinHistoryAsUser->merge($userPoinHistoryAsAffiliator);
                return response()->json(['data' => $mergedUserPoinHistory, 'message' => 'Success'], 200); 
            }
            
        }
        catch(\Throwable $th){
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
