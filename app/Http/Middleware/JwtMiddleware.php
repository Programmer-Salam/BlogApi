<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $payload = $this->jwtService->validateToken($token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        auth()->onceUsingId($payload['user_id']);

        return $next($request);
    }
}
