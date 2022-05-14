<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        if (JWTAuth::parseToken()->authenticate()) {
            // this was in the if condition initially
            if (JWTAuth::user()->userType == 'mobileSeller' || JWTAuth::user()->userType == 'electronicDeviceSeller'
            || JWTAuth::user()->userType == 'accessoriesSeller' || JWTAuth::user()->userType == 'admin') {
                return $next($request);
            } else{
                return response()->json([
                    'message' => 'unauthorized'
                ]);
            }
        }
    }
}
