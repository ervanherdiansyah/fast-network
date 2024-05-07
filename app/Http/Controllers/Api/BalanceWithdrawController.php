<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\WithdrawBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceWithdrawController extends Controller
{
    //
    public function getWithdrawBalanceByUser()
    {
        try {
            $user_id = Auth::user()->id;
            $point_withdraw_request = WithdrawBalance::where('user_id', $user_id);
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
                'balance_withdrawed' => 'required|integer'
            ]);

            // user harus login
            $user_id = Auth::user()->id;
            // ambil data user dengan data point di wallet
            $user_data = UserWallet::with("users")->where('user_id', $user_id)->first();
            // simpan data point user 
            $user_available_balance = $user_data->balance;
            // ambil data reward yang akan dipanggil.
            
            // cek poin user dengan poin yang reward butuhkan.
            if($user_available_balance < 300000){
                return response()->json(['Message' => "Saldo Kurang dari 300.000", 'status' => 400]);
            }

            else if($user_available_balance < $request->balance_withdrawed){
                return response()->json(['Message' => "Saldo Tidak Mencukupi", 'status' => 400]);
            }

            else{

                $data = WithdrawBalance::create([
                    'user_id' => $user_id,
                    'status_withdraw'=>"Pending",
                    'amount_withdraw'=>$request->balance_withdrawed,
                ]);

                // $user_data->update([
                //     'total_balance' => $user_available_balance - $request->balance_withdrawed
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
