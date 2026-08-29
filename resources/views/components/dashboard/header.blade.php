@props(['domain'])

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4" x-data="headerActions()">
            <!-- Domain Info -->
            <div class="flex items-center gap-4">
                <!-- Domain Icon -->
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 dark:from-cyan-500 dark:to-cyan-600 flex items-center justify-center shadow-lg shadow-violet-500/20 dark:shadow-cyan-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl md:text-2xl text-primary leading-tight flex items-center gap-3">
                        <span class="accent-primary break-all">{{ $domain->url }}</span>
                        @if($domain->status_code >= 200 && $domain->status_code < 400)
                            <span class="badge-success text-[10px]">● Online</span>
                        @else
                            <span class="badge-error text-[10px]">● Offline</span>
                        @endif
                        @if(isset($domain->pagespeed_score_desktop) && $domain->pagespeed_score_desktop > 0)
                            <span class="px-2 py-0.5 rounded text-[11px] font-black uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                Grade {{ $domain->grade }} ({{ $domain->pagespeed_score_desktop }}/100)
                            </span>
                        @endif
                    </h2>
                    <p class="text-muted text-sm mt-0.5">Last Check: {{ $domain->updated_at->diffForHumans() }}</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2 md:gap-3">
                <!-- Analyse Button (Primary) -->
                <button 
                    @click="runAnalysis()"
                    :disabled="isAnalyzing"
                    class="btn-primary"
                >
                    <template x-if="isAnalyzing">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!isAnalyzing">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </template>
                    <span x-text="isAnalyzing ? 'Analyzing...' : 'Analyze'"></span>
                </button>
                
                <a href="{{ $domain->url }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Visit
                </a>

                <!-- Tracking Code -->
                <button @click="showTrackingModal = true"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Tracking
                </button>

                <a href="{{ route('domains.report', $domain) }}"
                    class="px-3 md:px-4 py-2 bg-spectora-violet hover:bg-violet-600 text-white text-sm font-bold rounded-lg transition shadow-lg shadow-violet-900/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="hidden sm:inline">PDF</span>
                </a>
            </div>
            
            <!-- Analysis Feedback Toast -->
            <div x-show="analysisResult" x-cloak 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="fixed bottom-4 right-4 z-50">
                <div x-show="analysisResult === 'success'" class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">Analysis successful! Reloading...</span>
                </div>
                <div x-show="analysisResult === 'error'" class="bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="font-medium" x-text="'Error: ' + analysisError"></span>
                </div>
            </div>


            <!-- Tracking Code Modal -->
            <div x-show="showTrackingModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title" role="dialog" aria-modal="true">

                <!-- Backdrop -->
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showTrackingModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
                        @click="showTrackingModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal Panel -->
                    <div x-show="showTrackingModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-gray-800 border border-gray-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">

                        <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="text-left">
                                <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
                                    Tracking Code Installation
                                </h3>
                                <div class="mt-4" x-data="{ copied: false }">
                                    <p class="text-sm text-gray-400 mb-3">
                                        Copy and paste this code into the <code
                                            class="bg-gray-900 px-1 py-0.5 rounded text-gray-300">&lt;head&gt;</code>
                                        of your website.
                                    </p>
                                    <div class="relative group">
                                        <div class="flex items-start bg-gray-900 border border-gray-700 rounded p-2 h-24 focus-within:border-spectora-cyan transition-colors">
                                            <textarea readonly
                                                class="flex-1 bg-transparent border-none text-green-400 font-mono text-xs p-1 focus:ring-0 h-full resize-none leading-relaxed w-full"
                                                id="trackingCode"><script src="{{ url('/js/sp-core.js') }}" data-domain="{{ $domain->uuid }}"></script></textarea>
                                            
                                            <button
                                                @click="
                                                    navigator.clipboard.writeText(document.getElementById('trackingCode').value);
                                                    copied = true;
                                                    setTimeout(() => copied = false, 2000);
                                                "
                                                class="flex-none ml-2 text-xs font-bold px-3 py-1.5 rounded border transition-all duration-200"
                                                :class="copied ? 'bg-green-500/20 text-green-400 border-green-500/50' : 'bg-gray-800 hover:bg-gray-700 text-white border-gray-600'"
                                            >
                                                <span x-show="!copied">Copy</span>
                                                <span x-show="copied" x-cloak class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Copied!
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                            <button type="button" @click="showTrackingModal = false"
                                class="w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
