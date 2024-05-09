<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    // 
    public function getUserDetail()
    {
        try {
            //code...
            $userDetail = UserDetails::get();
            return response()->json(['data' => $userDetail, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th]);
        }
    }

    public function getUserDetailById($id)
    {
        try {
            //code...
            $userDetailById = UserDetails::where('id', $id)->first();
            return response()->json(['data' => $userDetailById, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function createUserDetail(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'user_id' => 'required',
                'nik' => 'required',
                'nomor_wa' => 'required',
                'nama_kontak' => 'required',
                'no_kontak' => 'required',
                'referral_use' => 'required'
            ]);

            $data = UserDetails::create([
                'user_id' => $request->user_id,
                'nik' => $request->nik,
                'nomor_wa' => $request->nomor_wa,
                'nama_kontak' => $request->nama_kontak,
                'no_kontak' => $request->no_kontak,
                'referral_use' => $request->referral_use
            ]);
            return response()->json(['data' => $data, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function updateUserDetailAdmin(Request $request, $id)
    {

        try {
            //code...
            Request()->validate([
                'user_id' => 'required',
                'nik' => 'required',
                'nomor_wa' => 'required',
                'nama_kontak' => 'required',
                'no_kontak' => 'required',
                'referral_use' => 'required'
            ]);

            $data = UserDetails::where('id', $id)->update([
                'user_id' => $request->user_id,
                'nik' => $request->nik,
                'nomor_wa' => $request->nomor_wa,
                'nama_kontak' => $request->nama_kontak,
                'no_kontak' => $request->no_kontak,
                'referral_use' => $request->referral_use
            ]);

            return response()->json(['data' => $data, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function deleteUserDetail($id)
    {
        try {
            UserDetails::where('id', $id)->first()->delete();
            return response()->json(['status' => 'Success']);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }
}
