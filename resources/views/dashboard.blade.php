<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-xl text-white tracking-tight">
                        Agency Monitoring
                    </h2>
                    <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-blue-950/60 text-blue-400 border border-blue-800/40">Spectora Engine</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Uptime-Probes, SSL-Zertifikate & Pulse-Telemetrie</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <x-spectora.push-alerts-badge />

                <!-- Add Domain Button -->
                <div x-data>
                    <button @click="$dispatch('open-add-domain')" 
                            class="btn-premium-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Website hinzufügen
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="dashboardManager()">
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Global Flash Messages -->
            @if (session('status'))
                <div class="mb-5 bg-emerald-950/40 border border-emerald-800/40 text-emerald-300 px-4 py-3 rounded-xl flex items-center justify-between text-xs font-medium" role="alert">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 bg-rose-950/40 border border-rose-800/40 text-rose-300 px-4 py-3 rounded-xl text-xs" role="alert">
                    <div class="font-bold mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Aktion fehlgeschlagen
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. Global KPIs Bar -->
            <x-spectora.global-metrics :kpis="$kpis ?? []" />

            <!-- 2. Search & Filter Bar -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 mb-5">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Domains durchsuchen..." 
                           class="w-full bg-[#0F1626] border border-[#1E293B] rounded-lg pl-9 pr-4 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors">
                </div>

                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5 bg-[#0F1626] border border-[#1E293B] p-1 rounded-lg">
                    <button type="button" 
                            @click="filterStatus = 'all'" 
                            :class="filterStatus === 'all' ? 'bg-[#1E293B] text-white' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded text-xs font-medium transition-colors">
                        Alle ({{ count($domains) }})
                    </button>
                    <button type="button" 
                            @click="filterStatus = 'online'" 
                            :class="filterStatus === 'online' ? 'bg-[#1E293B] text-emerald-400' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded text-xs font-medium transition-colors">
                        Online ({{ $kpis['online_count'] ?? 0 }})
                    </button>
                    <button type="button" 
                            @click="filterStatus = 'offline'" 
                            :class="filterStatus === 'offline' ? 'bg-[#1E293B] text-rose-400' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded text-xs font-medium transition-colors">
                        Störungen ({{ $kpis['active_incidents'] ?? 0 }})
                    </button>
                </div>
            </div>

            <!-- 3. Monitored Websites Grid -->
            @if(count($domains) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($domains as $domain)
                        <div x-show="matchesFilter({{ json_encode(strtolower($domain->url)) }}, {{ json_encode($domain->status_code >= 200 && $domain->status_code < 400 ? 'online' : 'offline') }})">
                            <x-spectora.domain-card :domain="$domain" />
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Zero State -->
                <div class="premium-card p-12 text-center my-8 max-w-lg mx-auto border-dashed">
                    <div class="w-12 h-12 rounded-xl bg-blue-950/50 border border-blue-800/40 flex items-center justify-center text-blue-400 mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Noch keine Websites überwacht</h3>
                    <p class="text-xs text-slate-400 mt-1 mb-5">Füge deine erste Kunden-Website hinzu, um automatisierte Uptime-Probes und Audits zu starten.</p>
                    <button type="button" @click="$dispatch('open-add-domain')" class="btn-premium-primary">
                        + Erste Website hinzufügen
                    </button>
                </div>
            @endif

            <!-- Modals -->
            <x-spectora.add-domain-modal />
            <x-spectora.watchdog-modal />
            <x-spectora.notes-modal />
            <x-spectora.delete-modal />
            <x-spectora.tracking-modal />

        </div>
    </div>

    <!-- Alpine Dashboard Controller Script -->
    <script>
        function dashboardManager() {
            return {
                searchQuery: '',
                filterStatus: 'all',

                matchesFilter(url, status) {
                    const matchesSearch = !this.searchQuery || url.includes(this.searchQuery.toLowerCase());
                    const matchesStatus = this.filterStatus === 'all' || this.filterStatus === status;
                    return matchesSearch && matchesStatus;
                },

                // Tracking Code Modal
                isTrackingOpen: false,
                trackingDomainUrl: '',
                trackingDomainUuid: '',
                copied: false,

                openTracking(url, uuid) {
                    this.trackingDomainUrl = url;
                    this.trackingDomainUuid = uuid;
                    this.isTrackingOpen = true;
                    this.copied = false;
                },

                closeTracking() {
                    this.isTrackingOpen = false;
                },

                getSnippet() {
                    const host = window.location.origin;
                    return `<script defer src="${host}/js/sp-pulse.js" data-domain="${this.trackingDomainUuid}"><\/script>`;
                },

                copySnippet() {
                    navigator.clipboard.writeText(this.getSnippet());
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                },

                // Notes Modal
                isNotesOpen: false,
                domainId: null,
                domainUrl: '',
                notes: [],
                newNote: '',

                async openNotes(id, url) {
                    this.domainId = id;
                    this.domainUrl = url;
                    this.isNotesOpen = true;
                    this.newNote = '';
                    await this.fetchNotes();
                },

                closeNotes() {
                    this.isNotesOpen = false;
                    this.domainId = null;
                    this.notes = [];
                },

                async fetchNotes() {
                    try {
                        const response = await fetch(`/domains/${this.domainId}/notes`);
                        if (response.ok) {
                            this.notes = await response.json();
                        }
                    } catch (e) {
                        console.error('Failed to fetch notes:', e);
                    }
                },

                async addNote() {
                    if (!this.newNote.trim()) return;

                    try {
                        const response = await fetch(`/domains/${this.domainId}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ content: this.newNote })
                        });

                        if (response.ok) {
                            this.newNote = '';
                            await this.fetchNotes();
                        }
                    } catch (e) {
                        console.error('Failed to add note:', e);
                    }
                },

                async editNote(note) {
                    const newContent = prompt('Notiz bearbeiten:', note.content);
                    if (newContent === null || newContent === note.content) return;

                    try {
                        const response = await fetch(`/notes/${note.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ content: newContent })
                        });

                        if (response.ok) {
                            await this.fetchNotes();
                        }
                    } catch (e) {
                        console.error('Failed to edit note:', e);
                    }
                },

                formatDate(dateString) {
                    try {
                        const d = new Date(dateString);
                        return d.toLocaleDateString('de-DE', {day: '2-digit', month: '2-digit', year: 'numeric'}) + ' ' + d.toLocaleTimeString('de-DE', {hour: '2-digit', minute: '2-digit'});
                    } catch (e) {
                        return dateString;
                    }
                },

                // Watchdog Modal
                isWatchdogOpen: false,
                watchdogUrl: '',
                watchdogDetails: null,
                watchdogType: '',

                openWatchdog(url, details, type) {
                    this.watchdogUrl = url;
                    this.watchdogDetails = details;
                    this.watchdogType = type;
                    this.isWatchdogOpen = true;
                },

                closeWatchdog() {
                    this.isWatchdogOpen = false;
                    this.watchdogDetails = null;
                },

                copyWatchdogJson() {
                    navigator.clipboard.writeText(JSON.stringify(this.watchdogDetails, null, 2));
                    const textEl = document.getElementById('wd-copy-text');
                    if (textEl) {
                        const orig = textEl.textContent;
                        textEl.textContent = '✓ Kopiert!';
                        setTimeout(() => { textEl.textContent = orig; }, 1800);
                    }
                },

                // Delete Confirmation Modal
                isDeleteModalOpen: false,
                deleteType: '',
                deleteLabel: '',
                deleteTargetId: '',

                confirmDelete(type, id, label) {
                    this.deleteType = type;
                    this.deleteTargetId = id;
                    this.deleteLabel = label;
                    this.isDeleteModalOpen = true;
                },

                closeDeleteModal() {
                    this.isDeleteModalOpen = false;
                    this.deleteType = '';
                    this.deleteTargetId = '';
                    this.deleteLabel = '';
                },

                async submitDelete() {
                    if (this.deleteType === 'domain') {
                        const form = document.getElementById(this.deleteTargetId);
                        if (form) form.submit();
                    } else if (this.deleteType === 'note') {
                        await this.performDeleteNote(this.deleteTargetId);
                        this.closeDeleteModal();
                    }
                },

                deleteNote(noteId) {
                    this.confirmDelete('note', noteId, 'diese Notiz');
                },

                async performDeleteNote(noteId) {
                    try {
                        const response = await fetch(`/notes/${noteId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            await this.fetchNotes();
                        }
                    } catch (e) {
                        console.error('Failed to delete note:', e);
                    }
                }
            };
        }

        function addDomainManager() {
            return {
                isOpen: {{ $errors->any() ? 'true' : 'false' }},
                openModal() {
                    this.isOpen = true;
                },
                closeModal() {
                    this.isOpen = false;
                }
            };
        }

        // Web Push Notification Initializer
        document.addEventListener('DOMContentLoaded', function() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                return;
            }

            if (Notification.permission === 'denied') {
                const blockedBadge = document.getElementById('notifications-blocked-badge');
                if (blockedBadge) blockedBadge.style.display = 'inline-flex';
                return;
            }

            navigator.serviceWorker.ready.then(function(registration) {
                registration.pushManager.getSubscription().then(function(subscription) {
                    if (subscription) {
                        const activeBadge = document.getElementById('notifications-active-badge');
                        if (activeBadge) activeBadge.style.display = 'inline-flex';
                    } else {
                        const btn = document.getElementById('enable-notifications-btn');
                        if (btn) btn.style.display = 'inline-flex';
                    }
                }).catch(() => {});
            }).catch(() => {});
        });

        function enableNotifications() {
            navigator.serviceWorker.ready.then(function(registration) {
                const vapidPublicKey = '{{ config('webpush.vapid.public_key') }}';
                if (!vapidPublicKey) {
                    alert('VAPID Public Key ist auf dem Server nicht konfiguriert.');
                    return;
                }

                registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
                }).then(function(subscription) {
                    fetch('/subscriptions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(subscription)
                    }).then(function(response) {
                        if (response.ok) {
                            const btn = document.getElementById('enable-notifications-btn');
                            const activeBadge = document.getElementById('notifications-active-badge');
                            if (btn) btn.style.display = 'none';
                            if (activeBadge) activeBadge.style.display = 'inline-flex';
                        }
                    });
                }).catch(function(err) {
                    if (Notification.permission === 'denied') {
                        const btn = document.getElementById('enable-notifications-btn');
                        const blockedBadge = document.getElementById('notifications-blocked-badge');
                        if (btn) btn.style.display = 'none';
                        if (blockedBadge) blockedBadge.style.display = 'inline-flex';
                    }
                });
            });
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    </script>
</x-app-layout>