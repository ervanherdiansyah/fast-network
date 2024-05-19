<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    //
    public function getBankByUserID()
    {
        try {
            //code...
            $user_id = Auth::user()->id;
            $BankUser = UserBank::where('user_id', $user_id)->get();
            return response()->json(['data' => $BankUser, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function AddBankByUserID(Request $request){
        Request()->validate([
            'nama_bank' => 'required',
            'nama_rekening' => 'required',
            'no_rekening' => 'required',
        ]);

        try {
            $user_id = Auth::user()->id;
            $newUserBank = UserBank::create([
                'user_id' =>$user_id,
                'nama_bank'=>$request->nama_bank,
                'nama_rekening'=>$request->nama_rekening,
                'no_rekening'=>$request->no_rekening,
                'rekening_utama'=>0
            ]);
            return response()->json(['data' => $newUserBank, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function updateBankDataByID(Request $request, $id)
    {
        try {
            //code...
            Request()->validate([
                'nama_bank' => 'required',
                'nama_rekening' => 'required',
                'no_rekening' => 'required',
                'rekening_utama' => 'integer'
            ]);

            $bankDetail = UserBank::find($id);
            $user_id = Auth::user()->id;

            if($user_id != $bankDetail->user_id){
                return response()->json(['message'=>'Tidak Bisa Mengubah Data Orang Lain'], 401);
            }

            if($request->rekening_utama == 1){
                $previous_main_bank = UserBank::find(Auth::user()->id)->where('rekening_utama', true)->first();
                $previous_main_bank->update([
                    'rekening_utama' => false
                ]);

                $bankDetail->update([
                    'nama_bank' => $request->nama_bank,
                    'nama_rekening' => $request->nama_rekening,
                    'no_rekening' => $request->no_rekening,
                    'rekening_utama' => true
                ]);
                
                return response()->json(['data' => $bankDetail, 'message' => 'Success'], 200);
            }
           
            $bankDetail->update([
                'nama_bank' => $request->nama_bank,
                'nama_rekening' => $request->nama_rekening,
                'no_rekening' => $request->no_rekening,
            ]);
           
            return response()->json(['data' => $bankDetail, 'message' => 'Success'], 200);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function deleteBankByID($id)
    {
        try {
            //code...
            $bankToDelete = UserBank::where('id', $id)->first();
            $user_id = Auth::user()->id;
            if($user_id != $bankToDelete->user_id){
                return response()->json(['message'=>'Tidak Bisa Menghapus Data Orang Lain'], 401);
            }

            if($bankToDelete->rekening_utama == 1){
                return response()->json(['message' => 'Tidak bisa dihapus karena akun utama'], 401);
            }
            else{
                $deleteBankDetail = UserBank::where('id', $id)->first()->delete();
                return response()->json(['message'=>'Success'], 200);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

 
}
