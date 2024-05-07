<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Paket;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutContoller extends Controller
{
    public function checkout(Request $request)
    {
        // $item_details = [];

        $orders = Order::with('orderDetail.product', 'users', 'paket')->where('user_id', Auth::user()->id)->where('status', 'Pending')->latest()->first();

        $totalHarga = $orders->paket->price + $request->hargaOngkir;

        $item_details[] = [
            'id' => $orders->order_code,
            'price' => $orders->paket->price,
            'quantity' => 1,
            'name' => $orders->paket->paket_nama,
        ];

        // foreach ($orders->orderDetail as $orderDetail) {
        //     $item_details[] = [
        //         'id' => $orderDetail->product->id,
        //         'price' => 1000,
        //         'quantity' => $orderDetail->quantity,
        //         'name' => $orderDetail->product->product_name,
        //     ];
        // }

        // Sistem point
        $user = User::with('userDetail')->where('id', Auth::user()->id)->first();
        $userReferal = User::where('referral', $user->userDetail->referral_use)->first();

        if ($user->first_order == true) {

            $affliator = UserWallet::where('user_id', $userReferal->id)->first();
            $affliator->total_balance += 300000;
            $affliator->current_balance += 300000;
            $affliator->save();

            $affliasi = UserWallet::where('user_id', Auth::user()->id)->first();
            $affliasi->total_point += 15;
            $affliasi->current_point += 15;
            $affliasi->save();

            $user->update([
                'first_order' => 0,
                'first_buy_success' => 1
            ]);
        } else {
            $affliator = UserWallet::where('user_id', $userReferal->id)->first();
            $affliator->total_balance += 100000 * $orders->paket->value;
            $affliator->current_balance += 100000 * $orders->paket->value;
            $affliator->total_point += 5 * $orders->paket->value;
            $affliator->current_point += 5 * $orders->paket->value;
            $affliator->save();

            $affliasi = UserWallet::where('user_id', Auth::user()->id)->first();
            $affliasi->total_point += $orders->paket->point;
            $affliasi->current_point += $orders->paket->point;
            $affliasi->save();
        }

        // return response()->json(['affliasi' => $affliasi, 'affliator' => $affliator]);

        // Payment gateway Midtrans

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        // \Midtrans\Config::$overrideNotifUrl = config('app.url') . '/api/callback';
        // \Midtrans\Config::$overrideNotifUrl = 'https://3b59-114-79-55-197.ngrok-free.app/api/callback';

        $params = array(
            'transaction_details' => array(
                'order_id' => $orders->id,
                'gross_amount' => $totalHarga,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ),
            'item_details' => $item_details,

        );
        // return response()->json($params['transaction_details']['gross_amount']);
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        return [
            'grand_total' => number_format($totalHarga),
            'snapToken' => $snapToken,
            'order' => $orders,
        ];
    }

    public function callback(Request $request)
    {

        $serverKey = config('midtrans.serverKey');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if ($hashed == $request->signature_key) {
            if (($request->transaction_status == 'capture' && $request->payment_type == 'credit_card' && $request->fraud_status == 'accept') or $request->transaction_status == 'settlement') {
                $order = Order::find($request->order_id);
                $order->update(['status' => 'Paid']);

                if ($order->status = 'Paid') {
                    $orders = Order::with('orderDetail.product', 'users', 'paket')->where('order_id', $request->order_id)->where('status', 'Pending')->latest()->first();

                    $user = User::with('userDetail')->where('id', Auth::user()->id)->first();
                    $userReferal = User::where('referral', $user->userDetail->referral_use)->first();

                    if ($user->first_order == true) {

                        $affliator = UserWallet::where('user_id', $userReferal->id)->first();
                        $affliator->total_balance += 300000;
                        $affliator->save();

                        $affliasi = UserWallet::where('user_id', Auth::user()->id)->first();
                        $affliasi->total_point += 15;
                        $affliasi->save();

                        $user->update([
                            'first_order' => 0,
                            'first_buy_success' => 1
                        ]);
                        // $user->save();
                    } else {
                        $affliator = UserWallet::where('user_id', $userReferal->id)->first();
                        $affliator->total_balance += 100000 * $orders->paket->value;
                        $affliator->total_point += 5 * $orders->paket->value;
                        $affliator->save();

                        $affliasi = UserWallet::where('user_id', Auth::user()->id)->first();
                        $affliasi->total_point += $orders->paket->point;
                        $affliasi->save();
                    }
                }
            }
        }
    }
}
