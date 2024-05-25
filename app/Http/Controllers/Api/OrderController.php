<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Paket;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function getAllOrder()
    {
        try {
            $orders = Order::where('users.userAlamat')->paginate(10);

            return response()->json(['data' => $orders, 'status' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getOrderById($id)
    {
        try {
            $orders = Order::where('id', $id)->first();

            return response()->json(['data' => $orders, 'status' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getAllOrderByUser()
    {
        try {
            $orders = Order::with('orderDetail.product', 'users', 'paket', 'userAlamat')->where('user_id', Auth::user()->id)->where('status', 'Paid')->latest()->paginate(10);

            // Format ulang data pesanan
            $formattedOrders = $orders->map(function ($order) {
                $products = $order->orderDetail->map(function ($detail) {
                    return $detail->product->product_name . ' ' . $detail->quantity;
                })->implode(', ');

                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'user_id' => $order->user_id,
                    'paket_id' => $order->paket_id,
                    'order_code' => $order->order_code,
                    'order_date' => $order->order_date,
                    'status' => $order->status,
                    'shipping_status' => $order->shipping_status,
                    'shipping_courier' => $order->shipping_courier,
                    'total_harga' => $order->total_harga,
                    'alamat_id' => $order->alamat_id,
                    'no_resi' => $order->no_resi,
                    'estimasi_tiba' => $order->estimasi_tiba,
                    'product' => $products,
                    'users' => $order->users,
                    'paket' => $order->paket,
                    'userAlamat' => $order->userAlamat
                ];
            });

            return response()->json(['data' => $formattedOrders, 'status' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getOrderByUserIdOnOrder()
    {
        try {
            $orders = Order::with('orderDetail.product', 'users', 'paket', 'userAlamat')->where('user_id', Auth::user()->id)->where('status', 'Pending')->latest()->first();

            $formattedOrders = $orders->map(function ($order) {
                $products = $order->orderDetail->map(function ($detail) {
                    return $detail->product->product_name . ' ' . $detail->quantity;
                })->implode(', ');

                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'user_id' => $order->user_id,
                    'paket_id' => $order->paket_id,
                    'order_code' => $order->order_code,
                    'order_date' => $order->order_date,
                    'status' => $order->status,
                    'shipping_status' => $order->shipping_status,
                    'shipping_courier' => $order->shipping_courier,
                    'total_harga' => $order->total_harga,
                    'alamat_id' => $order->alamat_id,
                    'no_resi' => $order->no_resi,
                    'estimasi_tiba' => $order->estimasi_tiba,
                    'product' => $products,
                    'users' => $order->users,
                    'paket' => $order->paket,
                    'userAlamat' => $order->userAlamat
                ];
            });

            return response()->json(['data' => $formattedOrders, 'status' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function addToOrder(Request $request)
    {
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Ambil paket
            $paketId = $request->input('paketId');
            $paket = Paket::findOrFail($paketId);
            $maxQuantity = $paket->max_quantity;
            $paketCode = $paket->paket_kode;
            $hargaPaket = $paket->price;


            $jsonData = $request->all();

            // Inisialisasi variabel untuk menyimpan jumlah total produk
            $totalQuantity = 0;

            // Iterasi melalui array produk
            foreach ($jsonData['products'] as $product) {
                // Tambahkan jumlah produk ke totalQuantity
                $totalQuantity += $product['quantity'];
            }

            // Buat order baru
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->paket_id = $request->paketId;
            $order->order_code = $paketCode . now()->format('Ymd') . rand(10, 99);
            $order->order_date = now();
            $order->status = 'Pending';
            $order->total_harga = $hargaPaket;
            $order->save();

            // Buat detail order untuk setiap produk
            foreach ($request->input('products') as $product) {
                // Cek ketersediaan stok produk
                $requestedQuantity = $product['quantity'];

                $productId = $product['product_id'];
                $requestedProduct = Product::find($productId);

                if (!$requestedProduct || $requestedQuantity > $requestedProduct->stock) {
                    DB::rollback();
                    return response()->json(['message' => 'Requested quantity exceeds available stock'], 400);
                } elseif ($requestedQuantity > 5) {
                    DB::rollback();
                    return response()->json(['message' => 'Request quantity does not exceed 5'], 400);
                }

                // Cek apakah jumlah produk yang diminta lebih dari max quantity
                if ($totalQuantity > $maxQuantity) {
                    DB::rollback();
                    return response()->json(['message' => 'Requested quantity exceeds maximum quantity allowed'], 400);
                } elseif ($totalQuantity < $maxQuantity) {
                    DB::rollback();
                    return response()->json(['message' => 'Requested quantity exceeds minumum quantity allowed'], 400);
                }

                // Buat detail order untuk produk ini
                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $order->id;
                $orderDetail->product_id = $productId;
                $orderDetail->quantity = $requestedQuantity;
                $orderDetail->save();
            }

            // Commit transaksi
            DB::commit();

            return response()->json(['data' => ['order' => $order, 'orderDetail' => $orderDetail], 'message' => 'Products added to order successfully'], 200);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getOrderByUserOnAfiliasi()
    {
        try {
            $referralCode = User::where('id', Auth::user()->id)->first();

            $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
                ->join('user_details', 'users.id', '=', 'user_details.user_id')
                ->where('user_details.referral_use', $referralCode->referral)
                ->select('orders.*', 'users.name as user_name', 'user_details.referral_use')
                ->with('users.userDetail', 'orderDetail.product')
                ->get();

            return response()->json(['data' => $orders, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getSumOrderOnAfiliasiByUser()
    {
        try {
            //code...
            $referralCode = User::where('id', Auth::user()->id)->first();

            $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
                ->join('user_details', 'users.id', '=', 'user_details.user_id')
                ->where('user_details.referral_use', $referralCode->referral)
                ->select('orders.user_id', DB::raw('SUM(orders.total_harga) as total_harga'))
                ->groupBy('orders.user_id')
                ->with('users.userDetail')
                ->get();
            return response()->json(['data' => $orders, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
