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
        if (! app()->isLocal()) {
            $url = config('app.url');

            // Erzwingt HTTPS-Schema und Root-URL für sämtliche URL-Generierung.
            // forceRootUrl hat in formatRoot() Priorität und setzt die Basis-URL
            // für asset(), route(), url() etc. auf den konfigurierten APP_URL-Wert.
            \Illuminate\Support\Facades\URL::forceScheme('https');
            \Illuminate\Support\Facades\URL::forceRootUrl($url);
        }
    }
}
