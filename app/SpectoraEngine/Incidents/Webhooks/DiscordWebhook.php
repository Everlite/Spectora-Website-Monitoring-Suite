<?php

namespace App\SpectoraEngine\Incidents\Webhooks;

use App\Models\Domain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordWebhook
{
    public static function sendIncident(string $webhookUrl, Domain $domain, array $issues, ?string $checkedUrl = null): bool
    {
        $targetUrl = $checkedUrl ?? $domain->url;
        $description = implode("\n", array_map(fn ($i) => "• ".strip_tags($i), $issues));

        $payload = [
            'username' => 'Spectora Engine',
            'avatar_url' => rtrim((string) config('app.url'), '/').'/images/logo.png',
            'embeds' => [
                [
                    'title' => '🚨 Outage Alert: '.$domain->url,
                    'url' => rtrim((string) config('app.url'), '/').'/domains/'.$domain->uuid,
                    'color' => 0xE11D48, // Rose Red
                    'fields' => [
                        [
                            'name' => 'Target URL',
                            'value' => $targetUrl,
                            'inline' => true,
                        ],
                        [
                            'name' => 'Detected Issues',
                            'value' => mb_substr($description, 0, 1024),
                            'inline' => false,
                        ],
                    ],
                    'timestamp' => now()->toIso8601String(),
                    'footer' => [
                        'text' => 'Spectora Incident State Machine',
                    ],
                ],
            ],
        ];

        try {
            $res = Http::timeout(5)->post($webhookUrl, $payload);
            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Discord webhook delivery failed for {$domain->url}: ".$e->getMessage());
            return false;
        }
    }

    public static function sendRecovery(string $webhookUrl, Domain $domain, ?string $checkedUrl = null): bool
    {
        $targetUrl = $checkedUrl ?? $domain->url;

        $payload = [
            'username' => 'Spectora Engine',
            'avatar_url' => rtrim((string) config('app.url'), '/').'/images/logo.png',
            'embeds' => [
                [
                    'title' => '✅ Recovery Resolved: '.$domain->url,
                    'url' => rtrim((string) config('app.url'), '/').'/domains/'.$domain->uuid,
                    'color' => 0x10B981, // Emerald Green
                    'description' => 'The website has successfully recovered and is responding with healthy HTTP status codes.',
                    'fields' => [
                        [
                            'name' => 'Target URL',
                            'value' => $targetUrl,
                            'inline' => true,
                        ],
                    ],
                    'timestamp' => now()->toIso8601String(),
                    'footer' => [
                        'text' => 'Spectora Incident State Machine',
                    ],
                ],
            ],
        ];

        try {
            $res = Http::timeout(5)->post($webhookUrl, $payload);
            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Discord recovery webhook delivery failed for {$domain->url}: ".$e->getMessage());
            return false;
        }
    }
}
