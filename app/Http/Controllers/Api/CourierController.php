<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function getAllCourier()
    {
        try {
            $Courier = Courier::get();
            return response()->json(['data' => $Courier, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }
    public function getCourierById($id)
    {
        try {
            $Courier = Courier::where('id', $id)->first();
            return response()->json(['data' => $Courier, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }

    public function createCourier(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'courier_name' => 'required|string',
            ]);

            $Courier = Courier::create([
                'courier_name' => $request->courier_name,
            ]);
            return response()->json(['data' => $Courier, 'status' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }

    public function updateCourier(Request $request, $id)
    {
        try {
            //code...
            Request()->validate([
                'courier_name' => 'required|string',
            ]);

            $Courier = Courier::where('id', $id)->first()->update([
                'courier_name' => $request->courier_name,
            ]);
            return response()->json(['data' => $Courier, 'status' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }

    public function deleteCourier($id)
    {
        try {
            Courier::where('id', $id)->first()->delete();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => 'Error'], 500);
        }
    }
}
