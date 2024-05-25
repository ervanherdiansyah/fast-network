<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TargetBonus;
use Illuminate\Http\Request;

class TargetBonusController extends Controller
{
    public function getTargetBonus()
    {
        try {
            //code...
            $targetBonus = TargetBonus::get();
            return response()->json(['data' => $targetBonus, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
