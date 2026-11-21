<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Hardcoded credentials
        $username = 'user';
        $password = 'password';

        $authHeader = $request->header('Authorization');

        if (
            !$authHeader ||
            !str_starts_with($authHeader, 'Basic ')
        ) {
            return response()->json([
                'Type' => 'E',
                'Message' => 'Unauthorized'
            ], 401);
        }

        $credentials = base64_decode(substr($authHeader, 6), true);

        if ($credentials === false) {
            return response()->json([
                'Type' => 'E',
                'Message' => 'Invalid Authorization Header'
            ], 401);
        }

        if ($credentials !== "{$username}:{$password}") {
            return response()->json([
                'Type' => 'E',
                'Message' => 'Invalid Credentials'
            ], 401);
        }

        return $next($request);
    }
}