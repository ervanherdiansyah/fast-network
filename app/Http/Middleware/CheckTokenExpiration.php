<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json(['message' => 'Token not provided'], 400);
            }

            $payload = JWTAuth::getPayload($token);
            $exp = $payload->get('exp');
            $expiration = Carbon::createFromTimestamp($exp);

            if (Carbon::now()->greaterThanOrEqualTo($expiration)) {
                return response()->json(['message' => 'Token has expired'], 401);
            }

            // Set expiration time in the request for further use
            $request->attributes->set('expires_at', $expiration);

            // Optionally, return the expiration time directly from the middleware
            if ($request->is('api/auth/check-token-expiration')) {
                return response()->json([
                    'message' => 'Token is valid',
                    'expires_at' => $expiration->toDateTimeString()
                ]);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token is absent'], 400);
        }

        return $next($request);
    }
}
