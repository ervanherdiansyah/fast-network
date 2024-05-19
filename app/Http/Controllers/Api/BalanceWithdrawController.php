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

        // ambil permintaan withdraw balance yang pernah user
        try {
            $user_id = Auth::user()->id;
            $point_withdraw_request = WithdrawBalance::where('user_id', $user_id)->get();
            return response()->json(['data' => $point_withdraw_request, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function createBalanceWithdrawRequest(Request $request)
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
            $user_available_balance = $user_data->current_balance;
            // ambil data reward yang akan dipanggil.

            // cek apakah user masih memiliki withdraw balance yang status nya pending, jika ada maka user tidak bisa membuat withdraw
            // request yang baru
            $user_previous_balance_withdraw_request_pending = WithdrawBalance::where('user_id', $user_id)->where('status_withdraw', 'Pending')->first();
            if($user_previous_balance_withdraw_request_pending){
                return response()->json(['message' => "Anda Masih Memiliki Withdraw Balance Dengan Status Pending"], 401);
            }
            
            // cek poin user dengan poin yang reward butuhkan.
            if($user_available_balance < 300000){
                return response()->json(['message' => "Saldo Kurang dari 300.000"], 401);
            }

            else if($user_available_balance < $request->balance_withdrawed){
                return response()->json(['message' => "Saldo Tidak Mencukupi"], 401);
            }

            else if($request->balance_withdrawed < 300000){
                return response()->json(['message' => "Miniwal Withdraw adalah 300.000"], 401);
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
                return response()->json(['data' => $data, 'message' => 'Success'], 200);
            }

        } catch (\Throwable $th) {
            //throw $th;
            // rollback kalau ada error di salah satu transaksi database.
            DB::rollback();
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
