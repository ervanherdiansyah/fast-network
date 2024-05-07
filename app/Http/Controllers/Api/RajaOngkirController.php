<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
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
        $userAlamat = UserAlamat::where('user_id', Auth::user()->id)->where('alamat_utama', true)->first();
        $userOrder = Order::with('orderDetail.products', 'users', 'paket')->where('user_id', Auth::user()->id)->where('status', 'Pending')->latest()->first();

        $shippingFees = [];
        try {
            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_APIKEY'),
            ])->post(env('RAJAONGKIR_BASE_URL') . 'cost', [
                'origin' => env('RAJAONGKIR_ORIGIN'),
                'destination' => $userAlamat->kota_id,
                'weight' => $userOrder->paket->weight,
                'courier' => strtolower($request->courier),
            ]);

            $shippingFees = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [];
        }

        return response()->json(['data' => $shippingFees, 'status' => 'Success']);
    }
}
