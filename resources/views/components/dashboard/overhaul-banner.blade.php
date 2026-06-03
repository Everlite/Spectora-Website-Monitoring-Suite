{{-- Dashboard UI overhaul (May 2026) — report issues before production reliance --}}
<div
    class="mb-6 rounded-xl border-2 border-amber-400/80 bg-amber-50 dark:bg-amber-500/10 dark:border-amber-500/50 px-4 py-4 sm:px-5 shadow-sm"
    role="status"
    aria-live="polite"
>
    <div class="flex gap-3 sm:gap-4">
        <div class="flex-shrink-0 mt-0.5">
            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-bold text-amber-900 dark:text-amber-200 text-sm sm:text-base">
                Großes Dashboard-Overhaul — noch nicht vollständig getestet
            </p>
            <p class="mt-1 text-sm text-amber-800/90 dark:text-amber-100/80 leading-relaxed">
                Diese Domain-Ansicht wurde strukturell neu aufgebaut (Tabs, Komponenten, Layout).
                Bitte alle Bereiche (Overview, Analytics, History, Notes, Monitoring) am Wochenende selbst durchklicken,
                bevor du sie Kunden zeigst oder für Alerts verlässt.
            </p>
            <p class="mt-2 text-xs text-amber-700/80 dark:text-amber-200/60 font-mono">
                Spectora · dashboard refactor · {{ now()->format('Y-m-d') }}
            </p>
        </div>
    </div>
</div>
