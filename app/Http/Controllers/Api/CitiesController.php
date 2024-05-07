<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function getAllCities()
    {
        try {
            $cities = City::get();
            return response()->json(['data' => $cities, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }
    public function getCitiesById($id)
    {
        try {
            $cities = City::where('id', $id)->first();
            return response()->json(['data' => $cities, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }
    public function getcitiesByIdProvinsi(Request $request, $provinsi_id)
    {
        try {
            $cities = City::where('province_id', $provinsi_id)->first();
            return response()->json(['data' => $cities, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }
}
