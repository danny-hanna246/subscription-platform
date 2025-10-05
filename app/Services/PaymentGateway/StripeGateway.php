<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;

class StripeGateway implements PaymentGatewayInterface
{
    protected $gateway;

    public function __construct()
    {
        $this->gateway = PaymentGateway::where('code', 'stripe')->first();

        if ($this->gateway && $this->gateway->is_active) {
            \Stripe\Stripe::setApiKey($this->gateway->config['secret_key']);
        }
    }

    public function createPayment($amount, $currency, $data)
    {
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency),
                        'product_data' => [
                            'name' => $data['product_name'] ?? 'Subscription',
                        ],
                        'unit_amount' => $amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $data['success_url'],
                'cancel_url' => $data['cancel_url'],
                'metadata' => [
                    'subscription_request_id' => $data['subscription_request_id'] ?? null,
                ],
            ]);

            return [
                'success' => true,
                'payment_url' => $session->url,
                'session_id' => $session->id,
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
            $session = \Stripe\Checkout\Session::retrieve($transactionId);

            return [
                'success' => $session->payment_status === 'paid',
                'data' => $session,
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
