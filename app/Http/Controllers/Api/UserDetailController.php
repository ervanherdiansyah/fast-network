<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    // 
    public function getUserDetail(){
        try {
            //code...
            $userDetail = UserDetails::get();
            return response()->json(['data'=>$userDetail, 'status'=>'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th]);
        }

    }

    public function getUserDetailById($id){
        try {
            //code...
            $userDetailById = UserDetails::where('id', $id)->first();
            return response()->json(['data'=>$userDetailById, 'status'=>'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th, 'status'=>500]);
        }

    }

    public function createUserDetail(Request $request){
        try {
            //code...
            Request()->validate([
                'user_id'=>'required',
                'nik'=>'required',
                'nomor_wa'=>'required',
                'provinsi'=>'required',
                'kota'=>'required',
                'alamat'=>'required',
                'nama_bank'=>'required',
                'no_rek'=>'required',
                'nama_kontak'=>'required',
                'referral_use'=>'required',
                'first_order'=>'required'
            ]);

            $data = UserDetails::create([
                'user_id'=>$request->user_id,
                'nik'=>$request->nik,
                'nomor_wa'=>$request->nomor_wa,
                'provinsi'=>$request->provinsi,
                'kota'=>$request->kota,
                'alamat'=>$request->alamat,
                'nama_bank'=>$request->nama_bank,
                'no_rek'=>$request->no_rek,
                'nama_kontak'=>$request->nama_kontak,
                'referral_use'=>$request->referral_use,
                'first_order'=>1
            ]);
            return response()->json(['data'=>$data, 'status'=>'Success']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th, 'status'=>500]);
        }
    }

    public function updateUserDetailAdmin(Request $request, $id){

        try {
            //code...
            Request()->validate([
                'user_id'=>'required',
                'nik'=>'required',
                'nomor_wa'=>'required',
                'provinsi'=>'required',
                'kota'=>'required',
                'alamat'=>'required',
                'nama_bank'=>'required',
                'no_rek'=>'required',
                'nama_kontak'=>'required',
                'referral_use'=>'required',
                'first_order'=>'required'
            ]);

            $data = UserDetails::where('id', $id)->update([
                'user_id'=>$request->user_id,
                'nik'=>$request->nik,
                'nomor_wa'=>$request->nomor_wa,
                'provinsi'=>$request->provinsi,
                'kota'=>$request->kota,
                'alamat'=>$request->alamat,
                'nama_bank'=>$request->nama_bank,
                'no_rek'=>$request->no_rek,
                'nama_kontak'=>$request->nama_kontak,
                'referral_use'=>$request->referral_use,
                'first_order'=>1
            ]);
            return response()->json(['data'=>$data, 'status'=>'Success']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th, 'status'=>500]);
        }

    }
}
