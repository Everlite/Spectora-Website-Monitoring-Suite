            @if (session('error'))
                <div class="mb-4 bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded-xl relative shadow-lg" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Tabs Navigation - Professional Style -->
            <div class="card-base p-1.5 flex w-full overflow-x-auto scrollbar-hide">
                <nav class="flex space-x-1 w-full" aria-label="Tabs">
                    <button
                        @click="tab = 'overview'"
                        :class="tab === 'overview' 
                            ? 'bg-gradient-to-r from-violet-500 to-violet-600 dark:from-cyan-500 dark:to-cyan-600 text-white shadow-lg dark:shadow-cyan-500/20' 
                            : 'text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-gray-700/50'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Overview
                    </button>

                    <button
                        @click="tab = 'analytics'"
                        :class="tab === 'analytics' 
                            ? 'bg-gradient-to-r from-violet-500 to-violet-600 dark:from-cyan-500 dark:to-cyan-600 text-white shadow-lg dark:shadow-cyan-500/20' 
                            : 'text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-gray-700/50'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Analytics
                    </button>

                    <button
                        @click="tab = 'history'"
                        :class="tab === 'history' 
                            ? 'bg-gradient-to-r from-violet-500 to-violet-600 dark:from-cyan-500 dark:to-cyan-600 text-white shadow-lg dark:shadow-cyan-500/20' 
                            : 'text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-gray-700/50'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        History
                    </button>

                    <button
                        @click="tab = 'notes'"
                        :class="tab === 'notes' 
                            ? 'bg-gradient-to-r from-violet-500 to-violet-600 dark:from-cyan-500 dark:to-cyan-600 text-white shadow-lg dark:shadow-cyan-500/20' 
                            : 'text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-gray-700/50'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Notes
                    </button>

                    <button
                        @click="tab = 'monitoring'"
                        :class="tab === 'monitoring' 
                            ? 'bg-gradient-to-r from-violet-500 to-violet-600 dark:from-cyan-500 dark:to-cyan-600 text-white shadow-lg dark:shadow-cyan-500/20' 
                            : 'text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-gray-700/50'"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Monitoring
                    </button>

                </nav>
            </div>
