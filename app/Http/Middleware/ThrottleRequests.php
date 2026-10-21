<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = sha1(
            $request->method() . '|' .
            $request->getHost() . '|' .
            $request->path() . '|' .
            $request->ip()
        );

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $retryAfter = RateLimiter::availableIn($key);

            return response('Too Many Requests', 429)
                ->header('Retry-After', $retryAfter);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
