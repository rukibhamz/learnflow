<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    public function register(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        parent::register();
    }

    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user): bool {
            return (bool) ($user?->hasRole('admin'));
        });
    }

    protected function authorization(): void
    {
        Telescope::auth(function ($request): bool {
            return Gate::check('viewTelescope', [$request->user()]) || app()->environment('local');
        });
    }
}

