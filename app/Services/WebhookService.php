<?php
// app/Services/WebhookService.php

namespace App\Services;

use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * إرسال webhook
     */
    public function send($apiKeyId, $event, $data)
    {
        $webhooks = Webhook::where('api_key_id', $apiKeyId)
            ->where('status', 'active')
            ->get();

        foreach ($webhooks as $webhook) {
            if ($webhook->hasEvent($event)) {
                $this->sendWebhook($webhook, $event, $data);
            }
        }
    }

    /**
     * إرسال webhook لعنوان محدد
     */
    protected function sendWebhook(Webhook $webhook, $event, $data)
    {
        try {
            $payload = [
                'event' => $event,
                'data' => $data,
                'timestamp' => now()->toIso8601String(),
            ];

            $signature = $this->generateSignature($payload, $webhook->secret);

            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Webhook-Event' => $event,
                    'X-Webhook-Signature' => $signature,
                    'User-Agent' => 'SubscriptionSystem-Webhook/1.0',
                ])
                ->post($webhook->url, $payload);

            if ($response->successful()) {
                $webhook->update([
                    'last_triggered_at' => now(),
                    'failed_attempts' => 0,
                    'status' => 'active',
                ]);

                Log::info('Webhook sent successfully', [
                    'webhook_id' => $webhook->id,
                    'event' => $event,
                    'url' => $webhook->url,
                ]);
            } else {
                $this->handleWebhookFailure($webhook, $response->status());
            }
        } catch (\Exception $e) {
            $this->handleWebhookFailure($webhook, 0, $e->getMessage());
        }
    }

    /**
     * معالجة فشل الـ Webhook
     */
    protected function handleWebhookFailure(Webhook $webhook, $statusCode, $error = null)
    {
        $webhook->increment('failed_attempts');

        // إذا فشل أكثر من 5 مرات، قم بإيقافه
        if ($webhook->failed_attempts >= 5) {
            $webhook->update(['status' => 'failed']);
        }

        Log::error('Webhook failed', [
            'webhook_id' => $webhook->id,
            'url' => $webhook->url,
            'status_code' => $statusCode,
            'error' => $error,
            'failed_attempts' => $webhook->failed_attempts,
        ]);
    }

    /**
     * توليد توقيع للـ Webhook
     */
    protected function generateSignature($payload, $secret)
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }

    /**
     * التحقق من توقيع الـ Webhook
     */
    public function verifySignature($payload, $signature, $secret)
    {
        $expectedSignature = $this->generateSignature($payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
