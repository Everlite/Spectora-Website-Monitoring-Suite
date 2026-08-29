<!-- Horizon UI Signature Dark Sidebar -->
<aside class="hidden lg:flex flex-col w-64 bg-[#111C44] border-r border-[#1B254B] shrink-0 min-h-screen z-40">
    
    <!-- 1. Horizon Brand Header -->
    <div class="h-20 flex items-center px-6 border-b border-[#1B254B]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#4318FF] to-[#7551FF] flex items-center justify-center text-white font-black text-sm shadow-horizon-btn shrink-0">
                S
            </div>
            <div>
                <div class="text-base font-extrabold text-white tracking-wider flex items-center gap-1.5">
                    <span>SPECTORA</span>
                    <span class="text-[#7551FF] font-light">PRO</span>
                </div>
                <div class="text-[10px] font-semibold text-[#A3AED0] tracking-tight">AGENCY MONITORING</div>
            </div>
        </a>
    </div>

    <!-- 2. Navigation Items -->
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
        
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-[#A3AED0] uppercase tracking-wider">
                Hauptmenü
            </div>
            <nav class="space-y-1.5">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="relative flex items-center gap-3 px-3.5 py-3 rounded-horizon-sm text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#1B254B] text-white shadow-sm' : 'text-[#A3AED0] hover:text-white hover:bg-[#1B254B]/50' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-[#7551FF]' : 'text-[#A3AED0]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                    @if(request()->routeIs('dashboard'))
                        <span class="absolute right-0 top-2 bottom-2 w-1.5 bg-[#7551FF] rounded-l-full"></span>
                    @endif
                </a>

                <!-- Settings -->
                <a href="{{ route('settings.edit') }}" 
                   class="relative flex items-center gap-3 px-3.5 py-3 rounded-horizon-sm text-xs font-bold transition-all {{ request()->routeIs('settings.edit') ? 'bg-[#1B254B] text-white shadow-sm' : 'text-[#A3AED0] hover:text-white hover:bg-[#1B254B]/50' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('settings.edit') ? 'text-[#7551FF]' : 'text-[#A3AED0]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Einstellungen & Profil</span>
                    @if(request()->routeIs('settings.edit'))
                        <span class="absolute right-0 top-2 bottom-2 w-1.5 bg-[#7551FF] rounded-l-full"></span>
                    @endif
                </a>
            </nav>
        </div>

        <!-- 3. Horizon Engine Promo Box -->
        <div class="bg-gradient-to-br from-[#7551FF] to-[#4318FF] rounded-horizon p-4 text-white shadow-horizon-btn relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full bg-white/10 blur-xl"></div>
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div class="text-xs font-bold mb-1">Spectora Engine</div>
            <p class="text-[11px] text-white/80 leading-relaxed mb-3">24/7 Autonome Probes, SSL-Wächter & Telemetrie aktiv.</p>
            <div class="text-[10px] font-mono bg-black/20 rounded-full px-2.5 py-1 inline-block">v2.0 Production Ready</div>
        </div>

    </div>

    <!-- 4. User Footer Profile -->
    <div class="p-4 border-t border-[#1B254B] mt-auto">
        <div class="flex items-center justify-between p-2.5 rounded-horizon-sm bg-[#0B1437] border border-[#1B254B]">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#7551FF] to-[#01B574] flex items-center justify-center font-bold text-xs text-white shrink-0 shadow-sm">
                    {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-[#A3AED0] truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-[#A3AED0] hover:text-[#EE5D50] p-1.5 rounded-full hover:bg-[#111C44] transition-colors" title="Abmelden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>

</aside>
