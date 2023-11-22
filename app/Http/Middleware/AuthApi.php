<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $request->headers->set('Accept', 'Application/json');
            $request->headers->set('Content-Type', 'Application/json');

            $user = JWTAuth::parseToken()->authenticate();
        }
        catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Invalid'
                    ], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Expired'
                ],401);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization Token not found'
                ],401);
            }
        }

        $request['user'] = $user;
        return $next($request);
    }
}
