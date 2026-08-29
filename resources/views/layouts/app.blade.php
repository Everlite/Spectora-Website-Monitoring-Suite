<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spectora') }} - Agency Monitoring Suite</title>

        <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- PWA / Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#090B10">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#090B10] text-[#F1F3F9] min-h-screen selection:bg-[#3B57E8] selection:text-white" x-data="{ mobileSidebarOpen: false }">
        
        <div class="flex min-h-screen bg-[#090B10]">
            
            <!-- 1. Left Sidebar Navigation (Desktop) -->
            @include('layouts.navigation')

            <!-- Mobile Sidebar Drawer -->
            <div x-show="mobileSidebarOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 lg:hidden flex" 
                 role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
                <div class="relative flex flex-col w-72 bg-[#0D111A] border-r border-[#202A3E] p-4 z-50">
                    <div class="flex items-center justify-between pb-4 border-b border-[#202A3E] mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-studio-sm bg-[#3B57E8] text-white flex items-center justify-center font-bold text-xs">SP</div>
                            <span class="text-sm font-bold text-white">SPECTORA PRO</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="text-[#8A95A8] hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold bg-[#171E2E] text-white">
                            Flotten-Übersicht
                        </a>
                        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold text-[#8A95A8] hover:text-white">
                            Einstellungen
                        </a>
                    </nav>
                </div>
            </div>

            <!-- 2. Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                
                <!-- Studio Top Bar -->
                <header class="h-14 border-b border-[#202A3E] bg-[#0D111A]/90 backdrop-blur sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden text-[#8A95A8] hover:text-white p-1.5 rounded-studio-sm border border-[#202A3E]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>

                        <div class="flex items-center gap-2 text-xs">
                            <a href="{{ route('dashboard') }}" class="text-[#8A95A8] hover:text-white font-medium transition-colors">Monitoring</a>
                            <span class="text-[#5A667A]">/</span>
                            <span class="text-white font-bold">Flotten-Übersicht</span>
                        </div>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-3">
                        <x-spectora.push-alerts-badge />

                        <div class="w-7 h-7 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center font-bold text-xs text-[#3B57E8]">
                            {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </header>

                <!-- Page Viewport -->
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
