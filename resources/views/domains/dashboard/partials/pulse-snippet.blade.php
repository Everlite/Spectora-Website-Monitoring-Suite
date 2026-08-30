<div class="space-y-4" x-data="{ copied: false }">
    <p class="text-xs text-studio-muted">
        Cookie-frei, unter 1 KB. Täglich rotierender HMAC-Hash statt dauerhafter ID.
        Vor <code class="bg-studio-bg px-1.5 py-0.5 rounded text-white font-mono border border-studio-border">&lt;/head&gt;</code> auf {{ $domain->url }} einfügen.
    </p>

    <div class="flex justify-end">
        <button type="button"
                @click="
                    navigator.clipboard.writeText(document.getElementById('pulse-tracking-script').textContent.trim());
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
                class="btn-spectora-primary text-xs py-1.5 px-3">
            <span x-text="copied ? 'Kopiert' : 'Code kopieren'"></span>
        </button>
    </div>

    <pre id="pulse-tracking-script" class="bg-studio-bg border border-studio-border rounded-studio-sm p-3.5 text-xs font-mono text-studio-emerald select-all overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;script defer src="{{ rtrim(config('app.url'), '/') }}/js/sp-pulse.js" data-domain="{{ $domain->uuid }}"&gt;&lt;/script&gt;</pre>

    <form method="POST" action="{{ route('domains.analytics.settings', $domain) }}" class="pt-3 border-t border-studio-border space-y-2">
        @csrf
        <p class="text-[10px] font-bold uppercase tracking-widest text-studio-muted">Standort-Genauigkeit</p>
        <p class="text-xs text-studio-muted">Keine Roh-IP. GeoIP nur so grob wie nötig.</p>
        <div class="flex flex-wrap items-center gap-3 text-xs">
            @foreach(['off' => 'Aus', 'country' => 'Land', 'city' => 'Stadt'] as $value => $label)
                <label class="inline-flex items-center gap-1.5 text-studio-muted">
                    <input type="radio" name="analytics_geo_precision" value="{{ $value }}"
                           {{ ($domain->analytics_geo_precision ?? 'city') === $value ? 'checked' : '' }}
                           class="accent-studio-brand">
                    {{ $label }}
                </label>
            @endforeach
            <button type="submit" class="btn-spectora-secondary text-xs py-1 px-2.5">Speichern</button>
        </div>
    </form>
</div>
