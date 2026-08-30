<?php

namespace App\SpectoraEngine;

class ProbeResult
{
    /**
     * @param  list<string>  $issues
     * @param  array<string, mixed>  $safetyDetails
     */
    public function __construct(
        public readonly bool $ran,
        public readonly ?string $skipReason = null,
        public readonly int $statusCode = 0,
        public readonly ?float $responseTime = null,
        public readonly ?int $sslDays = null,
        public readonly string $safetyStatus = 'safe',
        public readonly array $issues = [],
        public readonly array $safetyDetails = [],
    ) {}
}
