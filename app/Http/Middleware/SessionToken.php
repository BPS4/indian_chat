<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SessionToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // public function handle($request, Closure $next)
    // {
    //     $token = $request->header('session-token');
    //     if (!$token) {
    //         return response()->json(['error' => 'Session token required'], 401);
    //     }

    //     try {
    //         $payload = JWTAuth::setToken($token)->getPayload();

    //         // Optional: check for specific type
    //         if ($payload->get('type') !== 'session') {
    //             return response()->json(['error' => 'Invalid session token type'], 403);
    //         }
    //     } catch (JWTException  $e) {
    //         return response()->json(['error' => 'Invalid or expired session token'], 401);
    //     }

    //     return $next($request);
    // }


    public function handle($request, Closure $next)
{
    $token = $request->header('session-token');
    if (!$token) {
        return response()->json(['error' => 'Session token required'], 401);
    }

    try {
        $user = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Set the user so $user is available in Broadcast channels
        $request->setUserResolver(fn() => $user);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid or expired token'], 401);
    }

    return $next($request);
}



}
