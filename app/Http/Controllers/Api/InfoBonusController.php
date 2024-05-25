<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InfoBonus;
use Illuminate\Http\Request;

class InfoBonusController extends Controller
{
    public function getInfoBonus()
    {
        try {
            //code...
            $infobonus = InfoBonus::get();
            return response()->json(['data' => $infobonus, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
