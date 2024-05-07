<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function getProvinces()
    {
        $apiKey = env('RAJAONGKIR_APIKEY');
        $response = Http::withHeaders([
            'key' => $apiKey,
        ])->get('https://api.rajaongkir.com/starter/province');

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Mendapatkan data dari respons JSON
            $data = $response->json();

            // Periksa apakah atribut 'rajaongkir' dan 'results' ada dalam respons
            if (isset($data['rajaongkir'], $data['rajaongkir']['results'])) {
                // Ambil data provinsi dari hasil respons
                $provinces = $data['rajaongkir']['results'];

                // Iterasi melalui setiap provinsi dan simpan ke dalam tabel provinces
                foreach ($provinces as $province) {
                    Province::create([
                        'name'        => $province['province'],
                    ]);
                }
            } else {
                // Tangani kesalahan jika respons tidak mengandung data yang diharapkan
                // Misalnya, jika format respons tidak sesuai dengan yang diharapkan
                return response()->json(['error' => 'Invalid response format'], 500);
            }
        } else {
            // Tangani kesalahan jika permintaan gagal
            return response()->json(['error' => 'Failed to fetch provinces'], $response->status());
        }
    }

    public function getCities()
    {
        $apiKey = env('RAJAONGKIR_APIKEY');
        $response = Http::withHeaders([
            'key' => $apiKey,
        ])->get('https://api.rajaongkir.com/starter/city');

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            // Mendapatkan data dari respons JSON
            $data = $response->json();
            // return response()->json(['data' => $data], 200);


            // Periksa apakah atribut 'rajaongkir' dan 'results' ada dalam respons
            if (isset($data['rajaongkir'], $data['rajaongkir']['results'])) {
                // Ambil data provinsi dari hasil respons
                $cities = $data['rajaongkir']['results'];

                // Iterasi melalui setiap provinsi dan simpan ke dalam tabel cities
                foreach ($cities as $province) {
                    City::create([
                        'province_id' => $province['province_id'],
                        'name'        => $province['city_name'],
                    ]);
                }
                return response()->json(['message' => 'Successfully'], 200);
            } else {
                // Tangani kesalahan jika respons tidak mengandung data yang diharapkan
                // Misalnya, jika format respons tidak sesuai dengan yang diharapkan
                return response()->json(['error' => 'Invalid response format'], 500);
            }
        } else {
            // Tangani kesalahan jika permintaan gagal
            return response()->json(['error' => 'Failed to fetch cities'], $response->status());
        }
    }
}
