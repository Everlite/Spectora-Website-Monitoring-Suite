<?php

namespace App\SpectoraEngine\Audit;

class AuditResult
{
    /**
     * @param  int  $score  Score between 0 and 100
     * @param  string  $grade  Letter grade: A+, A, B, C, D, F
     * @param  array<string, mixed>  $metrics  Key metrics (ttfb_ms, size_bytes, etc.)
     * @param  list<array<string, mixed>>  $findings  List of structured findings
     */
    public function __construct(
        public readonly int $score,
        public readonly string $grade,
        public readonly array $metrics,
        public readonly array $findings
    ) {}

    /**
     * Converts result to array format compatible with legacy views and database.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'grade' => $this->grade,
            'metrics' => $this->metrics,
            'findings' => $this->findings,
            'details' => $this->findings, // Legacy compatibility
        ];
    }

    /**
     * Filter findings by status.
     *
     * @param  string  $status  'error', 'warning', 'success', 'info'
     * @return list<array<string, mixed>>
     */
    public function getFindingsByStatus(string $status): array
    {
        return array_values(array_filter($this->findings, fn ($f) => ($f['status'] ?? '') === $status));
    }
}
