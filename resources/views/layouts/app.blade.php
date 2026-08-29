<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spectora') }} - Agency Monitoring</title>

        <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- PWA / Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0B1437">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0B1437] text-white min-h-screen selection:bg-[#7551FF] selection:text-white" x-data="{ mobileSidebarOpen: false }">
        
        <div class="flex min-h-screen bg-[#0B1437]">
            
            <!-- 1. Left Horizon Sidebar (Desktop) -->
            @include('layouts.navigation')

            <!-- Mobile Sidebar Drawer -->
            <div x-show="mobileSidebarOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 lg:hidden flex" 
                 role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
                <div class="relative flex flex-col w-72 bg-[#111C44] border-r border-[#1B254B] p-5 z-50">
                    <div class="flex items-center justify-between pb-4 border-b border-[#1B254B] mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#4318FF] to-[#7551FF] flex items-center justify-center font-black text-xs">S</div>
                            <span class="text-base font-bold text-white">SPECTORA PRO</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="text-[#A3AED0] hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <nav class="space-y-1.5">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-horizon-sm text-xs font-bold bg-[#1B254B] text-white">
                            Dashboard
                        </a>
                        <a href="{{ route('settings.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-horizon-sm text-xs font-bold text-[#A3AED0] hover:text-white">
                            Einstellungen
                        </a>
                    </nav>
                </div>
            </div>

            <!-- 2. Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                
                <!-- Floating Glassmorphic Horizon Navbar -->
                <header class="sticky top-4 mx-4 sm:mx-6 lg:mx-8 z-30 backdrop-blur-xl bg-[#111C44]/80 border border-[#1B254B]/80 rounded-horizon px-5 py-3 shadow-horizon flex items-center justify-between">
                    
                    <!-- Left: Breadcrumb & Title -->
                    <div class="flex items-center gap-3">
                        <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden text-[#A3AED0] hover:text-white p-1.5 rounded-full bg-[#1B254B] border border-[#2B3674]/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>

                        <div>
                            <p class="text-[11px] font-semibold text-[#A3AED0]">Pages / Dashboard</p>
                            <h1 class="text-base sm:text-lg font-bold text-white tracking-tight">Main Dashboard</h1>
                        </div>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-3">
                        <x-spectora.push-alerts-badge />

                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#7551FF] to-[#01B574] flex items-center justify-center font-bold text-xs text-white shadow-sm shrink-0">
                            {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6">
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
