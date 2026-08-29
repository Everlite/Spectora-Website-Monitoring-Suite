@props(['domain'])

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 py-2" x-data="headerActions()">
    <!-- Domain Info -->
    <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-lg bg-card border border-border flex items-center justify-center text-foreground shadow-sm shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
        </div>
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h2 class="font-bold text-lg text-foreground tracking-tight">
                    {{ $domain->url }}
                </h2>
                @if($domain->status_code >= 200 && $domain->status_code < 400)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-950/40 text-emerald-400 border border-emerald-800/40">
                        ● Online ({{ $domain->status_code }})
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-950/40 text-rose-400 border border-rose-800/40">
                        ● Offline
                    </span>
                @endif
                @if(isset($domain->pagespeed_score_desktop) && $domain->pagespeed_score_desktop > 0)
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-secondary text-foreground border border-border">
                        Grade {{ $domain->grade }} ({{ $domain->pagespeed_score_desktop }}/100)
                    </span>
                @endif
            </div>
            <p class="text-xs text-muted-foreground mt-0.5">Letzter Check: {{ $domain->last_checked ? $domain->last_checked->diffForHumans() : 'nie' }}</p>
        </div>
    </div>
    
    <!-- Action Buttons (shadcn style) -->
    <div class="flex flex-wrap items-center gap-2">
        <!-- Analyse Button (Primary) -->
        <button 
            type="button"
            @click="runAnalysis()"
            :disabled="isAnalyzing"
            class="btn-default"
        >
            <template x-if="isAnalyzing">
                <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <template x-if="!isAnalyzing">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </template>
            <span x-text="isAnalyzing ? 'Prüfung läuft...' : 'Jetzt prüfen'"></span>
        </button>
        
        <!-- Visit Website -->
        <a href="{{ $domain->url }}" target="_blank" rel="noopener noreferrer"
           class="btn-outline">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            <span>Öffnen</span>
        </a>

        <!-- Tracking Code Modal Trigger -->
        <button type="button" @click="showTrackingModal = true"
                class="btn-secondary">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            <span>Tracking Code</span>
        </button>
    </div>

    <!-- Tracking Modal -->
    <div x-show="showTrackingModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showTrackingModal" 
                 class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
                 @click="showTrackingModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showTrackingModal" 
                 class="inline-block align-bottom bg-card border border-border rounded-xl text-left overflow-hidden shadow-shadcn-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-6 space-y-4">
                <div class="flex items-start justify-between pb-3 border-b border-border">
                    <div>
                        <h3 class="text-base font-semibold text-foreground">Pulse Tracking Code</h3>
                        <p class="text-xs text-muted-foreground">{{ $domain->url }}</p>
                    </div>
                    <button type="button" @click="showTrackingModal = false" class="text-muted-foreground hover:text-foreground">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="space-y-2">
                    <p class="text-xs text-muted-foreground">Füge diesen Code in den <code class="bg-muted px-1.5 py-0.5 rounded text-foreground font-mono">&lt;head&gt;</code> ein:</p>
                    <pre class="bg-zinc-950 border border-border rounded-lg p-3 text-xs font-mono text-emerald-400 select-all overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;script defer src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" data-domain="{{ $domain->uuid }}"&gt;&lt;/script&gt;</pre>
                </div>
                <div class="flex justify-end pt-3 border-t border-border">
                    <button type="button" @click="showTrackingModal = false" class="btn-secondary">Schließen</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function headerActions() {
        return {
            isAnalyzing: false,
            showTrackingModal: false,

            async runAnalysis() {
                this.isAnalyzing = true;
                try {
                    const response = await fetch('{{ route('domains.analyze', $domain) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Prüfung fehlgeschlagen. Bitte versuche es erneut.');
                    }
                } catch (e) {
                    console.error('Analysis failed:', e);
                } finally {
                    this.isAnalyzing = false;
                }
            }
        };
    }
</script>
