<?php

namespace App\SpectoraEngine\Watchdog;

class WatchdogResult
{
    /**
     * @param  string  $status  'safe', 'warning', 'danger', 'error'
     * @param  list<array<string, mixed>>  $issues  Detected threat issues
     * @param  array{critical: int, warning: int, info: int}  $summary  Issue counts by severity
     */
    public function __construct(
        public readonly string $status,
        public readonly array $issues,
        public readonly array $summary
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'issues' => $this->issues,
            'summary' => $this->summary,
        ];
    }

    public function isDangerous(): bool
    {
        return $this->status === 'danger' || $this->summary['critical'] > 0;
    }

    public function hasWarnings(): bool
    {
        return $this->status === 'warning' || $this->summary['warning'] > 0;
    }
}
