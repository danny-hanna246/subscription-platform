<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('api:cleanup-logs --days=30')
            ->daily()
            ->at('02:00')
            ->onOneServer();

        $schedule->command('api:notify-expiring-keys --days=7')
            ->daily()
            ->at('09:00')
            ->onOneServer();

        $schedule->command('subscriptions:check-expired')
            ->hourly()
            ->onOneServer();

        $schedule->command('subscriptions:notify-expiring')
            ->daily()
            ->at('10:00')
            ->onOneServer();
    }
}
