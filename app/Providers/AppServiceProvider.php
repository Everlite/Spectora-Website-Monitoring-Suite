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
        // Erzwinge HTTPS, wenn APP_URL mit https:// beginnt
        if (str_starts_with(config('app.url', ''), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
