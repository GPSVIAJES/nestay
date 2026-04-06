<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateHawkRateLimit
{
    /**
     * Handle an incoming request.
     * Enforce RateHawk's 10 req/s limit per user session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'ratehawk_limit_' . ($request->user()?->id ?: $request->ip());

        if (RateLimiter::tooManyAttempts($key, config('ratehawk.rate_limit_rps', 10))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Límite de peticiones excedido. Por favor, espera un momento.'
            ], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
