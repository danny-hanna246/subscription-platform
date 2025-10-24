<?php
// app/Console/Commands/NotifyExpiringApiKeys.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApiKeyExpiringNotification;
use App\Models\Admin;

class NotifyExpiringApiKeys extends Command
{
    protected $signature = 'api:notify-expiring-keys {--days=7}';
    protected $description = 'Notify admins about expiring API keys';

    public function handle()
    {
        $days = $this->option('days');

        $expiringKeys = ApiKey::expiringSoon($days)->get();

        if ($expiringKeys->isEmpty()) {
            $this->info('No expiring API keys found.');
            return Command::SUCCESS;
        }

        $admins = Admin::where('is_active', true)->get();

        foreach ($expiringKeys as $key) {
            $this->info("API Key '{$key->client_name}' expires in {$key->daysUntilExpiry()} days");

            Notification::send($admins, new ApiKeyExpiringNotification($key));
        }

        $this->info("Notified admins about {$expiringKeys->count()} expiring API keys.");

        return Command::SUCCESS;
    }
}
