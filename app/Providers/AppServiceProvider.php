<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('view-users', fn ($user) => $user->hasRole('Super Admin'));
        Gate::define('view-roles', fn ($user) => $user->hasRole('Super Admin'));
    }
}
