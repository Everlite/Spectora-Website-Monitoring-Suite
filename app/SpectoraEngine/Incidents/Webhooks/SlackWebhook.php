<?php

namespace App\SpectoraEngine\Incidents\Webhooks;

use App\Models\Domain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackWebhook
{
    public static function sendIncident(string $webhookUrl, Domain $domain, array $issues, ?string $checkedUrl = null): bool
    {
        $targetUrl = $checkedUrl ?? $domain->url;
        $description = implode("\n", array_map(fn ($i) => "• ".strip_tags($i), $issues));

        $payload = [
            'text' => "🚨 *Outage Alert:* {$domain->url} is DOWN\n*Target:* {$targetUrl}\n*Issues:*\n{$description}",
            'attachments' => [
                [
                    'color' => 'danger',
                    'title' => 'Open Spectora Dashboard',
                    'title_link' => rtrim((string) config('app.url'), '/').'/domains/'.$domain->uuid,
                ],
            ],
        ];

        try {
            $res = Http::timeout(5)->post($webhookUrl, $payload);
            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Slack webhook delivery failed for {$domain->url}: ".$e->getMessage());
            return false;
        }
    }

    public static function sendRecovery(string $webhookUrl, Domain $domain, ?string $checkedUrl = null): bool
    {
        $targetUrl = $checkedUrl ?? $domain->url;

        $payload = [
            'text' => "✅ *Recovery Resolved:* {$domain->url} is BACK ONLINE ({$targetUrl})",
            'attachments' => [
                [
                    'color' => 'good',
                    'title' => 'Open Spectora Dashboard',
                    'title_link' => rtrim((string) config('app.url'), '/').'/domains/'.$domain->uuid,
                ],
            ],
        ];

        try {
            $res = Http::timeout(5)->post($webhookUrl, $payload);
            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Slack recovery webhook delivery failed for {$domain->url}: ".$e->getMessage());
            return false;
        }
    }
}
