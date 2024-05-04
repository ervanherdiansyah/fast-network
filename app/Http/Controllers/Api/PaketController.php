<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaketController extends Controller
{
    //
    public function getUserDetail(){
        try {
            //code...
            $paketDetail = Paket::get();
            return response()->json(['data'=>$paketDetail, 'status'=>'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th->getMessage(), 'status'=>500]);
        }

    }

    public function getPaketByDetail($id){
        try {
            //code...
            $paketDetailById = Paket::where('id', $id)->first();
            return response()->json(['data'=>$paketDetailById, 'status'=>'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th->getMessage(), 'status'=>500]);
        }

    }

    public function createPaket(Request $request){
        try {
            //code...
            Request()->validate([
                'user_id'=>'required|integer',
                'paket_nama'=>'required|string',
                'max_quantity'=>'required|integer',
                'price'=>'required|integer',
                'weight'=>'required|integer',
                'description'=>'required|string',
                'image'=>'required|nullable',
                'point'=>'required|integer',
                'paket_kode'=>'required|string'
            ]);
            
            $file_name = $request->paket->getClientOriginalName();
            $namaGambar = str_replace(' ', '_', $file_name);
            $image = $request->paket->storeAs('public/paket', $namaGambar);

            $data = Paket::create([
                'user_id'=>$request->user_id,
                'paket_nama'=>$request->paket_nama,
                'max_quantity'=>$request->max_quantity,
                'price'=>$request->price,
                'weight'=>$request->weight,
                'description'=>$request->description,
                'image'=>"paket/".$namaGambar,
                'point'=>$request->point,
                'paket_kode'=>$request->paket_kode
            ]);
            return response()->json(['data'=>$data, 'status'=>'Success']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th, 'status'=>500]);
        }
    }


    public function updatePaket(Request $request, $id){

        try {
            //code...
            Request()->validate([
                'user_id'=>'required|integer',
                'paket_nama'=>'required|string',
                'max_quantity'=>'required|integer',
                'price'=>'required|integer',
                'weight'=>'required|integer',
                'description'=>'required|string',
                'image'=>'required|nullable',
                'point'=>'required|integer',
                'paket_kode'=>'required|string'
            ]);

            $data = Paket::find($id);
                if (Request()->hasFile('gambar')) {
                    if (Storage::exists($data->paket)) {
                        Storage::delete($data->paket);
                    }
                    $file_name = $request->paket->getClientOriginalName();
                    $namaGambar = str_replace(' ', '_', $file_name);
                    $image = $request->paket->storeAs('public/paket', $namaGambar);
                    $data->update([
                        'image' => "paket/" . $namaGambar,
                        'user_id'=>$request->user_id,
                        'paket_nama'=>$request->paket_nama,
                        'max_quantity'=>$request->max_quantity,
                        'price'=>$request->price,
                        'weight'=>$request->weight,
                        'description'=>$request->description,
                        'point'=>$request->point,
                        'paket_kode'=>$request->paket_kode
                    ]);
                } else {
                    $data->update([
                        'user_id'=>$request->user_id,
                        'nik'=>$request->paket_nama,
                        'nomor_wa'=>$request->max_quantity,
                        'price'=>$request->price,
                        'weight'=>$request->weight,
                        'description'=>$request->description,
                        'point'=>$request->point,
                        'paket_kode'=>$request->paket_kode
                ]);
            };
            
            return response()->json(['data'=>$data, 'status'=>'Success']);


        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th, 'status'=>500]);
        }

    }

    public function deletePaket($id){
        try {
            //code...
            $deletePaket = Paket::where('id', $id)->first()->delete();
            return response()->json(['status'=>'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error'=>$th->getMessage(), 'status'=>500]);
        }

    }


}
