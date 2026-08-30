<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spectora') }}</title>

        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#1a73e8">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $navDomain = request()->route('domain');
        $navDomain = $navDomain instanceof \App\Models\Domain ? $navDomain : null;
    @endphp
    <body class="font-sans antialiased bg-studio-bg text-studio-text min-h-screen" x-data="{ mobileSidebarOpen: false }">

        <div class="flex min-h-screen">
            @include('layouts.navigation')

            <div
                x-show="mobileSidebarOpen"
                x-cloak
                class="fixed inset-0 z-50 lg:hidden flex"
                role="dialog"
                aria-modal="true"
            >
                <div class="fixed inset-0 bg-black/40" @click="mobileSidebarOpen = false"></div>
                <div class="relative w-64 bg-white border-r border-studio-border p-4 z-50">
                    <div class="flex items-center justify-between mb-6">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo class="w-7 h-7" />
                            <span class="text-base font-medium">Spectora</span>
                        </a>
                        <button type="button" @click="mobileSidebarOpen = false" class="text-studio-muted">✕</button>
                    </div>
                    <nav class="space-y-1 text-sm">
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-studio-sm {{ request()->routeIs('dashboard') ? 'bg-studio-hover text-studio-brand font-medium' : 'text-studio-muted' }}">Start</a>
                        @if($navDomain)
                            <a href="{{ route('domains.show', $navDomain) }}" class="block px-3 py-2 rounded-studio-sm bg-studio-hover text-studio-brand font-medium truncate">{{ parse_url($navDomain->url, PHP_URL_HOST) ?: $navDomain->url }}</a>
                        @endif
                        <a href="{{ route('settings.edit') }}" class="block px-3 py-2 rounded-studio-sm {{ request()->routeIs('settings.*') ? 'bg-studio-hover text-studio-brand font-medium' : 'text-studio-muted' }}">Einstellungen</a>
                    </nav>
                </div>
            </div>

            <div class="flex-1 flex flex-col min-w-0">
                <header class="h-14 bg-white border-b border-studio-border flex items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-3 min-w-0">
                        <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden text-studio-muted p-1.5" aria-label="Menü">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="text-sm truncate">
                            @if($navDomain)
                                <span class="text-studio-muted">Berichte / </span>
                                <span class="font-medium">{{ parse_url($navDomain->url, PHP_URL_HOST) ?: $navDomain->url }}</span>
                            @elseif(request()->routeIs('settings.*'))
                                <span class="font-medium">Einstellungen</span>
                            @else
                                <span class="font-medium">Start</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-studio-muted">
                        <span class="hidden sm:inline">Letzte 30 Tage</span>
                        <x-spectora.push-alerts-badge />
                    </div>
                </header>

                <main class="flex-1 p-4 sm:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </body>
</html>
