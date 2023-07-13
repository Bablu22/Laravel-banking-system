<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException ) {
                return response()->json(['message' => 'Invalid token']);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException ) {
                return response()->json(['message' => 'Token expired']);
            } else {
                return response()->json(['message' => 'Authorization token not found']);
            }
        }

        $request->auth = $user;

        return $next($request);
    }
}
