<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowFrameAncestors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Set the CSP header to allow your WordPress subdomain
        $response->headers->set(
            'Content-Security-Policy',
            "frame-ancestors 'self' https://interactive.fenix24ransim.com http://127.0.0.1:8000 http://127.0.0.1:8080"
        );

        // Remove the X-Frame-Options header if it exists
        $response->headers->remove('X-Frame-Options');

        return $response;
    }
} 