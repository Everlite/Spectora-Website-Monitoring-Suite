@props([
    'findings' => [],
    'watchdog' => [],
    'score' => null,
    'grade' => null,
])

@php
    use App\SpectoraEngine\Audit\AuditResult;

    $rows = [];
    foreach ($findings as $key => $detail) {
        if (! is_array($detail)) {
            $rows[] = [
                'category' => 'sonstiges',
                'label' => is_string($key) ? (string) $key : 'Hinweis',
                'status' => 'success',
                'message' => (string) $detail,
                'recommendation' => null,
            ];
            continue;
        }
        $rows[] = [
            'category' => $detail['category'] ?? 'sonstiges',
            'label' => $detail['label'] ?? (is_string($key) && ! is_numeric($key) ? (string) $key : 'Prüfpunkt'),
            'status' => AuditResult::normalizeStatus($detail['status'] ?? null),
            'message' => $detail['message'] ?? '',
            'recommendation' => $detail['recommendation'] ?? null,
        ];
    }

    $watchdogIssues = is_array($watchdog) ? ($watchdog['issues'] ?? []) : [];
    foreach ($watchdogIssues as $issue) {
        if (! is_array($issue)) {
            continue;
        }
        $sev = $issue['severity'] ?? 'warning';
        $rows[] = [
            'category' => 'watchdog',
            'label' => $issue['title'] ?? 'Watchdog',
            'status' => $sev === 'critical' ? 'error' : ($sev === 'warning' ? 'warning' : 'success'),
            'message' => $issue['description'] ?? '',
            'recommendation' => $issue['recommendation'] ?? null,
        ];
    }

    $groups = [];
    foreach ($rows as $row) {
        $groups[$row['category']][] = $row;
    }

    $errors = collect($rows)->where('status', 'error')->count();
    $warns = collect($rows)->where('status', 'warning')->count();
    $oks = collect($rows)->where('status', 'success')->count();
@endphp

<section class="spectora-card overflow-hidden">
    <div class="px-5 py-4 border-b border-studio-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-sm font-extrabold text-white tracking-tight">Engine-Bericht</h2>
            <p class="text-[11px] text-studio-muted mt-0.5">Prüfpunkte der letzten Analyse — mit gemessenem Wert und nächstem Schritt.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($score !== null)
                <span class="font-mono text-xs font-bold px-2 py-1 rounded-studio-sm border border-studio-border bg-studio-bg text-white">
                    {{ (int) $score }}/100
                    @if($grade)
                        <span class="text-studio-muted font-semibold">· {{ $grade }}</span>
                    @endif
                </span>
            @endif
            <span class="text-[11px] font-mono font-bold px-2 py-1 rounded-studio-sm {{ $errors ? 'bg-studio-rose/15 text-studio-rose border border-studio-rose/30' : 'bg-studio-elevated text-studio-muted border border-studio-border' }}">{{ $errors }} Fehler</span>
            <span class="text-[11px] font-mono font-bold px-2 py-1 rounded-studio-sm {{ $warns ? 'bg-studio-amber/15 text-studio-amber border border-studio-amber/30' : 'bg-studio-elevated text-studio-muted border border-studio-border' }}">{{ $warns }} Hinweise</span>
            <span class="text-[11px] font-mono font-bold px-2 py-1 rounded-studio-sm bg-studio-emerald/15 text-studio-emerald border border-studio-emerald/30">{{ $oks }} ok</span>
        </div>
    </div>

    @if($rows === [])
        <p class="px-5 py-8 text-xs text-studio-muted">Noch kein Audit. „Deep Probe starten“ ausführen.</p>
    @else
        <div class="divide-y divide-studio-border">
            @foreach($groups as $category => $items)
                <div>
                    <div class="px-5 py-2 bg-studio-elevated/40 text-[10px] font-bold uppercase tracking-wider text-studio-muted">
                        {{ AuditResult::categoryLabel((string) $category) }}
                    </div>
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-studio-border">
                            @foreach($items as $item)
                                <tr class="align-top">
                                    <td class="pl-5 pr-3 py-3 w-44 shrink-0">
                                        <div class="text-xs font-bold text-white">{{ $item['label'] }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <p class="text-xs text-studio-text font-mono leading-relaxed">{{ $item['message'] }}</p>
                                        @if($item['recommendation'] && $item['status'] !== 'success')
                                            <p class="text-[11px] text-studio-muted mt-1">{{ $item['recommendation'] }}</p>
                                        @endif
                                    </td>
                                    <td class="pr-5 pl-3 py-3 w-24 text-right">
                                        @if($item['status'] === 'success')
                                            <span class="badge-status-online">ok</span>
                                        @elseif($item['status'] === 'warning')
                                            <span class="badge-status-warning">Hinweis</span>
                                        @else
                                            <span class="badge-status-offline">Fehler</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endif
</section>
