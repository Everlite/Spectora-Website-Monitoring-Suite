<?php

namespace App\Providers;

use App\Services\SecurityService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SecurityService::class);
    }

    public function boot(): void
    {
        $appUrl = config('app.url');

        if (str_starts_with((string) $appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        URL::forceRootUrl($appUrl);
    }
}
