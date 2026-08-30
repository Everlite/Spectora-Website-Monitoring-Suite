<aside class="hidden lg:flex flex-col w-64 bg-studio-bg border-r border-studio-border shrink-0 min-h-screen z-40">

    <div class="h-16 flex items-center px-5 border-b border-studio-border">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0">
            <x-application-logo class="w-8 h-8 shrink-0" />
            <div class="min-w-0">
                <div class="text-sm font-extrabold text-white tracking-wide">Spectora</div>
                <div class="text-[10px] text-studio-muted font-medium">Website-Monitoring</div>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto px-3.5 py-5 space-y-6">
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-studio-elevated text-white border border-studio-border' : 'text-studio-muted hover:text-white hover:bg-studio-surface' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-studio-brand' : 'text-studio-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Flotte</span>
            </a>

            @if(!empty($navDomain))
                <a href="{{ route('domains.show', $navDomain) }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold bg-studio-elevated text-white border border-studio-border">
                    <svg class="w-4 h-4 text-studio-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    <span class="truncate">{{ $navDomain->url }}</span>
                </a>
            @endif

            <a href="{{ route('settings.edit') }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-studio-sm text-xs font-bold transition-all {{ request()->routeIs('settings.edit') ? 'bg-studio-elevated text-white border border-studio-border' : 'text-studio-muted hover:text-white hover:bg-studio-surface' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('settings.edit') ? 'text-studio-brand' : 'text-studio-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Einstellungen</span>
            </a>
        </nav>
    </div>

    <div class="p-3 border-t border-studio-border mt-auto">
        <div class="flex items-center justify-between p-2 rounded-studio-sm bg-studio-surface border border-studio-border">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-studio-sm bg-studio-elevated border border-studio-border flex items-center justify-center font-bold text-xs text-studio-brand shrink-0">
                    {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-studio-muted truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-studio-muted hover:text-studio-rose p-1.5 rounded-studio-sm hover:bg-studio-elevated transition-colors" title="Abmelden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>

</aside>
