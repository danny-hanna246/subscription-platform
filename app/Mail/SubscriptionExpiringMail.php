<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Subscription is Expiring Soon',
        );
    }

    public function content(): Content
    {
        $template = EmailTemplate::where('name', 'subscription_expiring')
            ->where('is_active', true)
            ->first();

        if ($template) {
            $daysRemaining = $this->subscription->remainingDays();

            $renderedTemplate = $template->render([
                'customer_name' => $this->subscription->customer->name,
                'product_name' => $this->subscription->plan->product->name,
                'plan_name' => $this->subscription->plan->name,
                'expires_at' => $this->subscription->ends_at->format('Y-m-d'),
                'days_remaining' => $daysRemaining,
                'renewal_url' => url('/subscriptions/' . $this->subscription->id . '/renew'),
            ]);

            return new Content(
                htmlString: $renderedTemplate['body'],
            );
        }

        return new Content(
            view: 'emails.subscription-expiring',
            with: ['subscription' => $this->subscription],
        );
    }
}
