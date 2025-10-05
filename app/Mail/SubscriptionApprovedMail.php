<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $license;

    public function __construct(Subscription $subscription, License $license)
    {
        $this->subscription = $subscription;
        $this->license = $license;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Subscription has been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-approved',
            with: [
                'subscription' => $this->subscription,
                'license' => $this->license,
            ],
        );
    }
}
