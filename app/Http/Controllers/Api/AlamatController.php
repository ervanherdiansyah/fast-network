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
            return response()->json(['data' => $alamat, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }
    public function getAlamatByUserId()
    {
        try {
            $user_id = Auth::user()->id;
            $alamat = UserAlamat::where('user_id', $user_id)->get();
            return response()->json(['data' => $alamat, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function getAlamatUtamaByUserId()
    {
        try {
            $user_id = Auth::user()->id;
            $alamat = UserAlamat::where('user_id', $user_id)->where('alamat_utama', true)->first();
            return response()->json(['data' => $alamat, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function createAlamat(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'alamat_lengkap' => 'required',
                'provinsi_id' => 'required|integer',
                'kota' => 'required|integer',
                'kecamatan' => 'required',
                'kelurahan' => 'required',
                'kode_pos' => 'required',
            ]);

            $alamat = UserAlamat::create([
                'user_id' => Auth::user()->id,
                'alamat_lengkap' => $request->alamat_lengkap,
                'provinsi' => $request->provinsi_id,
                'kota' => $request->kota_id,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
                'alamat_utama' => 0,
            ]);

            return response()->json(['data' => $alamat, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
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

            if($request->alamat_utama == 1){
                $previous_alamat_utama = UserAlamat::find(Auth::user()->id)->where('alamat_utama', true)->first();
                $previous_alamat_utama->update([
                    'alamat_utama' => false
                ]);

                $data = UserAlamat::where('id', $id)->update([
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'provinsi' => $request->provinsi_id,
                    'kota' => $request->kota_id,
                    'kecamatan' => $request->kecamatan,
                    'kelurahan' => $request->kelurahan,
                    'kode_pos' => $request->kode_pos,
                    'alamat_utama' => true
                ]);

                return response()->json(['data' => $data, 'status' => 'Success']);
            }

            $data = UserAlamat::where('id', $id)->update([
                'user_id' => Auth::user()->id,
                'alamat_lengkap' => $request->alamat_lengkap,
                'provinsi' => $request->provinsi_id,
                'kota' => $request->kota_id,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
            ]);
            
            return response()->json(['data' => $data, 'status' => 'Success']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function deleteAlamat($id)
    {
        try {

            $alamatById = UserAlamat::where('id', $id)->first();
            if ($alamatById->alamat_utama == 1) {
                return response()->json(['Authorized' => 'Tidak bisa dihapus karena alamat utama', 'status' => 401]);
            } else {
                UserAlamat::where('id', $id)->first()->delete();
                return response()->json(['status' => 'Success']);
            }
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }
}
