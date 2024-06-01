<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function PostForgotPassword(Request $request)
    {
        try {
            //code...
            $request->validate([
                'email' => 'required|email|exists:users',
            ]);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            $expiry = Carbon::now()->addHours(1);
            $token = Str::random(64);
            $data = DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'expiry' => $expiry,
                'created_at' => Carbon::now()
            ]);

            $user = User::where('id', 1)->first();

            Mail::send('emails.forgot-password', ['token' => $token], function ($message) use ($request, $user) {
                $message->to($request->email);
                $message->subject("Reset Password");
            });

            // Alert::toast('We have send an email to reset password', 'success');
            return response()->json(['message' => 'We have sent an email to reset your password'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function PostResetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required|string|min:8|confirmed', // Pastikan ada konfirmasi password
            ]);

            $updatePassword = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$updatePassword) {
                return response()->json(['message' => 'Invalid token. Silahkan Forgot Password Kembali'], 422);
            }

            // Periksa apakah token masih berlaku
            if ($updatePassword->expiry < Carbon::now()) {
                return response()->json(['message' => 'Token expired. Silahkan Forgot Password Kembali'], 422);
            }

            // Perbarui password jika token valid dan masih berlaku
            User::where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);

            // Hapus token reset password dari database agar tidak bisa digunakan lagi
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json(['message' => 'Password berhasil direset'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
