<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GambarBannner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GambarBannerController extends Controller
{
    //
    public function getAllBannerImages()
    {
        try {
            //code...
            $banner_images = GambarBannner::get();
            return response()->json(['data' => $banner_images, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function getBannerImagesBy($id)
    {
        try {
            //code...
            $banner_image = GambarBannner::where('id', $id)->first();
            return response()->json(['data' => $banner_image, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function createNewBannerImage(Request $request)
    {
        try {
            //code...

            Request()->validate([
                'nama_gambar' => 'required|string',
                'file_path' => 'nullable',
            ]);

            $file_name = null;
            if ($request->hasFile('file_path')) {
                $file_name = $request->file_path->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->file_path->storeAs('public/gambar_banner', $namaGambar);
            }

            $data = GambarBannner::create([
                'nama_gambar' => $request->nama_gambar,
                'file_path' => $file_name ? "gambar_banner/" . $namaGambar : null
            ]);
            return response()->json(['data' => $data, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateBannerImage(Request $request, $id)
    {
        try {
            //code...
            Request()->validate([
                'nama_gambar' => 'required|string',
                'file_path' => 'nullable',
            ]);

            $data = GambarBannner::find($id);
            if (Request()->hasFile('file_path') && Request()->file('file_path')->isValid()) {
                if (!empty($data->file_path) && Storage::disk('public')->exists($data->file_path)) {
                    Storage::disk('public')->delete($data->file_path);
                }
                $file_name = $request->file_path->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->file_path->storeAs('public/gambar_banner', $namaGambar);

                $data->update([
                    'nama_gambar' => $request->nama_gambar,
                    'file_path' => $file_name ? "gambar_banner/" . $namaGambar : null

                ]);
            } else {
                $data->update([
                    'nama_gambar' => $request->nama_gambar,
                ]);
            };

            return response()->json(['data' => $data, 'message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage(), 'status' => 500]);
        }
    }

    public function deleteGambarBanner($id)
    {
        try {
            //code...
            $data = GambarBannner::where('id', $id)->first();
            if (!empty($data->file_path) && Storage::disk('public')->exists($data->file_path)) {
                Storage::disk('public')->delete($data->file_path);
            }
            $deleteGambar = GambarBannner::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
