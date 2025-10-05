<?php

namespace App\Services\PaymentGateways;

interface PaymentGatewayInterface
{
    public function createPayment($amount, $currency, $data);
    public function verifyPayment($transactionId);
    public function refundPayment($transactionId, $amount);
    public function handleWebhook($payload);
}
