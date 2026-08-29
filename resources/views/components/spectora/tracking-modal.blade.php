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
             class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
             @click="closeTracking()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- shadcn Dialog Panel -->
        <div x-show="isTrackingOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-card border border-border rounded-xl text-left overflow-hidden shadow-shadcn-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
            
            <div class="p-6 space-y-4">
                
                <!-- Dialog Header -->
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <h3 class="text-base font-semibold text-foreground">Pulse Telemetrie Installation</h3>
                        <p class="text-xs text-muted-foreground font-mono" x-text="trackingDomainUrl"></p>
                    </div>
                    <button type="button" @click="closeTracking()" class="text-muted-foreground hover:text-foreground p-1 rounded-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Tabs (shadcn Segmented Controller) -->
                <div class="inline-flex rounded-lg border border-border bg-muted/40 p-1 text-muted-foreground w-full">
                    <button type="button" 
                            @click="activeTab = 'html'" 
                            :class="activeTab === 'html' ? 'bg-card text-foreground font-semibold shadow-sm' : 'hover:text-foreground'"
                            class="flex-1 px-3 py-1.5 rounded-md text-xs transition-colors">
                        HTML / WordPress
                    </button>
                    <button type="button" 
                            @click="activeTab = 'nextjs'" 
                            :class="activeTab === 'nextjs' ? 'bg-card text-foreground font-semibold shadow-sm' : 'hover:text-foreground'"
                            class="flex-1 px-3 py-1.5 rounded-md text-xs transition-colors">
                        Next.js / React
                    </button>
                    <button type="button" 
                            @click="activeTab = 'events'" 
                            :class="activeTab === 'events' ? 'bg-card text-foreground font-semibold shadow-sm' : 'hover:text-foreground'"
                            class="flex-1 px-3 py-1.5 rounded-md text-xs transition-colors">
                        Custom Events
                    </button>
                </div>

                <!-- Tab 1: HTML / WP -->
                <div x-show="activeTab === 'html'" class="space-y-3">
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Füge diesen Tag in den <code class="bg-muted px-1.5 py-0.5 rounded text-foreground font-mono">&lt;head&gt;</code> deiner Website ein. Funktioniert mit WordPress, Webflow, Shopify und statischem HTML:
                    </p>
                    <div class="relative">
                        <pre class="bg-zinc-950 border border-border rounded-lg p-3 text-xs font-mono text-emerald-400 select-all overflow-x-auto whitespace-pre-wrap leading-relaxed" x-text="getSnippet()"></pre>
                    </div>
                </div>

                <!-- Tab 2: Next.js -->
                <div x-show="activeTab === 'nextjs'" class="space-y-3">
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        In Next.js (App Router) fügst du das Skript in dein <code class="bg-muted px-1.5 py-0.5 rounded text-foreground font-mono">app/layout.tsx</code> ein:
                    </p>
                    <pre class="bg-zinc-950 border border-border rounded-lg p-3 text-xs font-mono text-zinc-300 overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;Script 
  defer 
  src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" 
  data-domain="<span x-text="trackingDomainUuid"></span>" 
/&gt;</pre>
                </div>

                <!-- Tab 3: Events -->
                <div x-show="activeTab === 'events'" class="space-y-3">
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Tracke benutzerdefinierte Conversions wie Buttons oder Formulare:
                    </p>
                    <pre class="bg-zinc-950 border border-border rounded-lg p-3 text-xs font-mono text-zinc-300 overflow-x-auto whitespace-pre-wrap leading-relaxed">window.spectora.track('lead_form_submitted', { plan: 'enterprise' });</pre>
                </div>

                <!-- Dialog Footer -->
                <div class="flex items-center justify-between pt-3 border-t border-border mt-4">
                    <button type="button" @click="closeTracking()" class="btn-ghost">
                        Schließen
                    </button>
                    <button type="button" 
                            @click="copySnippet()" 
                            class="btn-default">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        <span x-text="copied ? 'In die Zwischenablage kopiert!' : 'Code kopieren'"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
