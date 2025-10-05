<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Mail\SubscriptionExpiringMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendExpiringSubscriptionReminders extends Command
{
    protected $signature = 'subscriptions:send-expiring-reminders';
    protected $description = 'Send email reminders for subscriptions expiring soon';

    public function handle()
    {
        $this->info('Checking for expiring subscriptions...');

        // الاشتراكات التي ستنتهي خلال 7 أيام
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '<=', Carbon::now()->addDays(7))
            ->where('ends_at', '>', Carbon::now())
            ->with(['customer', 'plan.product'])
            ->get();

        $count = 0;
        foreach ($expiringSubscriptions as $subscription) {
            Mail::to($subscription->customer->email)
                ->queue(new SubscriptionExpiringMail($subscription));
            $count++;
        }

        $this->info("Sent {$count} expiring subscription reminder(s)");

        return 0;
    }
}
