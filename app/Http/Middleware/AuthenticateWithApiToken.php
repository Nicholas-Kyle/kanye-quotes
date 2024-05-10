<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::byToken($token)->first();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
