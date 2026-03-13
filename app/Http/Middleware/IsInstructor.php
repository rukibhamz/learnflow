<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsInstructor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || (! $user->hasRole('instructor') && ! $user->hasRole('admin'))) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
