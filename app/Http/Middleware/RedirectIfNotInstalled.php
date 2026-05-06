<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the current route is an installation route — always allow through
        if ($request->is('install*') || $request->routeIs('install*')) {
            return $next($request);
        }

        try {
            $installed = InstallerService::isInstalled();
        } catch (\Throwable) {
            // DB unreachable — treat as not installed
            $installed = false;
        }

        if ($installed) {
            return $next($request);
        }

        return redirect()->to(url('install'));
    }
}
