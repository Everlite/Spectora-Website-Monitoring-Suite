@props(['domain'])

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 py-2" x-data="headerActions()">
    <!-- Domain Info -->
    <div class="flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-full bg-[#1B254B] border border-[#2B3674] flex items-center justify-center text-[#7551FF] shadow-sm shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
        </div>
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="font-extrabold text-xl text-white tracking-tight">
                    {{ $domain->url }}
                </h2>
                @if($domain->status_code >= 200 && $domain->status_code < 400)
                    <span class="badge-horizon-success">
                        ● Online ({{ $domain->status_code }})
                    </span>
                @else
                    <span class="badge-horizon-danger">
                        ● Offline
                    </span>
                @endif
                @if(isset($domain->pagespeed_score_desktop) && $domain->pagespeed_score_desktop > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-mono font-extrabold bg-[#7551FF]/20 text-[#7551FF] border border-[#7551FF]/40">
                        Grade {{ $domain->grade }} ({{ $domain->pagespeed_score_desktop }}/100)
                    </span>
                @endif
            </div>
            <p class="text-xs text-[#A3AED0] mt-0.5">Letzter Check: {{ $domain->last_checked ? $domain->last_checked->diffForHumans() : 'nie' }}</p>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex flex-wrap items-center gap-2.5">
        <!-- Analyse Button -->
        <button 
            type="button"
            @click="runAnalysis()"
            :disabled="isAnalyzing"
            class="btn-horizon-primary"
        >
            <template x-if="isAnalyzing">
                <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <template x-if="!isAnalyzing">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </template>
            <span x-text="isAnalyzing ? 'Prüfung läuft...' : 'Jetzt prüfen'"></span>
        </button>
        
        <!-- Open Site -->
        <a href="{{ $domain->url }}" target="_blank" rel="noopener noreferrer"
           class="btn-horizon-secondary">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            <span>Öffnen</span>
        </a>

        <!-- Tracking Code -->
        <button type="button" @click="showTrackingModal = true"
                class="btn-horizon-secondary">
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
                 class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md" 
                 @click="showTrackingModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showTrackingModal" 
                 class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-6 space-y-4">
                <div class="flex items-start justify-between pb-4 border-b border-[#1B254B]">
                    <div>
                        <h3 class="text-base font-bold text-white">Pulse Tracking Code</h3>
                        <p class="text-xs text-[#A3AED0] font-mono mt-0.5">{{ $domain->url }}</p>
                    </div>
                    <button type="button" @click="showTrackingModal = false" class="text-[#A3AED0] hover:text-white p-1 rounded-full hover:bg-[#1B254B]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="space-y-2">
                    <p class="text-xs text-[#A3AED0]">Füge diesen Code in den <code class="bg-[#0B1437] px-2 py-0.5 rounded-full text-white font-mono border border-[#1B254B]">&lt;head&gt;</code> ein:</p>
                    <pre class="bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm p-4 text-xs font-mono text-[#01B574] select-all overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;script defer src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" data-domain="{{ $domain->uuid }}"&gt;&lt;/script&gt;</pre>
                </div>
                <div class="flex justify-end pt-4 border-t border-[#1B254B]">
                    <button type="button" @click="showTrackingModal = false" class="btn-horizon-secondary">Schließen</button>
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
