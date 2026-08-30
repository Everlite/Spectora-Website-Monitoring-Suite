@php
    $navDomain = request()->route('domain');
    $navDomain = $navDomain instanceof \App\Models\Domain ? $navDomain : null;
    $host = $navDomain ? (parse_url($navDomain->url, PHP_URL_HOST) ?: $navDomain->url) : null;
@endphp

<aside class="hidden lg:flex flex-col w-60 bg-white border-r border-studio-border shrink-0 min-h-screen">
    <div class="h-14 flex items-center px-4 border-b border-studio-border">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0">
            <x-application-logo class="w-7 h-7 shrink-0" />
            <span class="text-[17px] font-medium text-studio-text">Spectora</span>
        </a>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <p class="px-3 pb-1 text-[11px] font-medium uppercase tracking-wide text-studio-subtle">Berichte</p>
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-studio-sm {{ request()->routeIs('dashboard') && ! $navDomain ? 'bg-studio-hover text-studio-brand font-medium' : 'text-studio-muted hover:bg-studio-elevated' }}">
            Start
        </a>
        @if($navDomain)
            <a href="{{ route('domains.show', $navDomain) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-studio-sm bg-studio-hover text-studio-brand font-medium truncate">
                {{ $host }}
            </a>
        @endif

        <p class="px-3 pt-4 pb-1 text-[11px] font-medium uppercase tracking-wide text-studio-subtle">Verwaltung</p>
        <a href="{{ route('settings.edit') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-studio-sm {{ request()->routeIs('settings.*') ? 'bg-studio-hover text-studio-brand font-medium' : 'text-studio-muted hover:bg-studio-elevated' }}">
            Einstellungen
        </a>
    </nav>

    <div class="p-3 border-t border-studio-border">
        <div class="flex items-center justify-between px-2">
            <div class="min-w-0">
                <div class="text-xs font-medium truncate">{{ Auth::user()->name }}</div>
                <div class="text-[11px] text-studio-muted truncate">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-[11px] text-studio-muted hover:text-studio-rose">Abmelden</button>
            </form>
        </div>
    </div>
</aside>
