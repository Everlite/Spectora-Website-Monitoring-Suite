            <div class="space-y-6"
                 x-data="monitoringManager()">
                  
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Column 1 & 2: Main Settings -->
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Smart Filters Card -->
                        <div class="spectora-card p-5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-studio-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Intelligente Monitoring-Filter
                            </h3>
                            
                            <div class="space-y-2.5">
                                <!-- Only Public Pages -->
                                <label class="flex items-center justify-between p-3 rounded-studio-sm border border-studio-border bg-studio-bg hover:bg-studio-elevated/50 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-studio-emerald/15 flex items-center justify-center text-studio-emerald">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs text-white">Nur öffentliche Seiten</p>
                                            <p class="text-[11px] text-studio-muted">Überspringt Login-Bereiche und geschützte Pfade.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.only_check_public_pages" class="w-4 h-4 rounded bg-studio-surface border-studio-border text-studio-brand focus:ring-studio-brand">
                                </label>

                                <!-- Robots.txt -->
                                <label class="flex items-center justify-between p-3 rounded-studio-sm border border-studio-border bg-studio-bg hover:bg-studio-elevated/50 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-studio-brand/15 flex items-center justify-center text-studio-brand">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs text-white">Robots.txt beachten</p>
                                            <p class="text-[11px] text-studio-muted">Befolgt Disallow-Regeln in der robots.txt.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.respect_robots_txt" class="w-4 h-4 rounded bg-studio-surface border-studio-border text-studio-brand focus:ring-studio-brand">
                                </label>

                                <!-- Noindex -->
                                <label class="flex items-center justify-between p-3 rounded-studio-sm border border-studio-border bg-studio-bg hover:bg-studio-elevated/50 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-studio-amber/15 flex items-center justify-center text-studio-amber">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs text-white">Noindex respektieren</p>
                                            <p class="text-[11px] text-studio-muted">Ignoriert Seiten mit dem "noindex" Meta-Tag.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.respect_noindex" class="w-4 h-4 rounded bg-studio-surface border-studio-border text-studio-brand focus:ring-studio-brand">
                                </label>
                            </div>
                        </div>

                        <!-- Sitemap Crawler -->
                        <div class="spectora-card p-5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-studio-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                Sitemap Crawler
                            </h3>
                            <div class="flex gap-2">
                                <input type="url" x-model="sitemapUrl" placeholder="https://example.com/sitemap.xml" 
                                       class="spectora-input flex-1">
                                <button type="button" @click="crawlSitemap()" :disabled="isCrawling" class="btn-spectora-primary whitespace-nowrap">
                                    <span x-text="isCrawling ? 'Crawle...' : 'Sitemap einlesen'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Monitored Subpages Summary -->
                    <div class="space-y-4">
                        <div class="spectora-card p-5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-2">Unterseiten-Wächter</h3>
                            <p class="text-xs text-studio-muted leading-relaxed mb-3">
                                Wichtige Landingpages, Checkout-Funnels oder Kontaktformulare automatisch auf Status 200 prüfen.
                            </p>
                            <div class="p-3 bg-studio-bg border border-studio-border rounded-studio-sm">
                                <div class="text-[10px] uppercase font-bold text-studio-muted">Aktive Subpages</div>
                                <div class="text-xl font-bold font-mono text-white mt-0.5">
                                    {{ isset($monitoredUrls) ? $monitoredUrls->count() : ($domain->monitoredUrls ? $domain->monitoredUrls->count() : 0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
