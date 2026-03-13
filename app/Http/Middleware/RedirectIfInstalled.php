<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! InstallerService::isInstalled()) {
            return $next($request);
        }

        return redirect()->to($request->root())->with('installed', true);
    }
}
