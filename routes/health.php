<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
| Ops health (Docker / local monitoring). Restricted to loopback.
*/
Route::get('/health/ops', function () {
    $ip = request()->ip();
    if (! in_array($ip, ['127.0.0.1', '::1'], true)) {
        abort(404);
    }

    $heartbeat = Cache::get('spectora:ops:heartbeat');
    if ($heartbeat === null || now()->diffInMinutes(Carbon::parse($heartbeat)) > 20) {
        return response('scheduler heartbeat stale', 503);
    }

    return response('ok', 200);
});
