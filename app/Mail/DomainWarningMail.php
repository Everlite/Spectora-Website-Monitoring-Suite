<?php

namespace App\Mail;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DomainWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $domain;

    public $issues;

    public ?string $checkedUrl = null;

    public function __construct(Domain $domain, array $issues, ?string $checkedUrl = null)
    {
        $this->domain = $domain;
        $this->issues = $issues;
        $this->checkedUrl = $checkedUrl;
    }

    public function build()
    {
        $label = $this->checkedUrl ?? $this->domain->url;

        return $this->subject('⚠️ Spectora Monitor Warning: '.$label)
            ->view('emails.domain_warning');
    }
}
