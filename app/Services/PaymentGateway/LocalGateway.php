<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class LocalGateway implements PaymentGatewayInterface
{
    protected $gateway;

    public function __construct($gatewayCode = 'local_gateway')
    {
        $this->gateway = PaymentGateway::where('code', $gatewayCode)->first();
    }

    public function createPayment($amount, $currency, $data)
    {
        if (!$this->gateway || !$this->gateway->is_active) {
            return [
                'success' => false,
                'error' => 'Gateway not configured or inactive',
            ];
        }

        try {
            // هنا ستضع API الخاص بالبوابة المحلية
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->gateway->config['api_key'],
            ])->post($this->gateway->config['api_url'] . '/create-payment', [
                'merchant_id' => $this->gateway->config['merchant_id'],
                'amount' => $amount,
                'currency' => $currency,
                'callback_url' => $this->gateway->settings['callback_url'],
                'reference' => $data['reference'] ?? null,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'payment_url' => $response->json('payment_url'),
                    'transaction_id' => $response->json('transaction_id'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Payment creation failed'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyPayment($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->gateway->config['api_key'],
            ])->get($this->gateway->config['api_url'] . '/verify-payment/' . $transactionId);

            if ($response->successful()) {
                return [
                    'success' => $response->json('status') === 'completed',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Verification failed',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment($transactionId, $amount)
    {
        // Implementation for refunds
    }

    public function handleWebhook($payload)
    {
        // Implementation for webhook handling
    }
}
