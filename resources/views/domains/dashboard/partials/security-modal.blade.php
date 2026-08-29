            <!-- Security Details Modal (Horizon UI) -->
            <div x-show="showSecurityModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md transition-opacity" aria-hidden="true" @click="showSecurityModal = false"></div>
                    <div class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                        <div class="p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full {{ $domain->safety_status === 'safe' ? 'bg-[#01B574]/20 text-[#01B574]' : 'bg-[#EE5D50]/20 text-[#EE5D50]' }} sm:mx-0">
                                    @if($domain->safety_status === 'safe')
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    @else
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    @endif
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-base font-extrabold text-white">Security Report</h3>
                                    
                                    @if($criticalCount > 0 || $warningCount > 0)
                                        <div class="flex gap-2 mt-2 text-xs">
                                            @if($criticalCount > 0)
                                                <span class="badge-horizon-danger">{{ $criticalCount }} Critical</span>
                                            @endif
                                            @if($warningCount > 0)
                                                <span class="badge-horizon-warning">{{ $warningCount }} Warnings</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-4 max-h-[55vh] overflow-y-auto pr-2 space-y-3">
                                        @if(!empty($securityIssues))
                                            @foreach($securityIssues as $issue)
                                                @php
                                                    $severity = $issue['severity'] ?? 'warning';
                                                    $colors = match($severity) {
                                                        'critical' => ['bg' => 'bg-[#EE5D50]/10', 'border' => 'border-[#EE5D50]/30', 'badge' => 'badge-horizon-danger'],
                                                        'warning' => ['bg' => 'bg-[#FFB547]/10', 'border' => 'border-[#FFB547]/30', 'badge' => 'badge-horizon-warning'],
                                                        default => ['bg' => 'bg-[#1B254B]', 'border' => 'border-[#2B3674]', 'badge' => 'badge-horizon-neutral'],
                                                    };
                                                @endphp
                                                <div class="{{ $colors['bg'] }} {{ $colors['border'] }} border rounded-horizon-sm p-4">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <h4 class="font-bold text-white text-xs">{{ $issue['title'] ?? 'Issue' }}</h4>
                                                        <span class="{{ $colors['badge'] }} text-[10px] uppercase font-bold">{{ $severity }}</span>
                                                    </div>
                                                    <p class="text-[#A3AED0] text-xs">{{ $issue['description'] ?? '' }}</p>
                                                    @if(!empty($issue['recommendation']))
                                                        <div class="mt-2 flex items-start gap-2 text-xs">
                                                            <span class="text-[#01B574]">✓</span>
                                                            <span class="text-[#01B574]">{{ $issue['recommendation'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-8">
                                                <svg class="w-10 h-10 mx-auto text-[#01B574] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                <p class="text-[#01B574] font-bold text-sm">Alles sicher!</p>
                                                <p class="text-[#A3AED0] text-xs mt-1">Keine Sicherheits-Bedrohungen gefunden.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[#0B1437] px-6 py-3.5 sm:flex sm:flex-row-reverse border-t border-[#1B254B]">
                            <button type="button" @click="showSecurityModal = false" class="btn-horizon-secondary">
                                Schließen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
