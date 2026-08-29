<!-- Tab Content: Analytics -->
<div x-show="tab === 'analytics'" 
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0 translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="space-y-6">

    <!-- 1. Tracking Installation Box -->
    <div class="spectora-card p-5" x-data="{ copied: false }">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center text-[#3B57E8] shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-white">Spectora Pulse Telemetrie-Code</h3>
                    <p class="text-[11px] text-[#8A95A8]">Cookie-freie, DSGVO-konforme Besucher- und Eventmessung (&lt; 1 KB)</p>
                </div>
            </div>

            <!-- Copy Button -->
            <button type="button" 
                    @click="
                        navigator.clipboard.writeText(document.getElementById('pulse-tracking-script').textContent.trim());
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                    class="btn-spectora-primary text-xs py-1.5 px-3">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                <span x-text="copied ? '✓ Kopiert!' : 'Code kopieren'"></span>
            </button>
        </div>

        <p class="text-xs text-[#8A95A8] mb-3">
            Füge diesen Tag vor dem schließenden <code class="bg-[#090B10] px-1.5 py-0.5 rounded text-white font-mono border border-[#202A3E]">&lt;/head&gt;</code> Tag auf <strong>{{ $domain->url }}</strong> ein:
        </p>

        <div class="relative">
            <pre id="pulse-tracking-script" class="bg-[#090B10] border border-[#202A3E] rounded-studio-sm p-3.5 text-xs font-mono text-[#10B981] select-all overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;script defer src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" data-domain="{{ $domain->uuid }}"&gt;&lt;/script&gt;</pre>
        </div>
    </div>

    <!-- 2. Analytics Metrics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Top Pages -->
        <div class="spectora-card p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#8A95A8] mb-3">Häufigste Seiten (Top Pages)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[#8A95A8] uppercase tracking-wider border-b border-[#202A3E] text-[11px] font-bold">
                            <th class="pb-2.5 font-semibold">Pfad</th>
                            <th class="pb-2.5 text-right font-semibold">Aufrufe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#202A3E]">
                        @forelse ($topPages ?? [] as $page)
                            <tr class="hover:bg-[#171E2E]/60 transition-colors">
                                <td class="py-2.5 text-white font-mono truncate max-w-xs">{{ $page->url ?? '/' }}</td>
                                <td class="py-2.5 text-right text-white font-mono font-bold">{{ number_format($page->total ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-6 text-center text-xs text-[#8A95A8]">Noch keine Seitendaten erfasst.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Referrers / Sources -->
        <div class="spectora-card p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#8A95A8] mb-3">Top Traffic-Quellen (Referrer)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[#8A95A8] uppercase tracking-wider border-b border-[#202A3E] text-[11px] font-bold">
                            <th class="pb-2.5 font-semibold">Quelle</th>
                            <th class="pb-2.5 text-right font-semibold">Besucher</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#202A3E]">
                        @php
                            $sources = $topSources ?? $topReferrers ?? [];
                        @endphp
                        @forelse ($sources as $source)
                            <tr class="hover:bg-[#171E2E]/60 transition-colors">
                                <td class="py-2.5 text-white font-mono truncate max-w-xs">{{ $source->referrer_domain ?? $source->referrer ?? 'Direkt / Bookmark' }}</td>
                                <td class="py-2.5 text-right text-white font-mono font-bold">{{ number_format($source->total ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-6 text-center text-xs text-[#8A95A8]">Noch keine Referrer erfasst.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
