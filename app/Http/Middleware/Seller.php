<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Seller
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
        // note that the admin should be in all all the middleware
        // return $next($request);
        if (JWTAuth::parseToken()->authenticate()) {
            if (
                JWTAuth::user()->userType == 'MobileSeller' || JWTAuth::user()->userType == 'ElectronicSeller'
                || JWTAuth::user()->userType == 'AccessoriesSeller' || JWTAuth::user()->userType == 'admin'
            ) {
                return $next($request);
            } else {
                return response()->json([
                    'user' => 'false'
                ]);
            }
        }
    }
}
