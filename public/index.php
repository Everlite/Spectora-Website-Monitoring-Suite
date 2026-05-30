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

// Optional HTTPS hint for reverse proxies that terminate TLS without X-Forwarded-Proto.
if (file_exists($envFile = __DIR__.'/../.env')) {
    Dotenv\Dotenv::createImmutable(dirname($envFile))->safeLoad();
}

$appUrl = $_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost';
$forceHttps = filter_var($_ENV['SPECTORA_FORCE_HTTPS'] ?? false, FILTER_VALIDATE_BOOLEAN)
    || str_starts_with((string) $appUrl, 'https://');

if ($forceHttps && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = '443';
}

$app->handleRequest(Request::capture());
