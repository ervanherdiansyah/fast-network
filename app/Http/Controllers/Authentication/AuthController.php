<?php

namespace App\Http\Controllers\Authentication;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserWallet;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\UserAlamat;
use App\Models\UserBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *s
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {

        DB::beginTransaction();
        try {

            Request()->validate([
                'name' => 'required',
                'nik' => 'required',
                'nomor_wa' => 'required',
                'provinsi_id' => 'required|integer',
                'kota_id' => 'required|integer',
                'alamat_lengkap' => 'required',
                'nama_bank' => 'required',
                'no_rekening' => 'required',
                'nama_kontak' => 'required',
                'no_kontak' => 'required',
                'referral_use' => 'required',
                'nama_rekening' => 'required',
                'password' => 'required|min:8',
                'email' => 'required|unique:users',
            ]);


            // $code = str_replace(' ', '', $request->name);
            // $randomNumber = rand(1000,9999);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => "mitra",
                'first_order' => 1,
                'first_buy_success' => 0,

                // 'referral ' => $randomNumber . $code
            ]);

            $UserDetails = UserDetails::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'nomor_wa' => $request->nomor_wa,
                'no_kontak' => $request->no_kontak,
                'nama_kontak' => $request->nama_kontak,
                'referral_use' => $request->referral_use,

            ]);

            $UserWallet = UserWallet::create([
                'user_id' => $user->id,
                'total_point' => 0,
                'total_balance' => 0,
                'total_referral' => 0,
                'current_balance' => 0,
                'current_point' => 0
            ]);

            $bank = UserBank::create([
                'user_id' => $user->id,
                'nama_rekening' => $request->nama_rekening,
                'no_rekening' => $request->no_rekening,
                'nama_bank' => $request->nama_bank,
                'rekening_utama' => 1

            ]);

            $alamat = UserAlamat::create([
                'user_id' => $user->id,
                'alamat_lengkap' => $request->alamat_lengkap,
                'provinsi_id' => $request->provinsi_id,
                'kota_id' => $request->kota_id,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
                'alamat_utama' => 1
            ]);

            $user_affiliate = User::where('referral', $request->referral_use)->first();
            $user_wallet_referral = UserWallet::where('user_id', $user_affiliate->id)->first();

            $user_wallet_referral->update([
                'total_referral' => $user_wallet_referral->total_referral + 1
            ]);

            // Coba login dan buat token JWT
            $credentials = ['email' => $user->email, 'password' => $request->password];

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['message' => 'Unauthorized | Invalid Credentials'], 401);
            }

            DB::commit();
            return response()->json([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'message' => 'Success Register'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            // return response()->json(['message' => 'Internal Server Error'], 500);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized | Invalid Credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {   
        try{
            JWTAuth::invalidate(JWTAuth::getToken());
            // auth()->logout();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }
        catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while logging out'], 500);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()
        ]);
    }
}
