<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Spectora') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="theme-color" content="#0C0A08">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-studio-bg text-studio-text min-h-screen selection:bg-studio-brand selection:text-studio-bg">
    <div class="sp-spectrum"></div>

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        <div class="hidden lg:flex flex-col justify-between px-16 py-16 border-r border-studio-border">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <x-application-logo class="w-9 h-9" />
                <span class="sp-display text-3xl text-studio-text">Spectora</span>
            </a>
            <div class="max-w-md space-y-6">
                <p class="sp-display text-5xl text-studio-text leading-[1.05]">
                    Eine Wahrheit<br>pro Website.
                </p>
                <p class="text-sm text-studio-muted leading-relaxed">
                    Pulse ohne Cookie. Uptime, SSL und Watchdog aus demselben Request.
                    Kein Google, kein Chrome-Theater. Dein Server.
                </p>
            </div>
            <p class="text-[11px] uppercase tracking-[0.2em] text-studio-subtle">Self-hosted · First-party · Deutsch</p>
        </div>

        <main class="flex flex-col justify-center px-6 sm:px-12 py-16">
            <div class="lg:hidden mb-10 flex items-center gap-3">
                <x-application-logo class="w-8 h-8" />
                <span class="sp-display text-2xl">Spectora</span>
            </div>
            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
