<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserLoginNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $payload)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert: New Login Detected',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user.login_notification',
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
