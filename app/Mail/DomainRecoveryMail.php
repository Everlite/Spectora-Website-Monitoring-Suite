<?php

namespace App\Mail;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DomainRecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Domain $domain,
        public ?string $checkedUrl = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Resolved: '.$this->domain->url.' is back online',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.domain-recovery',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
