<?php

namespace App\SpectoraEngine\Incidents;

use App\Mail\DomainRecoveryMail;
use App\Mail\DomainWarningMail;
use App\Models\Domain;
use App\Models\User;
use App\Notifications\DomainWarningNotification;
use App\SpectoraEngine\Incidents\Webhooks\DiscordWebhook;
use App\SpectoraEngine\Incidents\Webhooks\SlackWebhook;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertDispatcher
{
    /**
     * @return Collection<int, User>
     */
    public static function recipients(Domain $domain): Collection
    {
        $admins = User::query()->where('is_admin', true)->get();
        $owner = $domain->user;

        if ($owner) {
            $admins->prepend($owner);
        }

        return $admins->unique('id')->values();
    }

    /**
     * Dispatches downtime alerts across all configured channels (Email, WebPush, Webhooks).
     */
    public static function dispatchDowntime(Domain $domain, array $issues, ?string $checkedUrl = null): void
    {
        $sanitizedIssues = self::sanitizeIssues($issues);
        $recipients = self::recipients($domain);

        // 1. Email Alerts
        $emails = $recipients->pluck('email')->unique()->filter()->values();
        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new DomainWarningMail($domain, $sanitizedIssues, $checkedUrl));
            } catch (\Throwable $e) {
                Log::error("Failed to send outage alert mail to {$email}: ".$e->getMessage());
            }
        }

        // 2. Web Push Notifications
        if (config('webpush.vapid.public_key') && config('webpush.vapid.private_key')) {
            foreach ($recipients as $user) {
                if (! $user->pushSubscriptions()->exists()) {
                    continue;
                }
                try {
                    $user->notify(new DomainWarningNotification($domain, $sanitizedIssues, $checkedUrl));
                } catch (\Throwable $e) {
                    Log::error("Failed to send Web Push alert to user {$user->id}: ".$e->getMessage());
                }
            }
        }

        // 3. Webhooks (Domain level or User level)
        $webhookUrl = $domain->webhook_url ?? $domain->user?->webhook_url ?? config('spectora.webhook_url');
        if ($webhookUrl) {
            if (str_contains($webhookUrl, 'discord.com')) {
                DiscordWebhook::sendIncident($webhookUrl, $domain, $sanitizedIssues, $checkedUrl);
            } elseif (str_contains($webhookUrl, 'hooks.slack.com')) {
                SlackWebhook::sendIncident($webhookUrl, $domain, $sanitizedIssues, $checkedUrl);
            }
        }
    }

    /**
     * Dispatches recovery alerts when a domain comes back online.
     */
    public static function dispatchRecovery(Domain $domain, ?string $checkedUrl = null): void
    {
        $recipients = self::recipients($domain);

        // 1. Email Recovery
        $emails = $recipients->pluck('email')->unique()->filter()->values();
        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new DomainRecoveryMail($domain, $checkedUrl));
            } catch (\Throwable $e) {
                Log::error("Failed to send recovery mail to {$email}: ".$e->getMessage());
            }
        }

        // 2. Webhooks Recovery
        $webhookUrl = $domain->webhook_url ?? $domain->user?->webhook_url ?? config('spectora.webhook_url');
        if ($webhookUrl) {
            if (str_contains($webhookUrl, 'discord.com')) {
                DiscordWebhook::sendRecovery($webhookUrl, $domain, $checkedUrl);
            } elseif (str_contains($webhookUrl, 'hooks.slack.com')) {
                SlackWebhook::sendRecovery($webhookUrl, $domain, $checkedUrl);
            }
        }
    }

    private static function sanitizeIssues(array $issues): array
    {
        return array_map(function (string $issue): string {
            if (str_starts_with($issue, '❌ Check failed:')) {
                return '❌ Check failed: The server could not be reached.';
            }

            return $issue;
        }, $issues);
    }
}
