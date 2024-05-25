<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationModel;
use Auth;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    //
    public function getUserNotification(){
        try{
            $user_id = Auth::user()->id;
            $user_notifications = NotificationModel::where('user_id', $user_id)->latest()->get();
            return response()->json(['notifications'=>$user_notifications], 200);
        }
        catch(\Throwable $th){
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function createNotification(Request $request){
        try{
            Request()->validate([
                'jenis_notifikasi' => 'required|string',
                'judul' => 'required|string',
                'content' => 'required|string'
            ]);

            
            $notification = NotificationModel::create([
                'user_id' => 1,
                'jenis_notifikasi'=>"$request->jenis_notifikasi",
                'judul'=>$request->judul,
                'content'=>$request->content,
                'is_read'=>0
            ]);

            return response()->json(['message' => 'Success'], 200);
        }
        catch(\Throwable $th){
            return response()->json(['message'=>'Internal Server Error'], 500);
        }
    }
}
