<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Services\InstallerService;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\RedirectIfNotInstalled::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'instructor' => \App\Http\Middleware\IsInstructor::class,
            'student' => \App\Http\Middleware\IsStudent::class,
            'not-suspended' => \App\Http\Middleware\EnsureNotSuspended::class,
            'enrolled' => \App\Http\Middleware\EnsureEnrolled::class,
            'content-protection' => \App\Http\Middleware\ContentProtection::class,
            'maintenance' => \App\Http\Middleware\MaintenanceModeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // If the database is unavailable (dropped/missing), redirect to installer
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            // Error codes: 1049 = unknown database, 2002 = connection refused, 1045 = access denied
            $dbErrorCodes = [1049, 2002, 1045, 2003];
            $shouldRedirectToInstall = ! InstallerService::isInstalled();
            if (
                (in_array((int) $e->getCode(), $dbErrorCodes) || str_contains($e->getMessage(), 'Unknown database'))
                && $shouldRedirectToInstall
                && ! $request->is('install*')
            ) {
                return redirect()->to(url('install'));
            }
        });

        $exceptions->render(function (\PDOException $e, \Illuminate\Http\Request $request) {
            if (InstallerService::isInstalled()) {
                return null;
            }

            if ($request->is('install*')) {
                return null;
            }

            return redirect()->to(url('install'));
        });
    })->create();
