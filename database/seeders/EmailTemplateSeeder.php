<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'welcome_email',
                'subject' => 'Welcome to {app_name}',
                'body_html' => '<h1>Welcome {customer_name}!</h1><p>Thank you for subscribing.</p>',
                'body_text' => 'Welcome {customer_name}! Thank you for subscribing.',
                'variables' => ['customer_name', 'app_name'],
                'is_active' => true,
            ],
            [
                'name' => 'license_key_delivery',
                'subject' => 'Your License Key - {product_name}',
                'body_html' => '<h1>Your License Key</h1><p>Dear {customer_name},</p><p>Your license key: <strong>{license_key}</strong></p><p>Expires: {expires_at}</p>',
                'body_text' => 'Dear {customer_name}, Your license key: {license_key}. Expires: {expires_at}',
                'variables' => ['customer_name', 'license_key', 'product_name', 'expires_at'],
                'is_active' => true,
            ],
            [
                'name' => 'subscription_expiring',
                'subject' => 'Your Subscription is Expiring Soon',
                'body_html' => '<h1>Subscription Expiring</h1><p>Dear {customer_name},</p><p>Your subscription will expire on {expires_at}.</p>',
                'body_text' => 'Dear {customer_name}, Your subscription will expire on {expires_at}.',
                'variables' => ['customer_name', 'expires_at'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
