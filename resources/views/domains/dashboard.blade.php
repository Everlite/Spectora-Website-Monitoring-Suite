{{--
    Domain dashboard — composed from partials under resources/views/domains/dashboard/partials/
    and components under resources/views/components/dashboard/
--}}
<x-app-layout>
    <x-slot name="header">
        <x-dashboard.header :domain="$domain" />
    </x-slot>

    @include('domains.dashboard.partials.styles')

    <div class="py-10" x-data="dashboardData()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <x-dashboard.overhaul-banner />

            @include('domains.dashboard.partials.flash-and-tabs')

            @include('domains.dashboard.partials.tab-overview')

            @include('domains.dashboard.partials.security-modal')

            @include('domains.dashboard.partials.tab-monitoring')

            @include('domains.dashboard.partials.tab-analytics')

            @include('domains.dashboard.partials.tab-history')

            @include('domains.dashboard.partials.tab-notes')

        </div>
    </div>

    @include('domains.dashboard.partials.scripts')
</x-app-layout>
