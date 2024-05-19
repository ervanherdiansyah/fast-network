<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDetailController extends Controller
{
    // 
    // public function getUserDetail()
    // {
    //     try {
    //         //code...
    //         $userDetail = UserDetails::get();
    //         return response()->json(['data' => $userDetail, 'status' => 'Success']);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return response()->json(['Error' => $th]);
    //     }
    // }

    public function getUserDetailById()
    {
        $user_id = Auth::user()->id;

        try {
            //code...
            $userDetailById = UserDetails::where('user_id', $user_id)->first();
            return response()->json(['data' => $userDetailById, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    // public function createUserDetail(Request $request)
    // {
    //     try {
    //         //code...
    //         Request()->validate([
    //             'user_id' => 'required',
    //             'nik' => 'required',
    //             'nomor_wa' => 'required',
    //             'nama_kontak' => 'required',
    //             'no_kontak' => 'required',
    //             'referral_use' => 'required'
    //         ]);

    //         $data = UserDetails::create([
    //             'user_id' => $request->user_id,
    //             'nik' => $request->nik,
    //             'nomor_wa' => $request->nomor_wa,
    //             'nama_kontak' => $request->nama_kontak,
    //             'no_kontak' => $request->no_kontak,
    //             'referral_use' => $request->referral_use
    //         ]);
    //         return response()->json(['data' => $data, 'status' => 'Success']);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
    //     }
    // }

    public function updateUserDetail(Request $request)
    {
        

        try {
            //code...
            Request()->validate([
                'nik' => 'required',
                'nomor_wa' => 'required',
                'nama_kontak' => 'required',
                'no_kontak' => 'required',
            ]);

            $user_id = Auth::user()->id;
            $data = UserDetails::where('user_id', $user_id)->first();
            
            $data->update([
                'nik' => $request->nik,
                'nomor_wa' => $request->nomor_wa,
                'nama_kontak' => $request->nama_kontak,
                'no_kontak' => $request->no_kontak,
            ]);

            return response()->json(['data' => $data, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    // public function deleteUserDetail($id)
    // {
    //     try {
    //         UserDetails::where('id', $id)->first()->delete();
    //         return response()->json(['status' => 'Success']);
    //     } catch (\Throwable $th) {
    //         return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
    //     }
    // }

}
