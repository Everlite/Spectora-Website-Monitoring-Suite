<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $trustedProxies = env('TRUSTED_PROXIES');

        if ($trustedProxies !== null && $trustedProxies !== '') {
            $at = $trustedProxies === '*'
                ? '*'
                : array_values(array_filter(array_map('trim', explode(',', $trustedProxies))));

            $middleware->trustProxies(at: $at);
        }

        $middleware->validateCsrfTokens(except: [
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetUserTimezone::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
