<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyAgencyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $stats;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $stats)
    {
        $this->user = $user;
        $this->stats = $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $month = now()->subMonth()->translatedFormat('F');

        return new Envelope(
            from: new Address(config('mail.from.address'), 'Spectora Monitoring'),
            subject: "Your Monthly Report for {$month}: {$this->stats['total']} Domains Checked",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reports.monthly_agency',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
