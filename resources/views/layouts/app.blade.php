<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spectora') }}</title>

        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0C0A08">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $navDomain = request()->route('domain');
        $navDomain = $navDomain instanceof \App\Models\Domain ? $navDomain : null;
    @endphp
    <body class="font-sans antialiased bg-studio-bg text-studio-text min-h-screen selection:bg-studio-brand selection:text-studio-bg" x-data="{ mobileNavOpen: false }">

        <div class="sp-spectrum"></div>

        @include('layouts.navigation')

        <div
            x-show="mobileNavOpen"
            x-cloak
            class="lg:hidden border-b border-studio-border bg-studio-bg px-5 py-4 space-y-3"
        >
            <a href="{{ route('dashboard') }}" class="block text-sm {{ request()->routeIs('dashboard') ? 'text-studio-text' : 'text-studio-muted' }}">Websites</a>
            @if($navDomain)
                <a href="{{ route('domains.show', $navDomain) }}" class="block text-sm text-studio-text truncate">{{ $navDomain->url }}</a>
            @endif
            <a href="{{ route('settings.edit') }}" class="block text-sm {{ request()->routeIs('settings.*') ? 'text-studio-text' : 'text-studio-muted' }}">Einstellungen</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-studio-muted hover:text-studio-rose">Abmelden</button>
            </form>
        </div>

        <main class="max-w-[1360px] mx-auto px-5 sm:px-8 py-10 sm:py-14">
            {{ $slot }}
        </main>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </body>
</html>
