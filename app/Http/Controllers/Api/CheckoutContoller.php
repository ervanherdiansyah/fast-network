<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AffiliatorPoinHistory;
use App\Models\Order;
use App\Models\Paket;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserKomisiHistory;
use App\Models\UserPoinHistory;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CheckoutContoller extends Controller
{
    public function checkout(Request $request)
    {

        try {
            //code...
            // $item_details = [];
            if ($request->jenis_order == 'dikirim') {
                $orders = Order::with('orderDetail.product', 'users', 'paket')->where('user_id', Auth::user()->id)->where('status', 'Pending')->latest()->first();

                // JIKA Diskon paket aktif maka pake harga diskon
                if ($orders->paket->is_discount == true) {
                    $totalHarga = $orders->paket->discount_price + $request->shipping_price;
                    $price = $orders->paket->discount_price;
                    $orders->update([
                        'shipping_courier' => $request->shipping_courier,
                        'shipping_status' => "Diproses",
                        'estimasi_tiba' => $request->estimasi,
                        'alamat_id' => $request->alamat_id,
                        'alamat_pengambilan_paket_id' => $request->alamat_pengambilan_paket_id,
                        'total_belanja' => $totalHarga,
                        'shipping_price' => $request->shipping_price,
                        'jenis_order' => $request->jenis_order,
                    ]);
                } else {
                    $totalHarga = $orders->paket->price + $request->shipping_price;
                    $price = $orders->paket->price;
                    $orders->update([
                        'shipping_courier' => $request->shipping_courier,
                        'shipping_status' => "Diproses",
                        'estimasi_tiba' => $request->estimasi,
                        'alamat_id' => $request->alamat_id,
                        'alamat_pengambilan_paket_id' => $request->alamat_pengambilan_paket_id,
                        'total_belanja' => $totalHarga,
                        'shipping_price' => $request->shipping_price,
                        'jenis_order' => $request->jenis_order,
                    ]);
                }

                $item_details = [];

                $item_details[] = [
                    'id' => $orders->order_code,
                    'price' => $price,
                    'quantity' => 1,
                    'name' => $orders->paket->paket_nama,
                ];
                $item_details[] = [
                    'id' => rand(),
                    'price' => $request->shipping_price,
                    'quantity' => 1,
                    'name' => $request->shipping_courier,
                ];

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
                \Midtrans\Config::$overrideNotifUrl = 'https://backend.fastnetwork.id/api/callback';

                $order_id = $orders->id;
                $random_string = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
                $order_id_with_random = $order_id . $random_string;

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $order_id_with_random,
                        'gross_amount' => $price,
                    ),
                    'customer_details' => array(
                        'first_name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ),
                    'enabled_payments' => array(
                        "permata_va",
                        "bca_va",
                        "bni_va",
                        "bri_va",
                        "cimb_va",
                        "other_va",
                        "echannel",
                    ),
                    'item_details' => $item_details,


                );
                // return response()->json($params['transaction_details']['gross_amount']);
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                return response()->json(['data' => [
                    'snapToken' => $snapToken,
                    'total_belanja' => $totalHarga,
                    'order' => $orders,
                ], 'status' => 'Success'], 200);
            } elseif ($request->jenis_order == 'diambil') {
                $orders = Order::with('orderDetail.product', 'users', 'paket')->where('user_id', Auth::user()->id)->where('status', 'Pending')->latest()->first();

                // JIKA Diskon paket aktif maka pake harga diskon
                if ($orders->paket->is_discount == true) {
                    $totalHarga = $orders->paket->discount_price;
                    $orders->update([
                        'alamat_id' => $request->alamat_id,
                        'alamat_pengambilan_paket_id' => $request->alamat_pengambilan_paket_id,
                        'shipping_status' => "Diproses",
                        'total_belanja' => $totalHarga,
                        'jenis_order' => $request->jenis_order,
                    ]);
                } else {
                    $totalHarga = $orders->paket->price;
                    $orders->update([
                        'alamat_id' => $request->alamat_id,
                        'alamat_pengambilan_paket_id' => $request->alamat_pengambilan_paket_id,
                        'shipping_status' => "Diproses",
                        'total_belanja' => $totalHarga,
                        'jenis_order' => $request->jenis_order,
                    ]);
                }

                $item_details[] = [
                    'id' => $orders->order_code,
                    'price' => $totalHarga,
                    'quantity' => 1,
                    'name' => $orders->paket->paket_nama,
                ];

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
                \Midtrans\Config::$overrideNotifUrl = 'https://backend.fastnetwork.id/api/callback';

                // Check transaction status
                // $status = \Midtrans\Transaction::status($orders->id);

                // If transaction is pending, cancel it
                // if ($status->transaction_status == 'pending') {
                //     \Midtrans\Transaction::cancel($orders->id);
                // }

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
                return response()->json(['data' => [
                    'snapToken' => $snapToken,
                    'total_belanja' => $totalHarga,
                    'order' => $orders,
                ], 'status' => 'Success'], 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {

        $serverKey = config('midtrans.serverKey');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if ($hashed == $request->signature_key) {
            if (($request->transaction_status == 'capture' && $request->payment_type == 'credit_card' && $request->fraud_status == 'accept') or $request->transaction_status == 'settlement') {
                $numeric_part = preg_replace('/\D/', '', $request->order_id);
                $order = Order::find($numeric_part);
                $metode_pembayaran = '';
                if ($request->payment_type === 'bank_transfer') {
                    $metode_pembayaran = $request->va_numbers[0]['bank'];
                } elseif ($request->payment_type === 'echannel') {
                    $metode_pembayaran = 'Mandiri Bill Payment';
                } elseif ($request->payment_type === 'cstore') {
                    $metode_pembayaran = isset($request->store) ? $request->store : 'cstore';
                } elseif ($request->payment_type === 'gopay') {
                    $metode_pembayaran = 'GoPay';
                } elseif ($request->payment_type === 'shopeepay') {
                    $metode_pembayaran = 'ShopeePay';
                } elseif ($request->payment_type === 'qris') {
                    $metode_pembayaran = 'QRIS';
                } elseif ($request->payment_type === 'bca_klikpay') {
                    $metode_pembayaran = 'BCA KlikPay';
                } elseif ($request->payment_type === 'bca_klikbca') {
                    $metode_pembayaran = 'BCA KlikBCA';
                } elseif ($request->payment_type === 'bri_epay') {
                    $metode_pembayaran = 'BRI Epay';
                } elseif ($request->payment_type === 'cimb_clicks') {
                    $metode_pembayaran = 'CIMB Clicks';
                } elseif ($request->payment_type === 'danamon_online') {
                    $metode_pembayaran = 'Danamon Online';
                } elseif ($request->payment_type === 'akulaku') {
                    $metode_pembayaran = 'Akulaku';
                } elseif ($request->payment_type === 'permata_va') {
                    $metode_pembayaran = 'Permata VA';
                } elseif ($request->payment_type === 'bni_va') {
                    $metode_pembayaran = 'BNI VA';
                } elseif ($request->payment_type === 'other_va') {
                    $metode_pembayaran = 'Other VA';
                } elseif ($request->payment_type === 'alfamart') {
                    $metode_pembayaran = 'Alfamart';
                } elseif ($request->payment_type === 'indomaret') {
                    $metode_pembayaran = 'Indomaret';
                } else {
                    $metode_pembayaran = $request->payment_type;
                }
                $order->update([
                    'status' => 'Paid',
                    'metode_pembayaran' => $metode_pembayaran

                ]);

                $user = User::with('userDetail')->where('id', $order->user_id)->first();
                $userReferal = User::where('referral', $user->userDetail->referral_use)->first();

                $paket_terjual = Paket::where('id', $order->paket_id)->first();
                $paket_terjual->update([
                    'jumlah_terjual' => $paket_terjual->jumlah_terjual + 1,
                ]);

                if ($user->first_order == true) {
                    $affliator = UserWallet::where('user_id', $userReferal->id)->first();

                    // IF Transaksi Pertama = Paid generate kode referal
                    $user_name_character = str_split($user->name);
                    $first_user_character_name = $user_name_character[0];
                    $user_id_as_string = (string)$user->id;
                    $random_string = Str::random(6);
                    $referal_token = $first_user_character_name . $user_id_as_string . $random_string;

                    $user->update([
                        'referral' => $referal_token
                    ]);

                    // IF TRANSAKSI PERTAMA TRUE
                    // History Uang Yang Didapat via Komisi Referral.

                    // GET History Komisi Menggunakan affiliator_id !!!
                    $komisi_history_affiliator = UserKomisiHistory::create([
                        'affiliator_id' => $userReferal->id,
                        'affiliate_id' => $user->id,
                        'keterangan' => 'Kode Referal',
                        'order_id' => $order->id,
                        'info_transaksi' => 'Komisi Kode Referal Afiliasi',
                        'jumlah_komisi' => 300000
                    ]);

                    // Komisi Poin Si Pembeli
                    // GET History Poin ada dua, satu userPoinHistory satu AffiliatorPoinHistory
                    $komisi_poin_user = UserPoinHistory::create([
                        'user_id' => $user->id,
                        'keterangan' => 'Transaksi Produk',
                        'order_id' => $order->id,
                        'info_transaksi' => 'Transaksi',
                        'jumlah_poin' => 15
                    ]);


                    $affliator->total_balance += 300000;
                    $affliator->current_balance += 300000;
                    $affliator->save();

                    $affliasi = UserWallet::where('user_id', $user->id)->first();
                    $affliasi->total_point += 15;
                    $affliasi->current_point += 15;
                    $affliasi->save();

                    $user->update([
                        'first_order' => 0,
                        'first_buy_success' => 1
                    ]);
                } else {
                    $affliator = UserWallet::where('user_id', $userReferal->id)->first();
                    $affliator->total_balance += 100000 * $order->paket->value;
                    $affliator->current_balance += 100000 * $order->paket->value;
                    $affliator->total_point += 5 * $order->paket->value;
                    $affliator->current_point += 5 * $order->paket->value;
                    $affliator->save();

                    // History Uang Yang Didapat via Komisi Repeat Order.

                    // GET History Komisi Menggunakan affiliator_id !!!
                    // Komisi Affiliator Repeat Order
                    $komisi_history = UserKomisiHistory::create([
                        'affiliator_id' => $userReferal->id,
                        'affiliate_id' => $user->id,
                        'keterangan' => 'Repeat Order',
                        'order_id' => $order->id,
                        'info_transaksi' => 'Komisi Repeat Order',
                        'jumlah_komisi' => 100000 * $order->paket->value
                    ]);

                    // History Poin Yang Didapat 
                    // GET History Poin ada dua, satu Get By user_id dan kedua get by affiliate_id kemudian disatukan\

                    // Poin Yang Didapat User Ketika Repeat Order
                    $user_poin_history = UserPoinHistory::create([
                        'user_id' => $user->id,
                        'keterangan' => 'Transaksi Produk',
                        'order_id' => $order->id,
                        'info_transaksi' => 'Transaksi',
                        'jumlah_poin' => $order->paket->point
                    ]);

                    // Poin yang Didapat Affiliator Ketika User Repeat Order
                    $affiliator_poin_history = AffiliatorPoinHistory::create([
                        'affiliator_id' => $userReferal->id,
                        'affiliate_id' => $user->id,
                        'keterangan' => 'Repeat Order Afiliasi',
                        'order_id' => $order->id,
                        'info_transaksi' => 'Komisi Poin Repeat Order',
                        'jumlah_poin' => 5 * $order->paket->value
                    ]);

                    $affliasi = UserWallet::where('user_id', $user->id)->first();
                    $affliasi->total_point += $order->paket->point;
                    $affliasi->current_point += $order->paket->point;
                    $affliasi->save();
                }
            }
        }
    }
}
