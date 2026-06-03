<?php

namespace App\Notifications;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class DomainWarningNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Domain $domain,
        public array $issues
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $host = parse_url($this->domain->url, PHP_URL_HOST) ?: $this->domain->url;
        $summary = implode('; ', array_slice($this->issues, 0, 2));

        return (new WebPushMessage)
            ->title('Spectora: '.$host.' needs attention')
            ->body(mb_substr($summary, 0, 200))
            ->data([
                'url' => route('domains.show', $this->domain),
            ])
            ->options(['TTL' => 3600]);
    }
}
