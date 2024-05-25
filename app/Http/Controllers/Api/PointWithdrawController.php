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
            $point_withdraw_request = WithdrawPoint::where('user_id', $user_id)->get();
            return response()->json(['data' => $point_withdraw_request, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function createPointWithDrawRequest(Request $request)
    {   
        // begin transaction
        DB::beginTransaction();
        try {
            
            //code...
            Request()->validate([
                'reward_id' => 'required|integer',
                'nama_pemilik_rekening' => 'required|string',
                'nama_bank' => 'required|string',
                'no_rekening' => 'required|string',
                'id_alamat' => 'required|integer',
            ]);

            

            // user harus login
            $user_id = Auth::user()->id;
            // ambil data user dengan data point di wallet
            $user_data = UserWallet::with("users")->where('user_id', $user_id)->first();
            // simpan data point user 
            $user_available_point = $user_data->current_point;
            // ambil data reward yang akan dipanggil.
            $reward = Reward::where("id", $request->reward_id)->first();

            // cek apakah user masih memiliki withdraw point yang status nya pending, jika ada maka user tidak bisa membuat withdraw
            // request yang baru
            $user_previous_point_withdraw_request_pending = WithdrawPoint::where('user_id', $user_id)->where('status_withdraw', 'Pending')->first();
            if($user_previous_point_withdraw_request_pending){
                return response()->json(['message' => "Anda Masih Memiliki Withdraw Poin Dengan Status Pending"], 401);
            }
            
            // cek poin user dengan poin yang reward butuhkan.
            if($user_available_point < $reward->point){
                return response()->json(['message' => "Point Tidak Mencukupi"], 401);
            }

            else{

                $data = WithdrawPoint::create([
                    'user_id' => $user_id,
                    'status_withdraw'=>"Pending",
                    'reward_id'=>$request->reward_id,
                    'amount'=>$reward->point,
                    'nama_pemilik_rekening'=>$request->nama_pemilik_rekening,
                    'nama_bank'=>$request->nama_bank,
                    'no_rekening'=>$request->no_rekening,
                    'id_alamat'=>$request->id_alamat,
                ]);
                
                // ini mah dikurangi nya nanti kalau sudah di acc oleh admin di dashboard
                // $user_data->update([
                //     'total_point' => $user_available_point - $reward->point
                // ]);

                // Commit kalau Semuanya berhasil
                DB::commit();
                
                return response()->json(['data' => $data, 'message' => 'Success'], 200);
            }

        } catch (\Throwable $th) {
            //throw $th;
            // rollback kalau ada error di salah satu transaksi database.
            DB::rollback();
            return response()->json(['message' => 'Internal Server Error'], 401);
        }
    }
}
