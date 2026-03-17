<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentProtection
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!config('content-protection.enabled', true)) {
            return $response;
        }

        $headers = config('content-protection.security_headers', []);

        $response->headers->set('X-Content-Type-Options', $headers['x_content_type_options'] ?? 'nosniff');
        $response->headers->set('X-Frame-Options', $headers['x_frame_options'] ?? 'SAMEORIGIN');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Referrer-Policy', $headers['referrer_policy'] ?? 'same-origin');

        return $response;
    }
}
