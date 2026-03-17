<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user): bool {
            return (bool) ($user?->hasRole('admin'));
        });
    }

    /**
     * Configure Horizon authentication for the dashboard.
     */
    protected function authorization(): void
    {
        Horizon::auth(function ($request): bool {
            return Gate::check('viewHorizon', [$request->user()]) || app()->environment('local');
        });
    }
}

