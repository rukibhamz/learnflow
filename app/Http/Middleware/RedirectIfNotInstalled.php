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
        if (InstallerService::isInstalled()) {
            return $next($request);
        }

        // Check if the current route is an installation route
        if ($request->is('install') || $request->is('install/*') || $request->routeIs('install.*')) {
            return $next($request);
        }

        $target = route('install.welcome');
        \Illuminate\Support\Facades\Log::info('[INSTALL_DEBUG] RedirectIfNotInstalled redirect', [
            'target' => $target,
            'root' => $request->root(),
            'url' => $request->url(),
            'path' => $request->path(),
            'fullUrl' => $request->fullUrl(),
        ]);
        return redirect()->to($target);
    }
}
