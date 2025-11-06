<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ============================================
        // 1. التحديث التلقائي للاشتراكات المنتهية
        // ============================================
        // يعمل يومياً الساعة 00:00 (منتصف الليل)
        // يقوم بتحديث حالة الاشتراكات المنتهية من "active" إلى "expired"
        // وتحديث حالة التراخيص المرتبطة بها
        $schedule->call(function () {
            $service = app(\App\Services\SubscriptionService::class);
            $count = $service->checkAndUpdateExpiredSubscriptions();

            \Illuminate\Support\Facades\Log::info("Auto-updated {$count} expired subscriptions");
        })->daily()->at('00:00')->name('update-expired-subscriptions');

        // ============================================
        // 2. إرسال تذكير للاشتراكات التي ستنتهي قريباً
        // ============================================
        // يعمل يومياً الساعة 09:00 صباحاً
        // يرسل إيميل للعملاء الذين ستنتهي اشتراكاتهم خلال 7 أيام
        $schedule->call(function () {
            $subscriptions = \App\Models\Subscription::where('status', 'active')
                ->whereBetween('ends_at', [now(), now()->addDays(7)])
                ->whereDoesntHave('notifications', function ($query) {
                    $query->where('type', 'expiring_reminder')
                        ->where('created_at', '>=', now()->subDays(7));
                })
                ->get();

            foreach ($subscriptions as $subscription) {
                \Illuminate\Support\Facades\Mail::to($subscription->customer->email)
                    ->queue(new \App\Mail\SubscriptionExpiringMail($subscription));

                // تسجيل أنه تم إرسال التذكير
                \Illuminate\Support\Facades\Log::info("Expiring reminder sent to {$subscription->customer->email}");
            }

            \Illuminate\Support\Facades\Log::info("Sent {$subscriptions->count()} expiring reminders");
        })->daily()->at('09:00')->name('send-expiring-reminders');

        // ============================================
        // 3. تنظيف السجلات القديمة (اختياري)
        // ============================================
        // يعمل أسبوعياً كل يوم أحد الساعة 02:00
        // يحذف سجلات الـ Validation Logs و API Access Logs الأقدم من 90 يوم
        $schedule->call(function () {
            $deletedValidation = \App\Models\ValidationLog::where('created_at', '<', now()->subDays(90))->delete();
            $deletedApiAccess = \App\Models\ApiAccessLog::where('created_at', '<', now()->subDays(90))->delete();

            \Illuminate\Support\Facades\Log::info("Cleaned {$deletedValidation} validation logs and {$deletedApiAccess} API access logs");
        })->weekly()->sundays()->at('02:00')->name('cleanup-old-logs');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
