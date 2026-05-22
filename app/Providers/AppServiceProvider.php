<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!app()->isLocal()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
