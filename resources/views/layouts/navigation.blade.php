<nav x-data="{ open: false }" class="bg-[#0D1424]/90 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-500 via-teal-400 to-emerald-400 flex items-center justify-center shadow-lg shadow-cyan-500/25 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-[#070B12]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-black text-white tracking-tight">
                            Spectora<span class="text-cyan-400">.</span>
                        </span>
                    </a>
                </div>

                <!-- Engine Status Pill in Navbar -->
                <div class="hidden md:flex items-center gap-2 ms-8 px-3 py-1 rounded-full bg-slate-900/80 border border-slate-800 text-[11px] font-mono text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-emerald-400 font-bold">ENGINE ACTIVE</span>
                    <span class="text-slate-600">|</span>
                    <span>15m Probes</span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-semibold transition-colors border-b-2 {{ request()->routeIs('dashboard') ? 'border-cyan-400 text-cyan-400' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('settings.edit') }}" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-semibold transition-colors border-b-2 {{ request()->routeIs('settings.edit') ? 'border-cyan-400 text-cyan-400' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
                        {{ __('Settings') }}
                    </a>
                </div>
            </div>

            <!-- Right Controls: Profile Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-slate-700/80 rounded-xl text-xs font-semibold text-slate-200 bg-[#131B2E] hover:border-slate-600 hover:text-white focus:outline-none transition-all shadow-md">
                            <div class="w-6 h-6 rounded-lg bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-[11px] font-bold text-[#070B12]">
                                {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                            </div>
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('settings.edit')">
                            {{ __('Settings & Agency') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#0B0F17] border-b border-slate-800 px-4 pt-2 pb-4">
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-cyan-500/10 text-cyan-400 font-bold' : 'text-slate-300' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('settings.edit') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('settings.edit') ? 'bg-cyan-500/10 text-cyan-400 font-bold' : 'text-slate-300' }}">
                {{ __('Settings') }}
            </a>
        </div>

        <div class="pt-4 pb-1 border-t border-slate-800 mt-2">
            <div class="px-3 mb-2">
                <div class="font-bold text-sm text-white">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold text-rose-400 hover:bg-rose-500/10 transition-colors">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</nav>
