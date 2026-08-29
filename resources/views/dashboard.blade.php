<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                    <h2 class="font-extrabold text-2xl text-white tracking-tight">
                        Agency Command Center
                    </h2>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Spectora Multi-Factor Probes & Privacy Telemetry Matrix</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <!-- Push Alert State Badge -->
                <x-spectora.push-alerts-badge />

                <!-- Add Domain Button -->
                <div x-data>
                    <button @click="$dispatch('open-add-domain')" 
                            class="btn-cyber-primary text-xs py-2 px-4 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Website
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="dashboardManager()">
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Global Flash Messages -->
            @if (session('status'))
                <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl flex items-center justify-between shadow-lg" role="alert">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm font-medium">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-xl shadow-lg" role="alert">
                    <div class="font-bold text-sm mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Action Failed
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. Global KPIs Bar -->
            <x-spectora.global-metrics :kpis="$kpis ?? []" />

            <!-- 2. Monitored Websites Grid -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        Active Monitoring Targets
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-xs font-mono text-cyan-400 border border-slate-700">{{ count($domains) }}</span>
                    </h3>
                </div>
            </div>

            @if(count($domains) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($domains as $domain)
                        <x-spectora.domain-card :domain="$domain" />
                    @endforeach
                </div>
            @else
                <!-- Zero State -->
                <div class="glass-card p-12 text-center my-8 max-w-xl mx-auto border-dashed border-slate-700">
                    <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">No Monitored Websites Yet</h3>
                    <p class="text-xs text-slate-400 mt-2 mb-6">Add your first client domain to start tracking automated uptime probes, SSL expiration, and deep SEO/Security audits.</p>
                    <button type="button" @click="$dispatch('open-add-domain')" class="btn-cyber-primary text-xs py-2 px-4">
                        + Add First Domain
                    </button>
                </div>
            @endif

            <!-- Modals -->
            <x-spectora.add-domain-modal />
            <x-spectora.watchdog-modal />
            <x-spectora.notes-modal />
            <x-spectora.delete-modal />

        </div>
    </div>

    <!-- Alpine Dashboard Controller Script -->
    <script>
        function dashboardManager() {
            return {
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
                    const newContent = prompt('Edit note content:', note.content);
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
                        return d.toLocaleDateString('en-GB', {day: '2-digit', month: '2-digit', year: 'numeric'}) + ' ' + d.toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'});
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
                        textEl.textContent = '✓ Copied Telemetry!';
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
                    this.confirmDelete('note', noteId, 'this team note');
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
                    alert('VAPID Public Key is not configured on server.');
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