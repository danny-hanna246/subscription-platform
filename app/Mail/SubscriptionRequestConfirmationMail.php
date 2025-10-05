<?php

namespace App\Mail;

use App\Models\SubscriptionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRequestConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct(SubscriptionRequest $request)
    {
        $this->request = $request;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Request Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-request-confirmation',
            with: ['request' => $this->request],
        );
    }
}
