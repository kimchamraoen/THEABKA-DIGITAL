<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewUserNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $payload)
    {
    }

    public function envelope(): Envelope
    {
        $name = $this->payload['name'] ?? 'New User';

        return new Envelope(
            subject: "New User Registration - {$name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new_user',
            with: [
                'payload' => $this->payload,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
