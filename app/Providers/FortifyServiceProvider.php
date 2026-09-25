<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * The public site has no accounts, so Fortify's login, logout and
     * password reset routes are not registered.
     */
    public function register(): void
    {
        Fortify::ignoreRoutes();
    }
}
