<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Spectora') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="theme-color" content="#1a73e8">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-studio-bg text-studio-text min-h-screen">
    <main class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <a href="{{ url('/') }}" class="flex items-center gap-2 mb-8">
            <x-application-logo class="w-8 h-8" />
            <span class="text-xl font-medium">Spectora</span>
        </a>
        <div class="w-full max-w-sm spectora-card p-8">
            {{ $slot }}
        </div>
        <p class="mt-6 text-xs text-studio-muted">Datenschutzfreundliche Analytics. Self-hosted.</p>
    </main>
</body>
</html>
