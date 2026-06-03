            <!-- Security Details Modal -->
            <div x-show="showSecurityModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="showSecurityModal = false"></div>
                    <div class="inline-block align-bottom bg-spectora-card border border-gray-700 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                        <div class="bg-spectora-card px-5 pt-5 pb-4 sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full {{ $domain->safety_status === 'safe' ? 'bg-green-500/20' : 'bg-red-500/20' }} sm:mx-0">
                                    @if($domain->safety_status === 'safe')
                                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    @else
                                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    @endif
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg font-bold text-white">Security Report</h3>
                                    
                                    @if($criticalCount > 0 || $warningCount > 0)
                                        <div class="flex gap-2 mt-2 text-xs">
                                            @if($criticalCount > 0)
                                                <span class="px-2 py-1 rounded bg-red-500/20 text-red-400 font-bold">{{ $criticalCount }} Critical</span>
                                            @endif
                                            @if($warningCount > 0)
                                                <span class="px-2 py-1 rounded bg-orange-500/20 text-orange-400 font-bold">{{ $warningCount }} Warnings</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-4 max-h-[55vh] overflow-y-auto pr-2 space-y-3">
                                        @if(!empty($securityIssues))
                                            @foreach($securityIssues as $issue)
                                                @php
                                                    $severity = $issue['severity'] ?? 'warning';
                                                    $colors = match($severity) {
                                                        'critical' => ['bg' => 'bg-red-500/10', 'border' => 'border-red-500/30', 'badge' => 'bg-red-500/20 text-red-400'],
                                                        'warning' => ['bg' => 'bg-orange-500/10', 'border' => 'border-orange-500/30', 'badge' => 'bg-orange-500/20 text-orange-400'],
                                                        default => ['bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/30', 'badge' => 'bg-blue-500/20 text-blue-400'],
                                                    };
                                                @endphp
                                                <div class="{{ $colors['bg'] }} {{ $colors['border'] }} border rounded-lg p-4">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <h4 class="font-bold text-white text-sm">{{ $issue['title'] ?? 'Issue' }}</h4>
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded {{ $colors['badge'] }} uppercase font-bold">{{ $severity }}</span>
                                                    </div>
                                                    <p class="text-gray-300 text-sm">{{ $issue['description'] ?? '' }}</p>
                                                    @if(!empty($issue['recommendation']))
                                                        <div class="mt-2 flex items-start gap-2 text-xs">
                                                            <span class="text-green-500">✅</span>
                                                            <span class="text-green-400">{{ $issue['recommendation'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-8">
                                                <svg class="w-12 h-12 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                <p class="text-green-400 font-bold">All safe!</p>
                                                <p class="text-gray-500 text-sm mt-1">No security issues found.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-800/50 px-5 py-3 sm:flex sm:flex-row-reverse border-t border-gray-700">
                            <button type="button" @click="showSecurityModal = false" class="w-full inline-flex justify-center rounded-lg px-4 py-2 bg-spectora-cyan text-gray-900 text-sm font-bold hover:bg-cyan-400 transition sm:w-auto">Close</button>
                        </div>
                    </div>
                </div>
            </div>
