<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InfoBonus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Paket;
use App\Models\Product;
use App\Models\TargetBonus;
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
            $orders = Order::with('userAlamat', 'orderDetail.product', 'users', 'paket')->where('user_id', Auth::user()->id)->latest()->paginate(10);

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
                    'provinsi' => $order->userAlamat->provinsi->name,
                    'kota' => $order->userAlamat->kota->name,
                    'shipping_price' => $order->shipping_price,
                    'metode_pembayaran' => $order->metode_pembayaran,
                    'jenis_order' => $order->jenis_order,
                    'users' => $order->users,
                    'paket' => $order->paket,
                    'user_alamat' => $order->userAlamat,
                ];
            });

            return response()->json(['data' => $formattedOrders, 'status' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
                    'total_belanja' => $order->total_belanja,
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
            $statusPending = Order::where('user_id', Auth::user()->id)
                ->where('status', 'Pending')
                ->first();

            // Periksa apakah pesanan ditemukan dan hapus jika statusnya "Pending"
            if ($statusPending) {
                $statusPending->delete();
            }

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
            if ($paket->is_discount = true) {
                $order = new Order();
                $order->user_id = Auth::user()->id;
                $order->paket_id = $request->paketId;
                $order->order_code = $paketCode . now()->format('Ymd') . rand(10, 99);
                $order->order_date = now();
                $order->status = 'Pending';
                $order->total_harga = $paket->discount_price;
                $order->save();
            } else {
                $order = new Order();
                $order->user_id = Auth::user()->id;
                $order->paket_id = $request->paketId;
                $order->order_code = $paketCode . now()->format('Ymd') . rand(10, 99);
                $order->order_date = now();
                $order->status = 'Pending';
                $order->total_harga = $paket->price;
                $order->save();
            }


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
                ->latest()
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
                ->select('orders.user_id', DB::raw('SUM(orders.total_harga) as total_harga'), DB::raw('MAX(orders.order_date) as latest_order_date'))
                ->groupBy('orders.user_id')
                ->with('users.userDetail')
                ->latest()
                ->get();
            return response()->json(['data' => $orders, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getSumAOrderOnAfiliasiAllUser()
    {
        try {
            //code...
            $referralCode = User::where('id', Auth::user()->id)->first();

            $subTotals = Order::join('users', 'orders.user_id', '=', 'users.id')
                ->join('user_details', 'users.id', '=', 'user_details.user_id')
                ->where('user_details.referral_use', $referralCode->referral)
                ->select('orders.user_id', DB::raw('SUM(orders.total_harga) as subtotal'))
                ->groupBy('orders.user_id')
                ->with('users.userDetail')
                ->get();

            $info = InfoBonus::first();
            $target = TargetBonus::first();
            $overallSum = $subTotals->sum('subtotal');
            return response()->json(['data' => ['progress' => $overallSum, 'target' => $target, 'info' => $info], 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getOrderByUserID()
    {
        try {
            $user_id = Auth::user()->id;
            $user_orders = Order::with('paket', 'orderDetail', 'users')->where('user_id', $user_id)->get();
            $total_user_order = $user_orders->count();
            return response()->json(['data' => ['Total Orders' => $total_user_order, 'Detail Order' => $user_orders]], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getOrderByUserID2()
    {
        try {
            $user_id = Auth::user()->id;
            $user = User::where('id', $user_id)->first();
            // order diri sendiri

            $user_orders = Order::where('user_id', $user_id)->get();
            $data_order_user = [];
            foreach ($user_orders as $order) {
                $keterangan = [
                    'nama_user' => $order->users->name,
                    'nama_paket' => $order->paket->paket_nama,
                    'tanggal' => $order->order_date,
                    'keterangan' => 'Transaksi Produk'
                ];
                $data_order_user[] = $keterangan;
            }

            // order afiliasi
            // JOIN table user dengan table order
            $order_afiliasi = Order::join('users', 'orders.user_id', '=', 'users.id')
                ->join('user_details', 'users.id', '=', 'user_details.user_id')
                ->join('pakets', 'orders.paket_id', '=', 'pakets.id')
                ->where('user_details.referral_use', $user->referral)
                ->where('orders.status', 'Paid')
                ->latest()
                ->get();

            // return response()->json(['user_order' => $order_afiliasi], 200);

            $data_order_afiliasi = [];
            foreach ($order_afiliasi as $order) {
                $keterangan = [
                    'nama_afiliasi' => $order->users->name,
                    'nama_paket' => $order->paket->paket_nama,
                    'tanggal' => $order->order_date,
                    'keterangan' => 'Repeat Order Afiliasi'
                ];
                $data_order_afiliasi[] = $keterangan;
            }

            // Define a comparison function for sorting by 'tanggal' in descending order
            usort($data_order_user, function ($a, $b) {
             return strtotime($b['tanggal']) - strtotime($a['tanggal']);
            });

            usort($data_order_afiliasi, function ($a, $b) {
                return strtotime($b['tanggal']) - strtotime($a['tanggal']);
            });

            // get table order by user affiliate code.
            $total_user_order = $user_orders->count() + $order_afiliasi->count();
            return response()->json(['user_order' => $data_order_user, 'order_afilliate' => $data_order_afiliasi, 'Total_order' => $total_user_order], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
