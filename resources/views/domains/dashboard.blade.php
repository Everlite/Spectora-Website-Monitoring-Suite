<x-app-layout>
    @include('domains.dashboard.partials.styles')

    <div class="space-y-6" x-data="dashboardData()">
        
        <!-- Domain Action Header (Horizon UI) -->
        <x-dashboard.header :domain="$domain" />

        <!-- Segmented Tab Navigation -->
        @include('domains.dashboard.partials.flash-and-tabs')

        <!-- Tab 1: Overview -->
        @include('domains.dashboard.partials.tab-overview')

        <!-- Tab 2: Analytics -->
        @include('domains.dashboard.partials.tab-analytics')

        <!-- Tab 3: History & Analysis -->
        @include('domains.dashboard.partials.tab-history')

        <!-- Tab 4: Team Notes -->
        @include('domains.dashboard.partials.tab-notes')

        <!-- Tab 5: Monitoring & Subpages -->
        @include('domains.dashboard.partials.tab-monitoring')

        <!-- Security Modal -->
        @include('domains.dashboard.partials.security-modal')

    </div>

    @include('domains.dashboard.partials.scripts')
</x-app-layout>
