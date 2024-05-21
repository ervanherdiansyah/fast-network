<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Storage;

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
            $user_id = User::with('users')->where('id', Auth::user()->id)->first();
            $useReferralByUser = UserDetails::where('referral_use', $user_id->referral)->get();
            return response()->json(['data' => $useReferralByUser, 'message' => 'Success'], 200);
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
                'name'=>$request->name,
                'foto_profil'=> $file_name ? "foto_profil/" . $namaGambar : null,
                'email'=>$request->email
            ]);
            
            

            DB::commit();
            return response()->json(['data' => $data, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function updateUserPassword(Request $request){

        Request()->validate([
            'current_password'=>'required|string',
            'new_password'=>'required|string',
            'confirmation_password'=>'required|string'
        ]);

        $user_id = Auth::user()->id;
        $user_data = User::where('id', $user_id)->first();

        
        $database_current_password = $user_data->password();
        $current_password = $request->current_password;
        $new_password = $request->new_password;
        $confirmation_password = $request->confirmation_password;

        if(Hash::check($database_current_password, $current_password)){
            if(Hash::check($new_password, $confirmation_password)){
                $user_data->update([
                    'password'->bcrypt($new_password)
                ]);
                
                return response()->json(['message'=>'Password Berhasil Diganti'], 200);
            }
            else{
                return response()->json(['message'=>'Password Tidak Cocok'], 401);
            }
        }
        else{
            return response()->json(['message'=>'Password Lama Salah'], 401);
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
