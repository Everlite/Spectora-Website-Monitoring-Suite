<x-app-layout>
    <div x-data="{ showSecurityModal: false }" class="space-y-8">

        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs text-studio-muted hover:text-white transition-colors font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Zurück zur Flotte</span>
            </a>
        </div>

        <x-dashboard.header :domain="$domain" :all-domains="$allDomains ?? []" />

        @include('domains.dashboard.partials.flash-and-tabs')

        @include('domains.dashboard.partials.tab-overview')
        @include('domains.dashboard.partials.tab-analytics')
        @include('domains.dashboard.partials.tab-history')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @include('domains.dashboard.partials.tab-notes')
            @include('domains.dashboard.partials.tab-monitoring')
        </div>

        @include('domains.dashboard.partials.security-modal')
    </div>

    @include('domains.dashboard.partials.scripts')
</x-app-layout>
