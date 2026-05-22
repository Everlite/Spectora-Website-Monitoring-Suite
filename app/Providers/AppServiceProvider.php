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
        // Erzwinge HTTPS, wenn der Request über einen SSL-Proxy kommt oder wir nicht in der lokalen Entwicklungsumgebung sind
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        } elseif (str_contains(config('app.url'), 'https://') || !app()->isLocal()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
