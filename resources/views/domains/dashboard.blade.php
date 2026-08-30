<x-app-layout>
    <div class="space-y-12">

        <x-dashboard.header :domain="$domain" :all-domains="$allDomains ?? []" />

        @include('domains.dashboard.partials.flash-and-tabs')

        @include('domains.dashboard.partials.tab-overview')
        @include('domains.dashboard.partials.tab-history')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @include('domains.dashboard.partials.tab-notes')
            @include('domains.dashboard.partials.tab-monitoring')
        </div>

    </div>

    @include('domains.dashboard.partials.scripts')
</x-app-layout>
