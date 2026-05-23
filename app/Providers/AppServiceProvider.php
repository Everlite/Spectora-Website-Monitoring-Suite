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
        // Zusätzliche Absicherung auf URL-Generator-Ebene.
        // Die primäre HTTPS-Erkennung erfolgt in public/index.php vor Request::capture().
        \Illuminate\Support\Facades\URL::forceScheme('https');
        \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
    }
}
