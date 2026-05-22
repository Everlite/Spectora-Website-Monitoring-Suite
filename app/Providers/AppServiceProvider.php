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
            $url = config('app.url');
            \Illuminate\Support\Facades\URL::forceRootUrl($url);
            \Illuminate\Support\Facades\URL::forceScheme('https');
            // Zwingt Laravel, diesen String für alle Assets zu nutzen
            config(['app.asset_url' => rtrim($url, '/') . '/build']);
        }
    }
}
