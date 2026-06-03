<?php

namespace App\Services;

use App\Mail\DomainWarningMail;
use App\Models\Domain;
use App\Models\User;
use App\Notifications\DomainWarningNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DomainAlertService
{
    /**
     * Domain owner plus all administrators (unique by user id).
     *
     * @return Collection<int, User>
     */
    public static function recipients(Domain $domain): Collection
    {
        $users = User::query()->where('is_admin', true)->get();

        $owner = $domain->user;
        if ($owner) {
            $users->prepend($owner);
        }

        return $users->unique('id')->values();
    }

    /**
     * Send downtime e-mail and Web Push to alert recipients.
     */
    public static function sendDowntimeAlerts(Domain $domain, array $issues, ?string $checkedUrl = null): void
    {
        $issues = self::sanitizeIssuesForNotification($issues);
        $recipients = self::recipients($domain);

        $emails = $recipients->pluck('email')->unique()->filter()->values();

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new DomainWarningMail($domain, $issues, $checkedUrl));
            } catch (\Exception $e) {
                Log::error('Failed to send domain warning mail to '.$email.': '.$e->getMessage());
            }
        }

        if (! config('webpush.vapid.public_key') || ! config('webpush.vapid.private_key')) {
            return;
        }

        foreach ($recipients as $user) {
            if (! $user->pushSubscriptions()->exists()) {
                continue;
            }

            try {
                $user->notify(new DomainWarningNotification($domain, $issues, $checkedUrl));
            } catch (\Exception $e) {
                Log::error('Failed to send Web Push to user '.$user->id.': '.$e->getMessage());
            }
        }
    }

    /**
     * Strip internal exception details from outbound alert text.
     *
     * @param  list<string>  $issues
     * @return list<string>
     */
    public static function sanitizeIssuesForNotification(array $issues): array
    {
        return array_map(function (string $issue): string {
            if (str_starts_with($issue, '❌ Check failed:')) {
                return '❌ Check failed: The site could not be reached.';
            }

            return $issue;
        }, $issues);
    }
}
