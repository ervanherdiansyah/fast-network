<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWalletController extends Controller
{
    //
    public function getUserWallet()
    {
        // user harus login
        $user_id = Auth::user()->id;
        // ambil permintaan withdraw balance yang pernah user
        try {
            $user_wallet_information = UserWallet::where('user_id', $user_id)->first();
            return response()->json(['data' => $user_wallet_information, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
