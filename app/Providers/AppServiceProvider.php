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
        // Unbedingtes HTTPS-Forcing für sämtliche URL-Generierung.
        // Greift auf Service-Provider-Ebene bevor Middleware und Requests
        // verarbeitet werden – als Fallback zur ForceHttpsUrls-Middleware.
        \Illuminate\Support\Facades\URL::forceScheme('https');
        \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
    }
}
