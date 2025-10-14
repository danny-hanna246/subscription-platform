<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'type' => 'international',
                'description' => 'Accept credit cards worldwide',
                'config' => [
                    'public_key' => env('STRIPE_PUBLIC_KEY', ''),
                    'secret_key' => env('STRIPE_SECRET_KEY', ''),
                ],
                'is_active' => false, // سيفعل لاحقاً
                'is_test_mode' => true,
                'supported_currencies' => ['USD', 'EUR', 'GBP'],
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'type' => 'international',
                'description' => 'Accept PayPal payments',
                'config' => [
                    'client_id' => env('PAYPAL_CLIENT_ID', ''),
                    'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'supported_currencies' => ['USD', 'EUR'],
            ],
            [
                'name' => 'Cash Payment',
                'code' => 'cash',
                'type' => 'local',
                'description' => 'Accept cash payment at office',
                'config' => [],
                'is_active' => true,
                'is_test_mode' => false,
                'supported_currencies' => ['USD', 'SYP', 'SAR'],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::create($gateway);
        }
    }
}
