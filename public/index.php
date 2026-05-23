<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Force HTTPS detection at the PHP level BEFORE Request::capture() reads $_SERVER.
// Required because the Nginx Proxy Manager terminates TLS but does not forward
// X-Forwarded-Proto headers. Without this, Laravel sees every request as HTTP.
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

$app->handleRequest(Request::capture());
