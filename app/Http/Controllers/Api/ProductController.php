<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function getProduct()
    {
        try {
            //code...
            $product = Product::get();
            return response()->json(['data' => $product, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function getProductById($id)
    {
        try {
            //code...
            $product = Product::where('id', $id)->first();
            return response()->json(['data' => $product, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }
    public function createProduct(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'product_name' => 'required',
                'image' => 'nullable',
                'stock' => 'required',
            ]);

            $file_name = null;
            if ($request->hasFile('image')) {
                $file_name = $request->image->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->image->storeAs('public/product', $namaGambar);
            }


            $product = Product::create([
                'product_name' => $request->product_name,
                'image' =>  $file_name ? "product/" . $namaGambar : null,
                'stock' => $request->stock,
            ]);

            return response()->json(['data' => $product, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function updateProduct(Request $request, $id)
    {
        try {
            //code...
            Request()->validate([
                'product_name' => 'required',
                'image' => 'nullable',
                'stock' => 'required',
            ]);

            $product = Product::find($id);
            if (Request()->hasFile('image') && Request()->file('image')->isValid()) {
                if (!empty($product->image) && Storage::exists($product->image)) {
                    Storage::delete($product->image);
                }
                $file_name = $request->image->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->image->storeAs('public/product', $namaGambar);
                $product->update([
                    'product_name' => $request->product_name,
                    'image' => "product/" . $namaGambar,
                    'stock' => $request->stock,
                ]);
            } else {
                $product->update([
                    'product_name' => $request->product_name,
                    'stock' => $request->stock,
                ]);
            }

            return response()->json(['data' => $product, 'status' => 'Success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function deleteProduct($id)
    {
        try {

            Product::where('id', $id)->first()->delete();

            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['Error' => $th->getMessage(), 'status' => 500]);
        }
    }
}
