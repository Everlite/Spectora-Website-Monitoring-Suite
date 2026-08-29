<!-- shadcn/ui Sidebar (Kiranism Next.js Dashboard Style) -->
<aside class="hidden lg:flex flex-col w-64 border-r border-border bg-sidebar shrink-0 min-h-screen">
    
    <!-- 1. Workspace / Agency Switcher Header -->
    <div class="h-14 border-b border-border flex items-center px-4 justify-between">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-7 h-7 rounded-lg bg-primary text-primary-foreground flex items-center justify-center font-black text-xs shrink-0 shadow-sm">
                S
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-foreground truncate flex items-center gap-1.5">
                    <span>Spectora Suite</span>
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-zinc-800 text-zinc-400 border border-zinc-700">v2.0</span>
                </div>
                <div class="text-[10px] text-muted-foreground truncate">Agency Fleet Monitoring</div>
            </div>
        </div>
    </div>

    <!-- 2. Navigation Sections -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        
        <!-- Section: Overview -->
        <div>
            <div class="px-2 mb-1.5 text-[10px] font-semibold text-muted-foreground uppercase tracking-wider">
                Übersicht
            </div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-secondary text-foreground font-semibold shadow-sm' : 'text-muted-foreground hover:text-foreground hover:bg-accent/50' }}">
                    <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
            </nav>
        </div>

        <!-- Section: Fleet Monitoring -->
        <div>
            <div class="px-2 mb-1.5 text-[10px] font-semibold text-muted-foreground uppercase tracking-wider">
                Monitoring & Engine
            </div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs font-medium text-muted-foreground hover:text-foreground hover:bg-accent/50 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        <span>Fleet Targets</span>
                    </div>
                </a>

                <a href="{{ route('settings.edit') }}#webhooks" 
                   class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs font-medium text-muted-foreground hover:text-foreground hover:bg-accent/50 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span>Alerts & Webhooks</span>
                    </div>
                </a>
            </nav>
        </div>

        <!-- Section: Administration -->
        <div>
            <div class="px-2 mb-1.5 text-[10px] font-semibold text-muted-foreground uppercase tracking-wider">
                Verwaltung
            </div>
            <nav class="space-y-1">
                <a href="{{ route('settings.edit') }}" 
                   class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('settings.edit') ? 'bg-secondary text-foreground font-semibold shadow-sm' : 'text-muted-foreground hover:text-foreground hover:bg-accent/50' }}">
                    <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Einstellungen & Profil</span>
                </a>
            </nav>
        </div>

    </div>

    <!-- 3. User Footer Profile Card -->
    <div class="p-3 border-t border-border mt-auto">
        <div class="flex items-center justify-between p-2 rounded-lg bg-zinc-900/50 border border-border">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-7 h-7 rounded-md bg-zinc-800 flex items-center justify-center font-bold text-xs text-foreground shrink-0 border border-border">
                    {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-medium text-foreground truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-muted-foreground truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-muted-foreground hover:text-destructive p-1 rounded transition-colors" title="Abmelden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>

</aside>
