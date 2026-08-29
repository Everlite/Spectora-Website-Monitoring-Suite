            <!-- Security Details Modal (Spectora Studio) -->
            <div x-show="showSecurityModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="showSecurityModal = false"></div>
                    <div class="inline-block align-bottom bg-[#111622] border border-[#202A3E] rounded-studio text-left overflow-hidden shadow-studio-card transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full p-6 space-y-4">
                        <div class="flex items-start justify-between pb-3 border-b border-[#202A3E]">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-studio-sm {{ ($criticalCount ?? 0) === 0 ? 'bg-[#10B981]/15 text-[#10B981]' : 'bg-[#F43F5E]/15 text-[#F43F5E]' }} flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white">Sicherheits- und Audit-Prüfbericht</h3>
                                    <p class="text-xs text-[#8A95A8] font-mono mt-0.5">{{ $domain->url }}</p>
                                </div>
                            </div>
                            <button type="button" @click="showSecurityModal = false" class="text-[#8A95A8] hover:text-white p-1 rounded-studio-sm hover:bg-[#171E2E]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="max-h-[55vh] overflow-y-auto pr-1 space-y-2.5">
                            @php
                                $issues = $securityIssues ?? $auditDetails ?? [];
                            @endphp
                            @if(!empty($issues))
                                @foreach($issues as $key => $detail)
                                    @php
                                        $status = is_array($detail) ? ($detail['status'] ?? 'ok') : 'ok';
                                        $scoreVal = is_array($detail) ? ($detail['score'] ?? null) : null;
                                        $message = is_array($detail) ? ($detail['message'] ?? '') : (string)$detail;
                                    @endphp
                                    <div class="p-3 rounded-studio-sm border border-[#202A3E] bg-[#090B10] flex items-start gap-3">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5
                                            @if($status === 'ok' || $status === 'passed') bg-[#10B981]/15 text-[#10B981]
                                            @elseif($status === 'warning') bg-[#F59E0B]/15 text-[#F59E0B]
                                            @else bg-[#F43F5E]/15 text-[#F43F5E]
                                            @endif">
                                            @if($status === 'ok' || $status === 'passed')
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @elseif($status === 'warning')
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <h4 class="text-xs font-bold text-white uppercase tracking-wider font-mono">{{ ucwords(str_replace('_', ' ', (string)$key)) }}</h4>
                                                @if($scoreVal !== null)
                                                    <span class="text-[10px] font-mono font-bold text-[#8A95A8]">{{ $scoreVal }}/100</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-[#8A95A8] mt-0.5">{{ $message }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8 text-xs text-[#8A95A8]">
                                    Keine Sicherheits-Bedrohungen gefunden.
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end pt-3 border-t border-[#202A3E]">
                            <button type="button" @click="showSecurityModal = false" class="btn-spectora-secondary">
                                Schließen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
