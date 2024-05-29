<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaketController extends Controller
{
    //
    public function getPaket()
    {
        try {
            $paketDetail = Paket::get();
            return response()->json(['data' => $paketDetail, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getFilterPaket(Request $request)
    {
        try {
            //code...
            // if (!$request->hasHeader('Authorization') || !preg_match('/Bearer\s(\S+)/', $request->header('Authorization'))) {
            //     $paketDetail = Paket::where('is_visible', true)->first();
            //     return response()->json(['data' => $paketDetail, 'message' => 'Success'], 200);
            // }

            // $user = JWTAuth::parseToken()->authenticate();

            // if ($user) {
            //     $userAuth = User::where('id', Auth::user()->id)->first();
            //     if ($userAuth->first_buy_success == true) {
            //         $paketDetail = Paket::get();
            //         return response()->json(['data' => $paketDetail, 'message' => 'Success'], 200);
            //     } elseif ($userAuth->first_buy_success == false) {
            //         $paketDetail = Paket::where('is_visible', true)->first();
            //         return response()->json(['data' => $paketDetail, 'message' => 'Success'], 200);
            //     }
            // }
            $paketDetail = Paket::get();
            return response()->json(['data' => $paketDetail, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getPaketById($id)
    {
        try {
            //code...
            $paketDetailById = Paket::where('id', $id)->first();
            return response()->json(['data' => $paketDetailById, 'message' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function createPaket(Request $request)
    {
        try {
            //code...

            Request()->validate([
                'paket_nama' => 'required|string',
                'max_quantity' => 'required|integer',
                'price' => 'required|integer',
                'weight' => 'required|integer',
                'description' => 'required|string',
                'image' => 'nullable',
                'point' => 'required|integer',
                'paket_kode' => 'required|string',
                'value' => 'required',
            ]);

            $file_name = null;
            if ($request->hasFile('image')) {
                $file_name = $request->image->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->image->storeAs('public/paket', $namaGambar);
            }


            $data = Paket::create([
                'paket_nama' => $request->paket_nama,
                'max_quantity' => $request->max_quantity,
                'price' => $request->price,
                'weight' => $request->weight,
                'description' => $request->description,
                'image' => $file_name ? "paket/" . $namaGambar : null,
                'point' => $request->point,
                'paket_kode' => $request->paket_kode,
                'value' => $request->value,
            ]);
            return response()->json(['data' => $data, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }


    public function updatePaket(Request $request, $id)
    {

        try {
            //code...
            Request()->validate([
                'paket_nama' => 'required|string',
                'max_quantity' => 'required|integer',
                'price' => 'required|integer',
                'weight' => 'required|integer',
                'description' => 'required|string',
                'image' => 'nullable',
                'point' => 'required|integer',
                'paket_kode' => 'required|string',
                'value' => 'required',
            ]);

            $data = Paket::find($id);
            if (Request()->hasFile('image') && Request()->file('image')->isValid()) {
                if (!empty($data->image) && Storage::exists($data->image)) {
                    Storage::delete($data->image);
                }
                $file_name = $request->image->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->image->storeAs('public/paket', $namaGambar);

                $data->update([
                    'image' => "paket/" . $namaGambar,
                    'paket_nama' => $request->paket_nama,
                    'max_quantity' => $request->max_quantity,
                    'price' => $request->price,
                    'weight' => $request->weight,
                    'description' => $request->description,
                    'point' => $request->point,
                    'paket_kode' => $request->paket_kode,
                    'value' => $request->value,

                ]);
            } else {
                $data->update([
                    'user_id' => $request->user_id,
                    'paket_nama' => $request->paket_nama,
                    'max_quantity' => $request->max_quantity,
                    'price' => $request->price,
                    'weight' => $request->weight,
                    'description' => $request->description,
                    'point' => $request->point,
                    'paket_kode' => $request->paket_kode,
                    'value' => $request->value,

                ]);
            };

            return response()->json(['data' => $data, 'message' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function deletePaket($id)
    {
        try {
            //code...
            $deletePaket = Paket::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function setDiscount (Request $request, $id){
        Request()->validate([
            'discount_price' => 'required|integer',
            'is_discount' => 'required|integer'
        ]);

        $paket = Paket::where('id', $id)->first();
        $paket->update([
            'discount_price'=>$request->discount_price,
            'is_discount' => $request->is_discount
        ]);
    }

}
