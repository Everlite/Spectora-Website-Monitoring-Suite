<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GeoLite2 database path
    |--------------------------------------------------------------------------
    |
    | Download GeoLite2-City.mmdb from MaxMind and place it here when not
    | using Cloudflare (CF-IPCountry / CF-IPCity) headers on trusted proxies.
    |
    */

    'geolite2_path' => env('ANALYTICS_GEOLITE2_PATH', storage_path('app/geoip/GeoLite2-City.mmdb')),

    /*
    |--------------------------------------------------------------------------
    | Trusted reverse proxy (Cloudflare, etc.)
    |--------------------------------------------------------------------------
    |
    | When set, geo headers from the edge proxy are accepted. Mirrors
    | TRUSTED_PROXIES in bootstrap/app.php — keep both configured together.
    |
    */

    'trusted_proxies' => env('TRUSTED_PROXIES'),

];
