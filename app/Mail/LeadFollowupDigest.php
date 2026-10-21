<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LeadFollowupDigest extends Mailable
{
    public function __construct(public User $recipient, public array $items, public string $digestDate) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Tasmiya follow-ups · '.$this->digestDate);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.lead-followup-digest', text: 'mail.lead-followup-digest-text');
    }
}
