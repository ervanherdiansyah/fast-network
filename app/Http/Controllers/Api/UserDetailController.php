<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
            $userDetailById = UserDetails::with('users')->where('user_id', $user_id)->first();

            return response()->json(['data' => $userDetailById, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function getUseReferralByUser()
    {

        try {
            //code...
            $user_id = User::where('id', Auth::user()->id)->first();
            $useReferralByUser = UserDetails::with('users')->where('referral_use', $user_id->referral)->get();
            return response()->json(['data' => $useReferralByUser, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function getCountUseReferralByUser()
    {

        try {
            //code...
            $user_id = User::where('id', Auth::user()->id)->first();
            $useReferralByUser = UserDetails::with('users')->where('referral_use', $user_id->referral)->get();
            $count = $useReferralByUser->count();
            return response()->json(['data' => $count, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }



    public function updateUserDetail(Request $request)
    {


        try {
            //code...
            DB::beginTransaction();
            Request()->validate([
                'nik' => 'required',
                'nomor_wa' => 'required',
                'foto_profil' => 'nullable',
                'nama_kontak' => 'required',
                'no_kontak' => 'required',
                'name' => 'required|string',
                'email' => 'required|string'
            ]);


            $user_id = Auth::user()->id;
            $user = User::where('id', $user_id)->first();
            $data = UserDetails::where('user_id', $user_id)->first();

            $data->update([
                'nik' => $request->nik,
                'nomor_wa' => $request->nomor_wa,
                'nama_kontak' => $request->nama_kontak,
                'no_kontak' => $request->no_kontak,
            ]);

            $file_name = null;
            if (Request()->hasFile('foto_profil') && Request()->file('foto_profil')->isValid()) {
                if (!empty($user->foto_profil) && Storage::exists($user->foto_profil)) {
                    Storage::delete($user->foto_profil);
                }
                $file_name = $request->foto_profil->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->foto_profil->storeAs('public/foto_profil', $namaGambar);
            };

            $user->update([
                'name' => $request->name,
                'foto_profil' => $file_name ? "foto_profil/" . $namaGambar : null,
                'email' => $request->email
            ]);

            DB::commit();
            return response()->json(['data' => $data, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function updatepassword(Request $request)
    {
        try {
            //code...
            $request->validate([
                'password' => ['required', 'confirmed'],
            ]);
            $user =  User::where('id', Auth::user()->id)->first();
            $user->update([
                'password' =>  bcrypt($request->password),
            ]);

            return response()->json(['message' => 'berhasil ubah password'], 400);
            // if (Hash::check($request->current_password, auth()->user()->password)) {
            //     auth()->user()->update(['password' => Hash::make($request->password)]);
            //     return response()->json(['message' => 'Success Change Password'], 200);
            // }
            // throw ValidationException::withMessages([
            //     'current_password' => 'your current password does not mact with our record',
            // ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function confirmationpassword(Request $request)
    {
        try {
            //code...

            $request->validate([
                'password_confirmation' => ['required'],
            ]);

            if (Hash::check($request->password_confirmation, auth()->user()->password)) {
                return response()->json(['message' => 'your Password Confirmation is correct.'], 200);
            } else {
                return response()->json(['message' => 'your Password Confirmation does not mact with our record'], 400);
            }
            throw ValidationException::withMessages([
                'current_password' => 'your current password does not mact with our record',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
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
