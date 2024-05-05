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
            return response()->json(['data' => $BankUser, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
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
                
            ]);
            return response()->json(['data' => $newUserBank, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
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
            ]);

            $bankDetail = UserBank::find($id);
           
            $bankDetail->update([
                'nama_bank' => $request->nama_bank,
                'nama_rekening' => $request->nama_rekening,
                'no_rekening' => $request->no_rekening,
            ]);
           
            return response()->json(['data' => $bankDetail, 'status' => 'Success']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function deleteBankByID($id)
    {
        try {
            //code...
            $bankToDelete = UserBank::where('id', $id)->first();
            if($bankToDelete->rekening_utama == 1){
                return response()->json(['Unauthorized' => 'Tidak bisa dihapus karena akun utama', 'status' => 401]);
            }
            else{
                $deleteBankDetail = UserBank::where('id', $id)->first()->delete();
                return response()->json(['Success', 'status' => 200]);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

 
}
