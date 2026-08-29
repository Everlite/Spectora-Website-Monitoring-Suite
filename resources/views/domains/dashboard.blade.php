<x-app-layout>
    <div x-data="{ tab: 'overview', showSecurityModal: false }" class="space-y-6">
        
        <!-- Back Navigation Link -->
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs text-[#8A95A8] hover:text-white transition-colors font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Zurück zur Flotten-Übersicht</span>
            </a>
        </div>

        <!-- 1. Domain Action Header -->
        <x-dashboard.header :domain="$domain" />

        <!-- 2. Flash Messages & Tabs Controller -->
        @include('domains.dashboard.partials.flash-and-tabs')

        <!-- 3. Tab Contents -->
        @include('domains.dashboard.partials.tab-overview')
        @include('domains.dashboard.partials.tab-analytics')
        @include('domains.dashboard.partials.tab-history')
        @include('domains.dashboard.partials.tab-notes')
        @include('domains.dashboard.partials.tab-monitoring')

        <!-- 4. Security Modal -->
        @include('domains.dashboard.partials.security-modal')

    </div>

    <!-- 5. Dynamic Charts & Scripts -->
    @include('domains.dashboard.partials.scripts')
</x-app-layout>
