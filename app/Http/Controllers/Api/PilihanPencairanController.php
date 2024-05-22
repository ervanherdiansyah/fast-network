<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PilihanPencairan;
use Illuminate\Http\Request;

class PilihanPencairanController extends Controller
{
    public function getPilihanCepatPencairan()
    {
        try {
            $piliah_pencairan = PilihanPencairan::get();
            return response()->json(['data' => $piliah_pencairan, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function createPilihanCepatPencairan(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'nominal' => 'required|string',
            ]);

            $piliah_pencairan = PilihanPencairan::create([
                'nominal' => $request->nominal,
            ]);
            return response()->json(['data' => $piliah_pencairan, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function updatePilihanCepatPencairan(Request $request, $id)
    {
        try {
            //code...
            Request()->validate([
                'nominal' => 'required|string',

            ]);

            $piliah_pencairan = PilihanPencairan::where('id', $id)->first()->update([
                'nominal' => $request->nominal,

            ]);
            return response()->json(['data' => $piliah_pencairan, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function deletePilihanCepatPencairan($id)
    {
        try {
            PilihanPencairan::where('id', $id)->first()->delete();
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
