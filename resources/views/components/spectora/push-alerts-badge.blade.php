<div id="notification-ui" class="flex items-center">
    <!-- State: Enable Button -->
    <button id="enable-notifications-btn" onclick="enableNotifications()" style="display: none;"
        class="px-3.5 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-[#070B12] text-xs font-bold rounded-xl transition shadow-lg shadow-cyan-500/20 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        Enable Push Alerts
    </button>

    <!-- State: Active Badge -->
    <span id="notifications-active-badge" style="display: none;"
        class="px-3.5 py-2 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold rounded-xl flex items-center gap-2 cursor-default">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        Push Alerts Active
    </span>

    <!-- State: Blocked Badge -->
    <span id="notifications-blocked-badge" style="display: none;"
        class="px-3.5 py-2 bg-rose-500/10 text-rose-400 border border-rose-500/30 text-xs font-bold rounded-xl flex items-center gap-2 cursor-help"
        title="Web Push is blocked in browser settings.">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
        </svg>
        Alerts Blocked
    </span>
</div>
