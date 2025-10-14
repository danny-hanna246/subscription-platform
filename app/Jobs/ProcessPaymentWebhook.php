<?php

namespace App\Jobs;

use App\Models\WebhookLog;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhookLog;

    public function __construct(WebhookLog $webhookLog)
    {
        $this->webhookLog = $webhookLog;
    }

    public function handle(PaymentService $paymentService): void
    {
        try {
            // معالجة الـ webhook حسب المصدر
            if ($this->webhookLog->source === 'stripe') {
                // معالجة Stripe
            } elseif ($this->webhookLog->source === 'paypal') {
                // معالجة PayPal
            }

            $this->webhookLog->markAsProcessed();
        } catch (\Exception $e) {
            $this->webhookLog->markAsFailed($e->getMessage());
            throw $e; // لإعادة المحاولة
        }
    }
}