<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spectora') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#090B10">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $navDomain = request()->route('domain');
        $navDomain = $navDomain instanceof \App\Models\Domain ? $navDomain : null;
    @endphp
    <body class="font-sans antialiased bg-studio-bg text-studio-text min-h-screen selection:bg-studio-brand selection:text-white" x-data="{ mobileSidebarOpen: false }">

        <div class="flex min-h-screen bg-studio-bg">

            @include('layouts.navigation')

            <div x-show="mobileSidebarOpen"
                 x-cloak
                 class="fixed inset-0 z-50 lg:hidden flex"
                 role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-black/80" @click="mobileSidebarOpen = false"></div>
                <div class="relative flex flex-col w-72 bg-studio-bg border-r border-studio-border p-4 z-50">
                    <div class="flex items-center justify-between pb-4 border-b border-studio-border mb-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo class="w-7 h-7" />
                            <span class="text-sm font-bold text-white tracking-wide">Spectora</span>
                        </a>
                        <button type="button" @click="mobileSidebarOpen = false" class="text-studio-muted hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-studio-elevated text-white' : 'text-studio-muted hover:text-white' }}">
                            Flotte
                        </a>
                        @if($navDomain)
                            <a href="{{ route('domains.show', $navDomain) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold bg-studio-elevated text-white truncate">
                                {{ $navDomain->url }}
                            </a>
                        @endif
                        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold {{ request()->routeIs('settings.edit') ? 'bg-studio-elevated text-white' : 'text-studio-muted hover:text-white' }}">
                            Einstellungen
                        </a>
                    </nav>
                </div>
            </div>

            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

                <header class="h-14 border-b border-studio-border bg-studio-bg/90 backdrop-blur sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden text-studio-muted hover:text-white p-1.5 rounded-studio-sm border border-studio-border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>

                        <div class="flex items-center gap-2 text-xs">
                            <a href="{{ route('dashboard') }}" class="text-studio-muted hover:text-white font-medium transition-colors">Flotte</a>
                            @if($navDomain)
                                <span class="text-studio-subtle">/</span>
                                <span class="text-white font-bold truncate max-w-[16rem]">{{ $navDomain->url }}</span>
                            @elseif(request()->routeIs('settings.*'))
                                <span class="text-studio-subtle">/</span>
                                <span class="text-white font-bold">Einstellungen</span>
                            @else
                                <span class="text-studio-subtle">/</span>
                                <span class="text-white font-bold">Übersicht</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-spectora.push-alerts-badge />

                        <div class="w-7 h-7 rounded-studio-sm bg-studio-elevated border border-studio-border flex items-center justify-center font-bold text-xs text-studio-brand">
                            {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(err => {
                        console.log('SW registration failed: ', err);
                    });
                });
            }
        </script>
    </body>
</html>
