<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsUrls
{
    /**
     * Handle an incoming request.
     *
     * Forces all URL generation (assets, routes, redirects) to use HTTPS
     * regardless of proxy header detection. This is a belt-and-suspenders
     * measure for setups where Nginx Proxy Manager terminates TLS but
     * Laravel's TrustProxies middleware fails to reliably detect the
     * X-Forwarded-Proto header in all code paths.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hartes Override – unabhängig von Environment, Headern oder Config
        URL::forceScheme('https');
        URL::forceRootUrl(config('app.url'));

        return $next($request);
    }
}
