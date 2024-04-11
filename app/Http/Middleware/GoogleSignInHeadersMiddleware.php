<?php

namespace App\Http\Middleware;

use Closure;

class GoogleSignInHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Apply Cross-Origin-Opener-Policy header for Google Sign-In routes
        if ($request->is('auth/google*', 'auth/google/callback')) {
            $response = $next($request);
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            return $response;
        }

        return $next($request);
    }
}
