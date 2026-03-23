<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('settings.maintenance_mode')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user?->hasRole(['admin', 'instructor'])) {
            return $next($request);
        }

        return response(view('components.maintenance-overlay'), 200);
    }
}
