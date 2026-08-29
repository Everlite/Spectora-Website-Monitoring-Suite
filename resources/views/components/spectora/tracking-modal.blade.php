<div x-show="isTrackingOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true"
     x-data="{ activeTab: 'html' }">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isTrackingOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md" 
             @click="closeTracking()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Horizon Modal Panel -->
        <div x-show="isTrackingOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
            
            <div class="p-6 space-y-5">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#1B254B]">
                    <div>
                        <h3 class="text-base font-extrabold text-white">Pulse Telemetrie Setup</h3>
                        <p class="text-xs text-[#A3AED0] font-mono mt-0.5" x-text="trackingDomainUrl"></p>
                    </div>
                    <button type="button" @click="closeTracking()" class="text-[#A3AED0] hover:text-white p-1 rounded-full hover:bg-[#1B254B]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Tabs (Horizon Segmented Controller) -->
                <div class="inline-flex rounded-full bg-[#0B1437] border border-[#1B254B] p-1 text-[#A3AED0] w-full">
                    <button type="button" 
                            @click="activeTab = 'html'" 
                            :class="activeTab === 'html' ? 'bg-[#7551FF] text-white font-bold shadow-horizon-btn' : 'hover:text-white'"
                            class="flex-1 px-3 py-1.5 rounded-full text-xs transition-all">
                        HTML / WordPress
                    </button>
                    <button type="button" 
                            @click="activeTab = 'nextjs'" 
                            :class="activeTab === 'nextjs' ? 'bg-[#7551FF] text-white font-bold shadow-horizon-btn' : 'hover:text-white'"
                            class="flex-1 px-3 py-1.5 rounded-full text-xs transition-all">
                        Next.js / React
                    </button>
                    <button type="button" 
                            @click="activeTab = 'events'" 
                            :class="activeTab === 'events' ? 'bg-[#7551FF] text-white font-bold shadow-horizon-btn' : 'hover:text-white'"
                            class="flex-1 px-3 py-1.5 rounded-full text-xs transition-all">
                        Custom Events
                    </button>
                </div>

                <!-- Tab 1: HTML / WP -->
                <div x-show="activeTab === 'html'" class="space-y-3">
                    <p class="text-xs text-[#A3AED0] leading-relaxed">
                        Füge diesen Tag in den <code class="bg-[#0B1437] px-2 py-0.5 rounded-full text-white font-mono border border-[#1B254B]">&lt;head&gt;</code> deiner Website ein. Funktioniert mit WordPress, Webflow, Shopify und statischem HTML:
                    </p>
                    <div class="relative">
                        <pre class="bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm p-4 text-xs font-mono text-[#01B574] select-all overflow-x-auto whitespace-pre-wrap leading-relaxed" x-text="getSnippet()"></pre>
                    </div>
                </div>

                <!-- Tab 2: Next.js -->
                <div x-show="activeTab === 'nextjs'" class="space-y-3">
                    <p class="text-xs text-[#A3AED0] leading-relaxed">
                        In Next.js (App Router) fügst du das Skript in dein <code class="bg-[#0B1437] px-2 py-0.5 rounded-full text-white font-mono border border-[#1B254B]">app/layout.tsx</code> ein:
                    </p>
                    <pre class="bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm p-4 text-xs font-mono text-[#A3AED0] overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;Script 
  defer 
  src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" 
  data-domain="<span x-text="trackingDomainUuid"></span>" 
/&gt;</pre>
                </div>

                <!-- Tab 3: Events -->
                <div x-show="activeTab === 'events'" class="space-y-3">
                    <p class="text-xs text-[#A3AED0] leading-relaxed">
                        Tracke benutzerdefinierte Conversions wie Button-Klicks oder Formular-Einsendungen:
                    </p>
                    <pre class="bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm p-4 text-xs font-mono text-[#A3AED0] overflow-x-auto whitespace-pre-wrap leading-relaxed">window.spectora.track('lead_form_submitted', { plan: 'pro' });</pre>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-between pt-4 border-t border-[#1B254B]">
                    <button type="button" @click="closeTracking()" class="btn-horizon-ghost">
                        Schließen
                    </button>
                    <button type="button" 
                            @click="copySnippet()" 
                            class="btn-horizon-primary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        <span x-text="copied ? '✓ In Zwischenablage!' : 'Code kopieren'"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
