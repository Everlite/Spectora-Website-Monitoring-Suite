<nav x-data="{ open: false }" class="bg-[#090D16] border-b border-[#1E293B] sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex items-center">
                <!-- Brand Logo -->
                <div class="shrink-0 flex items-center gap-2.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black text-xs shadow-sm">
                            S
                        </div>
                        <span class="text-sm font-bold text-white tracking-tight">
                            Spectora
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#151F33] text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('settings.edit') }}" 
                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ request()->routeIs('settings.edit') ? 'bg-[#151F33] text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                        {{ __('Einstellungen') }}
                    </a>
                </div>
            </div>

            <!-- Right Controls: Profile Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-2.5 py-1.5 border border-[#1E293B] rounded-lg text-xs font-medium text-slate-300 bg-[#0F1626] hover:bg-[#151F33] hover:text-white transition-colors">
                            <div class="w-5 h-5 rounded bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-200">
                                {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                            </div>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('settings.edit')">
                            {{ __('Einstellungen & Agentur') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Abmelden') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#0F1626] border-b border-[#1E293B] px-4 pt-2 pb-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-xs font-medium {{ request()->routeIs('dashboard') ? 'bg-[#151F33] text-white font-bold' : 'text-slate-300' }}">
            {{ __('Dashboard') }}
        </a>
        <a href="{{ route('settings.edit') }}" class="block px-3 py-2 rounded-md text-xs font-medium {{ request()->routeIs('settings.edit') ? 'bg-[#151F33] text-white font-bold' : 'text-slate-300' }}">
            {{ __('Einstellungen') }}
        </a>
        <div class="pt-3 border-t border-[#1E293B] mt-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-1.5 rounded text-xs font-semibold text-rose-400 hover:bg-rose-950/40 transition-colors">
                    {{ __('Abmelden') }}
                </button>
            </form>
        </div>
    </div>
</nav>
