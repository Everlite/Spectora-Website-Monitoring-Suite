@props(['domain', 'allDomains' => []])

<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 py-2" x-data="headerActions()">
    
    <!-- Left: Target Info & Quick Switcher -->
    <div class="flex items-center gap-3.5 min-w-0">
        <div class="min-w-0">
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 group">
                        <span class="text-xl font-medium text-studio-text truncate">{{ parse_url($domain->url, PHP_URL_HOST) ?: $domain->url }}</span>
                        <svg class="w-4 h-4 text-studio-muted group-hover:text-studio-text" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown List -->
                    <div x-show="open" x-cloak 
                         class="absolute left-0 mt-2 w-64 bg-studio-surface border border-studio-border rounded-studio shadow-studio-card p-1.5 z-50 space-y-1">
                        <div class="px-2 py-1 text-[10px] font-bold text-studio-muted uppercase tracking-wider">
                            Website wechseln
                        </div>
                        @foreach($allDomains as $d)
                            <a href="{{ route('domains.show', $d) }}" 
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-studio-sm text-xs {{ $d->id === $domain->id ? 'bg-studio-hover text-studio-brand font-medium' : 'text-studio-muted hover:bg-studio-elevated' }}">
                                <span class="truncate">{{ $d->url }}</span>
                                <span class="w-2 h-2 rounded-full {{ $d->status_code >= 200 && $d->status_code < 400 ? 'bg-studio-emerald' : 'bg-studio-rose' }}"></span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Status Badge -->
                @if($domain->status_code >= 200 && $domain->status_code < 400)
                    <span class="badge-status-online">
                        ● Online ({{ $domain->status_code }})
                    </span>
                @else
                    <span class="badge-status-offline">
                        ● {{ $domain->status_code ? 'HTTP '.$domain->status_code : 'Offline' }}
                    </span>
                @endif

                <!-- Grade Badge -->
                @if(isset($domain->pagespeed_score_desktop) && $domain->pagespeed_score_desktop > 0)
                    <span class="text-[11px] font-mono text-studio-muted">
                        Dokument-Score {{ $domain->pagespeed_score_desktop }}/100 · {{ $domain->grade }}
                    </span>
                @endif
            </div>

            <p class="text-xs text-studio-muted mt-0.5 flex items-center gap-2">
                <span>Letzter Check: {{ $domain->last_checked ? $domain->last_checked->diffForHumans() : 'nie' }}</span>
                <span class="text-studio-subtle">·</span>
                <a href="{{ $domain->url }}" target="_blank" rel="noopener noreferrer" class="text-studio-brand hover:underline inline-flex items-center gap-1 font-medium">
                    Website öffnen
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </p>
        </div>
    </div>
    
    <!-- Right: Operational Action Buttons -->
    <div class="flex flex-wrap items-center gap-2">
        <!-- 1-Click Live Deep Probe -->
        <button 
            type="button"
            @click="runAnalysis()"
            :disabled="isAnalyzing"
            class="btn-spectora-primary"
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
            <span x-text="isAnalyzing ? 'Prüfung läuft...' : 'Deep Probe starten'"></span>
        </button>

        <!-- Tracking Code Modal Trigger -->
        <button type="button" @click="showTrackingModal = true"
                class="btn-spectora-secondary">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            <span>Tracking Code</span>
        </button>

        <!-- PDF Client Report Download -->
        <a href="{{ route('domains.report', $domain) }}" target="_blank"
           class="btn-spectora-secondary" title="PDF Report generieren">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>PDF Export</span>
        </a>
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
                 class="inline-block align-bottom bg-studio-surface border border-studio-border rounded-studio text-left overflow-hidden shadow-studio-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-6 space-y-4">
                <div class="flex items-start justify-between pb-3 border-b border-studio-border">
                    <div>
                        <h3 class="text-sm font-medium text-studio-text">Pulse-Snippet</h3>
                        <p class="text-xs text-studio-muted font-mono mt-0.5">{{ $domain->url }}</p>
                    </div>
                    <button type="button" @click="showTrackingModal = false" class="text-studio-muted hover:text-studio-text">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="space-y-2">
                    <p class="text-xs text-studio-muted">Füge diesen Tag vor dem schließenden <code class="bg-studio-elevated px-1.5 py-0.5 rounded text-studio-text font-mono border border-studio-border">&lt;/head&gt;</code> Tag ein:</p>
                    <pre class="bg-studio-bg border border-studio-border rounded-studio-sm p-3 text-xs font-mono text-studio-emerald select-all overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;script defer src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" data-domain="{{ $domain->uuid }}"&gt;&lt;/script&gt;</pre>
                </div>
                <div class="flex justify-end pt-3 border-t border-studio-border">
                    <button type="button" @click="showTrackingModal = false" class="btn-spectora-secondary">Schließen</button>
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
