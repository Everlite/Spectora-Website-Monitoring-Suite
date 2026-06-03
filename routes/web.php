<?php

use App\Http\Controllers\AgencySettingsController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\DomainNoteController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/domains/{domain}/history', [DomainController::class, 'history'])->name('domains.history');
    Route::get('/domains/{domain}', [DomainController::class, 'show'])->name('domains.show');
    Route::get('/domains/{domain}/status', [DomainController::class, 'status'])->name('domains.status');
    Route::post('/domains/{domain}/analyze', [DomainController::class, 'analyze'])
        ->middleware('throttle:6,1')
        ->name('domains.analyze');
    Route::post('/domains', [DomainController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('domains.store');
    Route::post('/domains/{domain}/settings', [DomainController::class, 'updateSettings'])
        ->middleware('throttle:30,1')
        ->name('domains.settings.update');
    Route::post('/domains/{domain}/sitemaps/detect', [DomainController::class, 'detectSitemaps'])
        ->middleware('throttle:6,1')
        ->name('domains.sitemaps.detect');
    Route::post('/domains/{domain}/urls/scan', [DomainController::class, 'scanUrls'])
        ->middleware('throttle:6,1')
        ->name('domains.urls.scan');
    Route::post('/domains/{domain}/urls/monitored', [DomainController::class, 'syncMonitoredUrls'])
        ->middleware('throttle:30,1')
        ->name('domains.urls.sync');
    Route::delete('/domains/{domain}', [DomainController::class, 'destroy'])->name('domains.destroy');
    Route::get('/domains/{domain}/analytics', [AnalyticsController::class, 'show'])->name('domains.analytics');
    Route::get('/domains/{domain}/report', [ReportController::class, 'download'])->name('domains.report');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Agency Settings
    Route::post('/profile/agency-logo', [AgencySettingsController::class, 'updateLogo'])
        ->middleware('throttle:10,1')
        ->name('agency.logo.update');

    // Domain Notes
    Route::get('/domains/{domain}/notes', [DomainNoteController::class, 'index'])->name('domains.notes.index');
    Route::post('/domains/{domain}/notes', [DomainNoteController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('domains.notes.store');
    Route::patch('/notes/{note}', [DomainNoteController::class, 'update'])
        ->middleware('throttle:30,1')
        ->name('notes.update');
    Route::delete('/notes/{note}', [DomainNoteController::class, 'destroy'])
        ->middleware('throttle:30,1')
        ->name('notes.destroy');

    // Web Push Subscriptions
    Route::post('/subscriptions', [PushSubscriptionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('subscriptions.store');

    // Admin User Management
    Route::middleware([EnsureUserIsAdmin::class])->group(function () {
        Route::post('/users', [UserController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

});

require __DIR__.'/auth.php';
