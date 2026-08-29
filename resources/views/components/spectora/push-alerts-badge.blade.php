<div id="notification-ui" class="flex items-center">
    <!-- State: Enable Button -->
    <button id="enable-notifications-btn" onclick="enableNotifications()" style="display: none;"
        class="btn-horizon-secondary text-xs py-2 px-4 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 text-[#7551FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        <span>Push-Alerts</span>
    </button>

    <!-- State: Active Badge -->
    <span id="notifications-active-badge" style="display: none;"
        class="badge-horizon-success text-[11px] py-1 px-3 cursor-default">
        ● Alerts aktiv
    </span>

    <!-- State: Blocked Badge -->
    <span id="notifications-blocked-badge" style="display: none;"
        class="badge-horizon-danger text-[11px] py-1 px-3 cursor-help"
        title="Web Push ist im Browser blockiert.">
        ● Alerts blockiert
    </span>
</div>
