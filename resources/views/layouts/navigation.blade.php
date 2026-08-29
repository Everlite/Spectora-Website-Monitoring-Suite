<!-- Spectora Studio Left Navigation -->
<aside class="hidden lg:flex flex-col w-64 bg-[#0D111A] border-r border-[#202A3E] shrink-0 min-h-screen z-40">
    
    <!-- 1. Studio Brand Header -->
    <div class="h-16 flex items-center justify-between px-5 border-b border-[#202A3E]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-studio-sm bg-[#3B57E8] flex items-center justify-center text-white font-extrabold text-xs shadow-studio-btn shrink-0">
                SP
            </div>
            <div>
                <div class="text-sm font-extrabold text-white tracking-wider flex items-center gap-1.5">
                    <span>SPECTORA</span>
                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-[#3B57E8]/20 text-[#4F6BFF] border border-[#3B57E8]/40 font-mono font-bold">PRO</span>
                </div>
                <div class="text-[10px] text-[#8A95A8] font-medium tracking-tight">Agency Monitoring</div>
            </div>
        </a>
    </div>

    <!-- 2. Navigation Menu -->
    <div class="flex-1 overflow-y-auto px-3.5 py-5 space-y-6">
        
        <div>
            <div class="px-2.5 mb-2 text-[10px] font-bold text-[#5A667A] uppercase tracking-wider">
                Monitoring Core
            </div>
            <nav class="space-y-1">
                <!-- Dashboard / Targets -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#171E2E] text-white border border-[#202A3E]' : 'text-[#8A95A8] hover:text-white hover:bg-[#111622]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-[#3B57E8]' : 'text-[#8A95A8]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Flotten-Übersicht</span>
                </a>

                <!-- Settings -->
                <a href="{{ route('settings.edit') }}" 
                   class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold transition-all {{ request()->routeIs('settings.edit') ? 'bg-[#171E2E] text-white border border-[#202A3E]' : 'text-[#8A95A8] hover:text-white hover:bg-[#111622]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('settings.edit') ? 'text-[#3B57E8]' : 'text-[#8A95A8]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Einstellungen & Branding</span>
                </a>
            </nav>
        </div>

        <!-- 3. Engine Live Node Widget -->
        <div class="bg-[#111622] rounded-studio-sm border border-[#202A3E] p-3.5 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-[#8A95A8] uppercase tracking-wider">Engine Node</span>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#10B981]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] animate-pulse"></span>
                    Aktiv
                </span>
            </div>
            <div class="text-[11px] font-mono text-[#F1F3F9] font-medium">
                Spectora Core v2.4
            </div>
            <div class="text-[10px] text-[#5A667A] leading-tight">
                24/7 Watchdog & Uptime Engine
            </div>
        </div>

    </div>

    <!-- 4. User Footer Profile -->
    <div class="p-3 border-t border-[#202A3E] mt-auto">
        <div class="flex items-center justify-between p-2 rounded-studio-sm bg-[#111622] border border-[#202A3E]">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center font-bold text-xs text-[#3B57E8] shrink-0">
                    {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-[#8A95A8] truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-[#8A95A8] hover:text-[#F43F5E] p-1.5 rounded-studio-sm hover:bg-[#171E2E] transition-colors" title="Abmelden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>

</aside>
