<?php

namespace App\Support;

/**
 * Generates inline SVG charts for PDF reports (no external services).
 */
class ReportChartSvg
{
    public static function lineChart(
        array $values,
        int $width = 400,
        int $height = 200,
        string $stroke = '#38BDF8',
        string $fill = 'rgba(56, 189, 248, 0.15)',
        ?float $minY = null,
        ?float $maxY = null,
    ): string {
        $padding = 24;
        $plotW = $width - ($padding * 2);
        $plotH = $height - ($padding * 2);
        $count = count($values);

        if ($count === 0) {
            return self::emptyChart($width, $height);
        }

        $numeric = array_values(array_filter($values, fn ($v) => $v !== null && $v !== ''));
        if ($numeric === []) {
            return self::emptyChart($width, $height);
        }

        $minY ??= min($numeric);
        $maxY ??= max($numeric);
        if ($maxY <= $minY) {
            $maxY = $minY + 1;
        }

        $points = [];
        $areaPoints = [];
        $step = $count > 1 ? $plotW / ($count - 1) : 0;

        for ($i = 0; $i < $count; $i++) {
            $x = $padding + ($step * $i);
            $val = $values[$i];
            if ($val === null || $val === '') {
                continue;
            }
            $y = $padding + $plotH - ((($val - $minY) / ($maxY - $minY)) * $plotH);
            $points[] = round($x, 1).','.round($y, 1);
            $areaPoints[] = ['x' => $x, 'y' => $y];
        }

        if ($points === []) {
            return self::emptyChart($width, $height);
        }

        $polyline = implode(' ', $points);
        $areaPath = self::buildAreaPath($areaPoints, $padding + $plotH);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <rect width="100%" height="100%" fill="#ffffff"/>
  <path d="{$areaPath}" fill="{$fill}" stroke="none"/>
  <polyline points="{$polyline}" fill="none" stroke="{$stroke}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
</svg>
SVG;

        return self::toDataUri($svg);
    }

    public static function barChart(
        array $values,
        int $width = 400,
        int $height = 200,
        string $fill = '#0F172A',
    ): string {
        $padding = 24;
        $plotW = $width - ($padding * 2);
        $plotH = $height - ($padding * 2);
        $count = count($values);

        if ($count === 0) {
            return self::emptyChart($width, $height);
        }

        $max = max(1, max(array_map(fn ($v) => (float) $v, $values)));
        $barW = $plotW / $count * 0.7;
        $gap = $plotW / $count * 0.3;

        $bars = '';
        for ($i = 0; $i < $count; $i++) {
            $val = (float) ($values[$i] ?? 0);
            $barH = ($val / $max) * $plotH;
            $x = $padding + ($i * ($barW + $gap));
            $y = $padding + $plotH - $barH;
            $bars .= sprintf(
                '<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" fill="%s" rx="1"/>',
                $x,
                $y,
                $barW,
                max(0, $barH),
                htmlspecialchars($fill, ENT_QUOTES)
            );
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <rect width="100%" height="100%" fill="#ffffff"/>
  {$bars}
</svg>
SVG;

        return self::toDataUri($svg);
    }

    private static function buildAreaPath(array $points, float $baselineY): string
    {
        if ($points === []) {
            return '';
        }

        $first = $points[0];
        $path = 'M '.round($first['x'], 1).' '.round($baselineY, 1);
        $path .= ' L '.round($first['x'], 1).' '.round($first['y'], 1);

        foreach ($points as $pt) {
            $path .= ' L '.round($pt['x'], 1).' '.round($pt['y'], 1);
        }

        $last = $points[count($points) - 1];
        $path .= ' L '.round($last['x'], 1).' '.round($baselineY, 1).' Z';

        return $path;
    }

    private static function emptyChart(int $width, int $height): string
    {
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <rect width="100%" height="100%" fill="#f8fafc"/>
  <text x="50%" y="50%" text-anchor="middle" fill="#94a3b8" font-family="sans-serif" font-size="12">No data</text>
</svg>
SVG;

        return self::toDataUri($svg);
    }

    private static function toDataUri(string $svg): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
