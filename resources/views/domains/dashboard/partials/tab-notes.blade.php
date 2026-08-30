            <div class="space-y-6">

                <!-- Add Note Card -->
                <div class="spectora-card p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-studio-text mb-3">Neue Team-Notiz hinzufügen</h3>
                    <form action="{{ route('domains.notes.store', $domain) }}" method="POST" class="space-y-3">
                        @csrf
                        <textarea 
                            name="content" 
                            rows="3" 
                            class="flex w-full rounded-studio-sm border border-studio-border bg-studio-bg px-3.5 py-2.5 text-xs text-studio-text placeholder-studio-subtle focus:border-studio-brand focus:outline-none focus:ring-1 focus:ring-studio-brand"
                            placeholder="Notiz für das Team (Deployments, DNS-Änderungen, Wartungsfenster)..."
                            required
                        ></textarea>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="btn-spectora-primary">
                                Notiz speichern
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notes List -->
                <div class="space-y-3">
                    @forelse($notes as $note)
                        <div class="spectora-card p-4 flex flex-col justify-between" id="note-{{ $note->id }}">
                            <div>
                                <div class="flex items-center justify-between mb-2 pb-2 border-b border-studio-border">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-studio-sm bg-studio-elevated border border-studio-border flex items-center justify-center font-bold text-xs text-studio-brand">
                                            {{ substr($note->user->first_name ?? 'A', 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-studio-text">
                                            {{ $note->user ? $note->user->first_name . ' ' . $note->user->last_name : 'Agentur-Team' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-studio-muted font-mono">
                                            {{ $note->created_at->format('d.m.Y H:i') }}
                                        </span>

                                        @if(Auth::id() === $note->user_id || Auth::user()->is_admin)
                                            <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline" onsubmit="return confirm('Möchtest du diese Notiz wirklich löschen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-studio-muted hover:text-studio-rose transition-colors p-1" title="Löschen">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-xs text-studio-text whitespace-pre-wrap leading-relaxed">
                                    {{ $note->content }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="spectora-card p-8 text-center text-studio-muted text-xs">
                            Noch keine Notizen für diese Domain hinterlegt.
                        </div>
                    @endforelse
                </div>

            </div>
