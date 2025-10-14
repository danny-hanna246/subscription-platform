<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    public function run(): void
    {
        // API Key للتطوير والاختبار
        ApiKey::create([
            'client_name' => 'Development Testing',
            'api_key' => 'sk_test_' . bin2hex(random_bytes(20)),
            'secret_hash' => hash('sha256', 'test_secret'),
            'allowed_ips' => null, // السماح من أي IP
            'scopes' => ['integration', 'admin', 'validate_license'],
            'status' => 'active',
        ]);

        // API Key للـ Integration فقط
        ApiKey::create([
            'client_name' => 'Website Integration',
            'api_key' => 'sk_integration_' . bin2hex(random_bytes(20)),
            'secret_hash' => hash('sha256', 'integration_secret'),
            'allowed_ips' => null,
            'scopes' => ['integration'],
            'status' => 'active',
        ]);
    }
}
