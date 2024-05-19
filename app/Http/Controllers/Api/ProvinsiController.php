<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinsiController extends Controller
{
    public function getAllProvinsi()
    {
        try {
            $provinsi = Province::get();
            return response()->json(['data' => $provinsi, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getProvinsiById($id)
    {
        try {
            $provinsi = Province::where('id', $id)->first();
            return response()->json(['data' => $provinsi, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
