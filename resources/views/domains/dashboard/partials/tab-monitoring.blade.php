            <!-- Tab Content: Monitoring -->
            <div x-show="tab === 'monitoring'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6"
                 x-data="monitoringManager()">
                 
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Column 1 & 2: Main Settings -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Smart Filters Card -->
                        <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Smart Monitoring Filters
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Only Public Pages -->
                                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-gray-200">Only public pages</p>
                                            <p class="text-xs text-slate-500 dark:text-gray-400">Automatically skips login areas and protected content.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.only_check_public_pages" class="w-5 h-5 rounded border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-violet-600 focus:ring-violet-500">
                                </label>

                                <!-- Robots.txt -->
                                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-gray-200">Respect robots.txt</p>
                                            <p class="text-xs text-slate-500 dark:text-gray-400">Follows the instructions in robots.txt (User-Agent: SpectoraBot).</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.respect_robots_txt" class="w-5 h-5 rounded border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-violet-600 focus:ring-violet-500">
                                </label>

                                <!-- Noindex -->
                                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-gray-200">Respect noindex</p>
                                            <p class="text-xs text-slate-500 dark:text-gray-400">Ignores pages with "noindex" meta tag or HTTP header.</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" x-model="settings.respect_noindex" class="w-5 h-5 rounded border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-violet-600 focus:ring-violet-500">
                                </label>
                            </div>
                        </div>

                        <!-- URL Patterns Card -->
                        <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                URL Exclusion Patterns
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-gray-400 mb-4">One pattern per line. Use * as a wildcard (e.g. <code>*/downloads/*</code>).</p>
                            <textarea 
                                x-model="settings.exclude_patterns"
                                rows="5"
                                class="w-full rounded-xl border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-900/50 text-slate-900 dark:text-white focus:ring-violet-500 focus:border-violet-500 font-mono text-sm"
                                placeholder="*/private/*&#10;*/kran/*"></textarea>
                            
                            <div class="mt-6 flex justify-end">
                                <button @click="saveSettings()" class="btn-primary" :disabled="isSaving">
                                    <template x-if="isSaving">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </template>
                                    <span x-text="isSaving ? 'Saving...' : 'Save settings'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Sitemaps & URLs -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Sitemaps Card -->
                        <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                                Sitemaps
                            </h3>
                            <div class="mb-4">
                                <button @click="detectSitemaps()" class="w-full py-2 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-300 rounded-lg text-sm font-bold hover:bg-slate-200 dark:hover:bg-gray-600 transition flex items-center justify-center gap-2" :disabled="isDetecting">
                                    <template x-if="isDetecting">
                                        <svg class="animate-spin h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </template>
                                    <span x-text="isDetecting ? 'Searching...' : 'Search sitemaps automatically'"></span>
                                </button>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                <template x-for="sitemap in sitemap_urls" :key="sitemap">
                                    <label class="flex items-center gap-3 p-2 rounded bg-slate-50 dark:bg-gray-900/40 border border-slate-100 dark:border-gray-700 hover:bg-slate-100 dark:hover:bg-gray-700 transition cursor-pointer">
                                        <input type="checkbox" :value="sitemap" x-model="settings.included_sitemaps" class="w-4 h-4 rounded border-slate-300 dark:border-gray-600 text-cyan-500 focus:ring-cyan-500">
                                        <span class="text-xs font-mono text-slate-600 dark:text-gray-300 truncate" x-text="sitemap.split('/').pop() || sitemap"></span>
                                    </label>
                                </template>
                                <template x-if="sitemap_urls.length === 0">
                                    <p class="text-center py-4 text-xs text-slate-400 italic">No sitemaps found.</p>
                                </template>
                            </div>
                        </div>

                        <!-- URL Selection Card -->
                        <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                Monitored URLs
                            </h3>
                            
                            <div class="mb-4">
                                <button @click="openUrlSelector()" class="w-full py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-bold hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Select & Manage URLs
                                </button>
                            </div>

                            <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                <template x-for="url in monitoredUrls" :key="url.id || url.url">
                                    <div class="flex items-center justify-between p-2 rounded bg-slate-50 dark:bg-gray-900/40 border border-slate-100 dark:border-gray-700/50">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-mono text-slate-500 dark:text-gray-400 truncate" x-text="url.url"></p>
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            <span x-show="url.is_active" class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                            <span x-text="url.is_active ? 'Active' : 'Inactive'" class="text-[10px] text-slate-500"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="monitoredUrls.length === 0">
                                    <p class="text-center py-4 text-xs text-slate-400 italic">No additional URLs selected yet.</p>
                                </template>
                            </div>
                        </div>
                    </div> <!-- End Column 3 -->
                    
                </div> <!-- End Grid -->

                <!-- URL Selection Modal (Now safely inside monitoringManager) -->
                <div x-show="showUrlModal" 
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showUrlModal = false">
                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
                        </div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 flex justify-between items-center">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Select URLs to monitor</h3>
                                <button @click="showUrlModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"></path></svg>
                                </button>
                            </div>

                            <div class="p-6">
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="text-sm text-slate-500 dark:text-gray-400">
                                        Scanning sitemaps and homepage for links...
                                    </div>
                                    <div class="flex gap-2">
                                        <button @click="selectPublicOnly()" class="text-xs font-bold text-violet-600 dark:text-violet-400 hover:underline">Select public only</button>
                                        <span class="text-slate-300 dark:text-gray-600">|</span>
                                        <button @click="toggleAllUrls()" class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:underline" x-text="allSelected ? 'Deselect all' : 'Select all'"></button>
                                    </div>
                                </div>

                                <div class="space-y-2 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                    <template x-if="isScanningUrls">
                                        <div class="py-12 text-center">
                                            <svg class="animate-spin h-8 w-8 text-violet-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <p class="text-slate-500">Analyzing domain structure...</p>
                                        </div>
                                    </template>

                                    <template x-for="item in discoveredUrls" :key="item.url">
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer group">
                                            <input type="checkbox" x-model="item.is_monitored" class="w-5 h-5 rounded border-slate-300 dark:border-gray-600 text-violet-600 focus:ring-violet-500">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-mono text-slate-700 dark:text-gray-300 truncate" x-text="item.url"></p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <template x-if="item.is_public">
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">PUBLIC</span>
                                                    </template>
                                                    <template x-if="!item.is_public">
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400" :title="item.skip_reason">PRIVATE/LOCKED</span>
                                                    </template>
                                                </div>
                                            </div>
                                        </label>
                                    </template>

                                    <template x-if="discoveredUrls.length === 0 && !isScanningUrls">
                                        <div class="py-12 text-center text-slate-400 text-sm">
                                            <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            No URLs found. Try different sitemap settings.
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 border-t border-slate-100 dark:border-gray-700 flex justify-end gap-3">
                                <button @click="showUrlModal = false" class="px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition">Cancel</button>
                                <button @click="saveUrlSelection()" class="btn-primary" :disabled="isSyncingUrls">
                                    <template x-if="isSyncingUrls">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </template>
                                    <span x-text="isSyncingUrls ? 'Saving...' : 'Save selection'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Modal -->

            </div> <!-- End Tab Content: Monitoring (monitoringManager) -->
