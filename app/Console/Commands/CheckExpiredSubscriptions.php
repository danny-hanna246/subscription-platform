<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SubscriptionService;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check and update expired subscriptions';

    public function handle(SubscriptionService $subscriptionService)
    {
        $this->info('Checking for expired subscriptions...');

        $count = $subscriptionService->checkAndUpdateExpiredSubscriptions();

        $this->info("Updated {$count} expired subscription(s)");

        return 0;
    }
}
