<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // تنظيف API Logs القديمة يومياً في الساعة 2 صباحاً
        $schedule->command('api:cleanup-logs --days=30')
            ->daily()
            ->at('02:00');

        // إشعار بانتهاء صلاحية API Keys يومياً في الساعة 9 صباحاً
        $schedule->command('api:notify-expiring-keys --days=7')
            ->daily()
            ->at('09:00');

        // تنظيف الاشتراكات المنتهية
        $schedule->command('subscriptions:expire')
            ->daily()
            ->at('01:00');

        // تذكير بتجديد الاشتراكات
        $schedule->command('subscriptions:notify-expiring')
            ->daily()
            ->at('10:00');
    }
}
