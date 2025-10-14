<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'license_key_delivery',
                'subject' => 'Your License Key - {product_name}',
                'body_html' => '<h1>Your License Key</h1><p>Dear {customer_name},</p><p>License Key: <strong>{license_key}</strong></p>',
                'body_text' => 'Your License Key: {license_key}',
                'variables' => ['customer_name', 'license_key', 'product_name'],
                'is_active' => true,
            ],
            [
                'name' => 'subscription_expiring',
                'subject' => 'Your Subscription is Expiring Soon',
                'body_html' => '<h1>Subscription Expiring</h1><p>Your subscription will expire in {days_remaining} days.</p>',
                'body_text' => 'Your subscription expires in {days_remaining} days',
                'variables' => ['customer_name', 'days_remaining'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
