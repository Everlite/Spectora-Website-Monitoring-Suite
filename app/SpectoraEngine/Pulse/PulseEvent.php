<?php

namespace App\SpectoraEngine\Pulse;

class PulseEvent
{
    public function __construct(
        public readonly string $domainUuid,
        public readonly string $url,
        public readonly ?string $path = null,
        public readonly ?string $referrer = null,
        public readonly ?int $width = null,
        public readonly ?string $eventType = 'pageview',
        public readonly ?string $eventName = null,
        public readonly ?array $eventData = null,
    ) {}

    public static function fromRequest(array $validated): self
    {
        $path = parse_url($validated['url'], PHP_URL_PATH) ?? '/';

        return new self(
            domainUuid: $validated['domain'],
            url: $validated['url'],
            path: $path,
            referrer: $validated['referrer'] ?? null,
            width: isset($validated['width']) ? (int) $validated['width'] : null,
            eventType: $validated['event_type'] ?? 'pageview',
            eventName: $validated['event_name'] ?? null,
            eventData: $validated['event_data'] ?? null,
        );
    }
}
