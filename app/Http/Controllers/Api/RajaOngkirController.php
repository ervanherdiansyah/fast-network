<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Province;
use App\Models\User;
use App\Models\UserAlamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    function getOngkir(Request $request)
    {
        try {
            $userAlamat = UserAlamat::where('user_id', Auth::user()->id)->where('alamat_utama', true)->first();
            $userOrder = Order::with('orderDetail.product', 'users', 'paket')->where('user_id', Auth::user()->id)->where('status', 'Pending')->latest()->first();
            $kurirList = Courier::all();
            // return response()->json($kurirList);

            $shippingFees = [];

            foreach ($kurirList as $kurir) {
                try {
                    $response = Http::withHeaders([
                        'key' => env('RAJAONGKIR_APIKEY'),
                    ])->post(env('RAJAONGKIR_BASE_URL') . 'cost', [
                        'origin' => env('RAJAONGKIR_ORIGIN'),
                        'destination' => $userAlamat->kota_id,
                        'weight' => $userOrder->paket->weight,
                        'courier' => $kurir->code,  // Menggunakan code kurir dari tabel kurir
                    ]);

                    $result = json_decode($response->getBody(), true);

                    if (isset($result['rajaongkir']['results'][0])) {
                        $shippingFees[] = [
                            'code' => $result['rajaongkir']['results'][0]['code'],
                            'name' => $result['rajaongkir']['results'][0]['name'],
                            'costs' => $result['rajaongkir']['results'][0]['costs'],
                        ];
                    }
                } catch (\Exception $e) {
                    // Jika terjadi kesalahan saat memanggil API untuk satu kurir, lanjutkan ke kurir berikutnya
                    // continue;
                    return response()->json(['message' =>  $e->getMessage()], 500);
                }
            }
            return response()->json(['data' => $shippingFees, 'status' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
