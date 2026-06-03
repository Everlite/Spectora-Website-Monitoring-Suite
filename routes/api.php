<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::post('/sync', [AnalyticsController::class, 'store'])
    ->middleware('throttle:120,1');
