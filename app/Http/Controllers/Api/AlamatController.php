<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAlamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function getAlamat()
    {
        try {
            $alamat = UserAlamat::get();
            return response()->json(['data' => $alamat, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function getAlamatByUserId()
    {
        try {
            $user_id = Auth::user()->id;
            $alamat = UserAlamat::with('users')->where('user_id', $user_id)->get();
            return response()->json(['data' => $alamat, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getAlamatUtamaByUserId()
    {
        try {
            $user_id = Auth::user()->id;
            $alamat = UserAlamat::with('users')->where('user_id', $user_id)->where('alamat_utama', true)->first();
            return response()->json(['data' => $alamat, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function createAlamat(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'alamat_lengkap' => 'required',
                'provinsi_id' => 'required|integer',
                'kota_id' => 'required|integer',
                'kecamatan' => 'required',
                'kelurahan' => 'required',
                'kode_pos' => 'required',
                'alamat_utama' => 'integer|nullable'
            ]);

            if($request->alamat_utama){
                $user_id = Auth::user()->id;
                $previous_alamat_utama = UserAlamat::find(Auth::user()->id)->where('alamat_utama', true)->first();
                $previous_alamat_utama->update([
                    'alamat_utama' => false
                ]);

                $alamat = UserAlamat::create([
                    'user_id' => Auth::user()->id,
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'provinsi_id' => $request->provinsi_id,
                    'kota_id' => $request->kota_id,
                    'kecamatan' => $request->kecamatan,
                    'kelurahan' => $request->kelurahan,
                    'kode_pos' => $request->kode_pos,
                    'alamat_utama' => 1,
                ]);

                return response()->json(['data' => $alamat, 'message' => 'Success'], 200);
            }

            $alamat = UserAlamat::create([
                'user_id' => Auth::user()->id,
                'alamat_lengkap' => $request->alamat_lengkap,
                'provinsi_id' => $request->provinsi_id,
                'kota_id' => $request->kota_id,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
                'alamat_utama' => 0,
            ]);

            return response()->json(['data' => $alamat, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function updateAlamat(Request $request, $id)
    {

        try {
            //code...
            Request()->validate([
                'alamat_lengkap' => 'required',
                'provinsi_id' => 'required|integer',
                'kota_id' => 'required|integer',
                'kecamatan' => 'required',
                'kelurahan' => 'required',
                'kode_pos' => 'required',
                'alamat_utama' => 'integer'
            ]);

            $data = UserAlamat::where('id', $id)->first();
            $user_id = Auth::user()->id;

            if ($user_id != $data->user_id) {
                return response()->json(['message' => 'Tidak Bisa Mengubah Data Orang Lain'], 401);
            }


            if ($request->alamat_utama == 1) {
                $previous_alamat_utama = UserAlamat::find(Auth::user()->id)->where('alamat_utama', true)->first();
                $previous_alamat_utama->update([
                    'alamat_utama' => false
                ]);

                $data->update([
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'provinsi_id' => $request->provinsi_id,
                    'kota_id' => $request->kota_id,
                    'kecamatan' => $request->kecamatan,
                    'kelurahan' => $request->kelurahan,
                    'kode_pos' => $request->kode_pos,
                    'alamat_utama' => true
                ]);

                return response()->json(['data' => $data, 'message' => 'Success'], 200);
            }


            $data->update([
                'alamat_lengkap' => $request->alamat_lengkap,
                'provinsi_id' => $request->provinsi_id,
                'kota_id' => $request->kota_id,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
            ]);

            return response()->json(['data' => $data, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function deleteAlamat($id)
    {
        try {

            $alamatById = UserAlamat::where('id', $id)->first();
            $user_id = Auth::user()->id;

            if ($user_id != $alamatById->user_id) {
                return response()->json(['message' => 'Tidak Bisa Menghapus Data Orang Lain'], 401);
            }

            if ($alamatById->alamat_utama == 1) {
                return response()->json(['message' => 'Tidak bisa dihapus karena alamat utama'], 401);
            } else {
                UserAlamat::where('id', $id)->first()->delete();
                return response()->json(['message' => 'Success'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
