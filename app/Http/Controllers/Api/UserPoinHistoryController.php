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
            $userPoinHistoryAsUserRaw = UserPoinHistory::where('user_id', $user_id)->get();
            $userPoinHistoryAsAffiliatorRaw = AffiliatorPoinHistory::where('affiliator_id', $user_id)->get();

            $userPoinHistoryAsUser = [];
            $userPoinHistoryAsAffiliator=[];

            foreach($userPoinHistoryAsUserRaw as $data){
                $bonuspoin=[
                    'keterangan'=>$data->keterangan,
                    'info_transaksi'=>'Transaksi',
                    'jumlah_poin'=>$data->jumlah_poin,
                    'created_at'=>$data->created_at,
                    'updated_at'=>$data->updated_at
                ];
                $userPoinHistoryAsUser[] = $bonuspoin;
            }

            foreach($userPoinHistoryAsAffiliatorRaw as $data){
                $bonuspoin=[
                    'keterangan'=>$data->keterangan,
                    // info transaksi diganti yang dari table tapi nama dari si affiliate, dari affiliate_id
                    'info_transaksi'=>$data->komisipoin_affiliate_id->name,
                    'jumlah_poin'=>$data->jumlah_poin,
                    'created_at'=>$data->created_at,
                    'updated_at'=>$data->updated_at
                ];
                $userPoinHistoryAsUser[] = $bonuspoin;
            }

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
