            <!-- Tab Content: Monitoring -->
            <div x-show="tab === 'monitoring'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6"
                 x-data="monitoringManager()">
                 
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Column 1 & 2: Main Settings -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Smart Filters Card -->
                        <div class="horizon-card p-6">
                            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#7551FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Intelligente Monitoring-Filter
                            </h3>
                            
                            <div class="space-y-3">
                                <!-- Only Public Pages -->
                                <label class="flex items-center justify-between p-3.5 rounded-horizon-sm border border-[#1B254B] bg-[#0B1437] hover:bg-[#121E4A] transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#01B574]/20 flex items-center justify-center text-[#01B574]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs text-white">Nur öffentliche Seiten</p>
                                            <p class="text-[11px] text-[#A3AED0]">Überspringt Login-Bereiche und geschützte Pfade.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.only_check_public_pages" class="w-4 h-4 rounded bg-[#111C44] border-[#1B254B] text-[#7551FF] focus:ring-[#7551FF]">
                                </label>

                                <!-- Robots.txt -->
                                <label class="flex items-center justify-between p-3.5 rounded-horizon-sm border border-[#1B254B] bg-[#0B1437] hover:bg-[#121E4A] transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#3965FF]/20 flex items-center justify-center text-[#3965FF]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs text-white">Robots.txt beachten</p>
                                            <p class="text-[11px] text-[#A3AED0]">Befolgt Disallow-Regeln in der robots.txt.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.respect_robots_txt" class="w-4 h-4 rounded bg-[#111C44] border-[#1B254B] text-[#7551FF] focus:ring-[#7551FF]">
                                </label>

                                <!-- Noindex -->
                                <label class="flex items-center justify-between p-3.5 rounded-horizon-sm border border-[#1B254B] bg-[#0B1437] hover:bg-[#121E4A] transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#FFB547]/20 flex items-center justify-center text-[#FFB547]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs text-white">Noindex respektieren</p>
                                            <p class="text-[11px] text-[#A3AED0]">Ignoriert Seiten mit dem "noindex" Meta-Tag.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.respect_noindex" class="w-4 h-4 rounded bg-[#111C44] border-[#1B254B] text-[#7551FF] focus:ring-[#7551FF]">
                                </label>
                            </div>
                        </div>

                        <!-- Sitemap Crawler -->
                        <div class="horizon-card p-6">
                            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#7551FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                Sitemap Crawler
                            </h3>
                            <div class="flex gap-2">
                                <input type="url" x-model="sitemapUrl" placeholder="https://example.com/sitemap.xml" 
                                       class="horizon-input flex-1">
                                <button type="button" @click="crawlSitemap()" :disabled="isCrawling" class="btn-horizon-primary whitespace-nowrap">
                                    <span x-text="isCrawling ? 'Crawle...' : 'Sitemap einlesen'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Monitored Subpages Summary -->
                    <div class="space-y-6">
                        <div class="horizon-card p-6">
                            <h3 class="text-sm font-bold text-white mb-3">Unterseiten-Wächter</h3>
                            <p class="text-xs text-[#A3AED0] leading-relaxed mb-4">
                                Wichtige Landingpages, Checkout-Funnels oder Kontaktformulare automatisch auf Status 200 prüfen.
                            </p>
                            <div class="p-3 bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm">
                                <div class="text-[10px] uppercase font-bold text-[#A3AED0]">Aktive Subpages</div>
                                <div class="text-xl font-bold font-mono text-white mt-1">
                                    {{ $domain->subpages ? $domain->subpages->count() : 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
