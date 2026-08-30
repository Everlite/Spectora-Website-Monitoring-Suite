@php
    $navDomain = request()->route('domain');
    $navDomain = $navDomain instanceof \App\Models\Domain ? $navDomain : null;
@endphp

<header class="border-b border-studio-border">
    <div class="max-w-[1360px] mx-auto px-5 sm:px-8 h-[72px] flex items-center justify-between gap-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
            <x-application-logo class="w-8 h-8 shrink-0" />
            <span class="sp-display text-[28px] text-studio-text leading-none tracking-tight">Spectora</span>
        </a>

        <nav class="hidden lg:flex items-center gap-8 text-sm">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') && ! $navDomain ? 'text-studio-text' : 'text-studio-muted hover:text-studio-text' }}">
                Websites
            </a>
            @if($navDomain)
                <span class="text-studio-subtle">/</span>
                <span class="text-studio-text truncate max-w-[20rem]">{{ parse_url($navDomain->url, PHP_URL_HOST) ?: $navDomain->url }}</span>
            @endif
            <a href="{{ route('settings.edit') }}"
               class="{{ request()->routeIs('settings.*') ? 'text-studio-text' : 'text-studio-muted hover:text-studio-text' }}">
                Einstellungen
            </a>
        </nav>

        <div class="flex items-center gap-4">
            <x-spectora.push-alerts-badge />
            <div class="hidden sm:block text-right leading-tight">
                <div class="text-xs text-studio-text">{{ Auth::user()->name }}</div>
                <div class="text-[11px] text-studio-muted truncate max-w-[10rem]">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                @csrf
                <button type="submit" class="text-xs text-studio-muted hover:text-studio-rose">Abmelden</button>
            </form>
            <button type="button" @click="mobileNavOpen = !mobileNavOpen" class="lg:hidden text-studio-muted border border-studio-border p-2" aria-label="Menü">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16"></path></svg>
            </button>
        </div>
    </div>
</header>
