<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Spectora') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="theme-color" content="#090B10">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-studio-bg text-studio-text min-h-screen">
    <main class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center space-y-3">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center">
                    <x-application-logo class="w-10 h-10" />
                </a>
                <div>
                    <div class="text-lg font-extrabold text-white tracking-wide">Spectora</div>
                    <p class="text-xs text-studio-muted mt-1">Selbst gehostetes Website-Monitoring</p>
                </div>
            </div>

            <div class="spectora-card p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </main>
</body>
</html>
