<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            // Stripe
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'type' => 'international',
                'description' => 'Accept credit cards, debit cards, and other payment methods',
                'config' => [
                    'public_key' => env('STRIPE_PUBLIC_KEY', ''),
                    'secret_key' => env('STRIPE_SECRET_KEY', ''),
                    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'supported_currencies' => ['USD', 'EUR', 'GBP', 'SAR', 'AED'],
                'settings' => [
                    'payment_methods' => ['card', 'apple_pay', 'google_pay'],
                ],
            ],

            // PayPal
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'type' => 'international',
                'description' => 'Accept PayPal payments',
                'config' => [
                    'client_id' => env('PAYPAL_CLIENT_ID', ''),
                    'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
                    'mode' => env('PAYPAL_MODE', 'sandbox'),
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'supported_currencies' => ['USD', 'EUR', 'GBP'],
                'settings' => [
                    'return_url' => url('/payment/paypal/success'),
                    'cancel_url' => url('/payment/paypal/cancel'),
                ],
            ],

            // Syriatel Cash (بوابة دفع محلية سورية)
            [
                'name' => 'Syriatel Cash',
                'code' => 'syriatel_cash',
                'type' => 'local',
                'description' => 'الدفع عبر محفظة سيرياتيل كاش',
                'config' => [
                    'merchant_id' => env('SYRIATEL_MERCHANT_ID', ''),
                    'api_key' => env('SYRIATEL_API_KEY', ''),
                    'api_url' => env('SYRIATEL_API_URL', ''),
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'supported_currencies' => ['SYP'],
                'settings' => [
                    'callback_url' => url('/payment/syriatel/callback'),
                ],
            ],

            // MTN Cash (بوابة دفع محلية سورية)
            [
                'name' => 'MTN Cash',
                'code' => 'mtn_cash',
                'type' => 'local',
                'description' => 'الدفع عبر محفظة MTN كاش',
                'config' => [
                    'merchant_id' => env('MTN_MERCHANT_ID', ''),
                    'api_key' => env('MTN_API_KEY', ''),
                    'api_url' => env('MTN_API_URL', ''),
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'supported_currencies' => ['SYP'],
                'settings' => [
                    'callback_url' => url('/payment/mtn/callback'),
                ],
            ],

            // بوابة دفع محلية عامة (قابلة للتخصيص)
            [
                'name' => 'Local Payment Gateway',
                'code' => 'local_gateway',
                'type' => 'local',
                'description' => 'بوابة دفع محلية قابلة للتخصيص',
                'config' => [
                    'api_url' => env('LOCAL_GATEWAY_API_URL', ''),
                    'api_key' => env('LOCAL_GATEWAY_API_KEY', ''),
                    'merchant_id' => env('LOCAL_GATEWAY_MERCHANT_ID', ''),
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'supported_currencies' => ['SYP'],
                'settings' => [
                    'callback_url' => url('/payment/local/callback'),
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::create($gateway);
        }
    }
}
