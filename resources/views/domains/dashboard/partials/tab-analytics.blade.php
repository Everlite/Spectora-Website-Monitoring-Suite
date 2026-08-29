<!-- Tab Content: Analytics -->
<div x-show="tab === 'analytics'" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="space-y-6">

    <!-- 1. Tracking Installation Box (Prominent & Clear) -->
    <div class="premium-card p-5 bg-[#0F1626] border border-[#1E293B]" x-data="{ copied: false, codeType: 'html' }">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-950/60 border border-emerald-800/50 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Spectora Pulse Tracking-Code</h3>
                    <p class="text-xs text-slate-400">Cookie-freie, DSGVO-orientierte Telemetrie (< 1 KB)</p>
                </div>
            </div>

            <!-- Copy Button -->
            <button type="button" 
                    @click="
                        navigator.clipboard.writeText(document.getElementById('pulse-tracking-script').textContent.trim());
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                    class="btn-premium-primary text-xs py-1.5 px-3">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                <span x-text="copied ? 'Kopiert!' : 'Code kopieren'"></span>
            </button>
        </div>

        <p class="text-xs text-slate-300 mb-3">
            Füge diesen Tag vor dem schließenden <code class="bg-[#070B13] px-1 py-0.5 rounded text-slate-300 font-mono">&lt;/head&gt;</code> Tag auf <strong>{{ $domain->url }}</strong> ein:
        </p>

        <div class="relative">
            <pre id="pulse-tracking-script" class="bg-[#070B13] border border-[#1E293B] rounded-lg p-3 text-xs font-mono text-emerald-400 select-all overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;script defer src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" data-domain="{{ $domain->uuid }}"&gt;&lt;/script&gt;</pre>
        </div>
    </div>

    <!-- 2. Analytics Metrics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Pages -->
        <div class="premium-card p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Häufigste Seiten (Top Pages)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 uppercase tracking-wider border-b border-[#1E293B]">
                            <th class="pb-2 font-medium">Pfad</th>
                            <th class="pb-2 text-right font-medium">Aufrufe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1E293B]/60">
                        @forelse ($topPages as $page)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-2.5 text-slate-300 font-mono truncate max-w-xs">{{ $page->url }}</td>
                                <td class="py-2.5 text-right text-white font-mono font-semibold">{{ number_format($page->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-slate-500">Noch keine Seitenaufrufe erfasst</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Sources -->
        <div class="premium-card p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Referrer & Quellen (Top Sources)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 uppercase tracking-wider border-b border-[#1E293B]">
                            <th class="pb-2 font-medium">Quelle</th>
                            <th class="pb-2 text-right font-medium">Besucher</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1E293B]/60">
                        @forelse ($topSources as $source)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-2.5 text-slate-300 font-mono truncate max-w-xs">{{ $source->referrer_domain ?: 'Direktaufruf / Unbekannt' }}</td>
                                <td class="py-2.5 text-right text-white font-mono font-semibold">{{ number_format($source->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-slate-500">Noch keine Referrer erfasst</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Countries -->
        <div class="premium-card p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Top Länder</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <tbody class="divide-y divide-[#1E293B]/60">
                        @forelse ($topCountries ?? [] as $row)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-2 text-slate-300">{{ $row->country }}</td>
                                <td class="py-2 text-right text-white font-mono font-semibold">{{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-3 text-slate-500" colspan="2">Noch keine Geo-Daten vorhanden</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Cities -->
        <div class="premium-card p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Top Städte</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <tbody class="divide-y divide-[#1E293B]/60">
                        @forelse ($topCities ?? [] as $row)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="py-2 text-slate-300">{{ $row->city }}@if($row->country) ({{ $row->country }})@endif</td>
                                <td class="py-2 text-right text-white font-mono font-semibold">{{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-3 text-slate-500" colspan="2">Noch keine Städte-Daten vorhanden</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
