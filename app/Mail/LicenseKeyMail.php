<?php

namespace App\Mail;

use App\Models\License;
use App\Models\Subscription;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseKeyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $license;
    public $subscription;
    public $customer;
    public $plan;

    public function __construct(License $license)
    {
        $this->license = $license;
        $this->subscription = $license->subscription;
        $this->customer = $this->subscription->customer;
        $this->plan = $this->subscription->plan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your License Key - ' . $this->plan->product->name,
        );
    }

    public function content(): Content
    {
        // محاولة استخدام EmailTemplate إن وجد
        $template = EmailTemplate::where('name', 'license_key_delivery')
            ->where('is_active', true)
            ->first();

        if ($template) {
            $renderedTemplate = $template->render([
                'customer_name' => $this->customer->name,
                'license_key' => $this->license->license_key,
                'product_name' => $this->plan->product->name,
                'plan_name' => $this->plan->name,
                'expires_at' => $this->license->expires_at?->format('Y-m-d'),
                'starts_at' => $this->subscription->starts_at->format('Y-m-d'),
                'device_limit' => $this->plan->device_limit,
                'user_limit' => $this->plan->user_limit,
            ]);

            return new Content(
                htmlString: $renderedTemplate['body'],
            );
        }

        // استخدام view افتراضي
        return new Content(
            view: 'emails.license-key',
            with: [
                'license' => $this->license,
                'subscription' => $this->subscription,
                'customer' => $this->customer,
                'plan' => $this->plan,
            ],
        );
    }
}
