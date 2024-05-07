<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserAlamat;
use App\Models\UserWallet;
use App\Models\WithdrawPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointWithdrawController extends Controller
{
    //
    public function getWithdrawPointByUser()
    {
        try {
            $user_id = Auth::user()->id;
            $point_withdraw_request = WithdrawPoint::where('user_id', $user_id);
            return response()->json(['data' => $point_withdraw_request, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function createWithDrawRequest(Request $request)
    {   
        // begin transaction
        DB::beginTransaction();
        try {
            
            //code...
            Request()->validate([
                'reward_id' => 'required|integer',
            ]);

            // user harus login
            $user_id = Auth::user()->id;
            // ambil data user dengan data point di wallet
            $user_data = UserWallet::with("users")->where('user_id', $user_id)->first();
            // simpan data point user 
            $user_available_point = $user_data->total_point;
            // ambil data reward yang akan dipanggil.
            $reward = Reward::where("id", $request->reward_id);
            
            // cek poin user dengan poin yang reward butuhkan.
            if($user_available_point < $reward->point){
                return response()->json(['Message' => "Point Tidak Mencukupi", 'status' => 400]);
            }

            else{

                $data = WithdrawPoint::create([
                    'user_id' => $user_id,
                    'status_withdraw'=>"Pending",
                    'reward_id'=>$request->reward_id,
                    'amount'=>$request->point
                ]);

                // $user_data->update([
                //     'total_point' => $user_available_point - $reward->point
                // ]);

                // Commit kalau Semuanya berhasil
                DB::commit();
                
                return response()->json(['data' => $data, 'status' => 'Success']);
            }

        } catch (\Throwable $th) {
            //throw $th;
            // rollback kalau ada error di salah satu transaksi database.
            DB::rollback();
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }
}
