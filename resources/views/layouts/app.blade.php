<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spectora') }} - Fleet Monitoring</title>

        <!-- Fonts (Inter + JetBrains Mono) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- PWA / Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#09090b">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-foreground min-h-screen selection:bg-primary selection:text-primary-foreground" x-data="{ mobileSidebarOpen: false }">
        
        <div class="flex min-h-screen bg-background">
            
            <!-- 1. Left Sidebar Navigation (Desktop) -->
            @include('layouts.navigation')

            <!-- Mobile Sidebar Drawer -->
            <div x-show="mobileSidebarOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 lg:hidden flex" 
                 role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
                <div class="relative flex flex-col w-72 bg-sidebar border-r border-border p-4 z-50">
                    <div class="flex items-center justify-between pb-4 border-b border-border mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-primary text-primary-foreground flex items-center justify-center font-black text-xs">S</div>
                            <span class="text-sm font-bold text-foreground">Spectora</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="text-muted-foreground hover:text-foreground">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium bg-secondary text-foreground">
                            Dashboard
                        </a>
                        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium text-muted-foreground hover:text-foreground">
                            Einstellungen
                        </a>
                    </nav>
                </div>
            </div>

            <!-- 2. Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                
                <!-- Top Header Bar -->
                <header class="h-14 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden text-muted-foreground hover:text-foreground p-1.5 rounded-md border border-border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>

                        <!-- Breadcrumbs -->
                        <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                            <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Monitoring</a>
                            <span>/</span>
                            <span class="text-foreground font-medium">Fleet Overview</span>
                        </div>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-3">
                        <x-spectora.push-alerts-badge />

                        <div class="w-7 h-7 rounded-md bg-secondary flex items-center justify-center font-bold text-xs text-foreground border border-border">
                            {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
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
