<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class HybridAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Coba auth dengan JWT terlebih dahulu
        if ($token = $request->bearerToken()) {
            Auth::setDefaultDriver('api');
            try {
                JWTAuth::setToken($token)->authenticate();
                return $next($request);
            } catch (\Exception $e) {
                // Lanjut ke pengecekan session
            }
        }

        // Jika tidak ada token, cek session
        if (Auth::check()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
