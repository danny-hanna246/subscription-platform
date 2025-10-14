<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    protected $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysRemaining = $this->subscription->remainingDays();

        return [
            'type' => 'subscription_expiring',
            'title' => 'اشتراك على وشك الانتهاء',
            'message' => "اشتراك {$this->subscription->customer->name} سينتهي خلال {$daysRemaining} يوم",
            'icon' => 'exclamation-triangle',
            'color' => 'yellow',
            'subscription_id' => $this->subscription->id,
            'customer_name' => $this->subscription->customer->name,
            'customer_email' => $this->subscription->customer->email,
            'plan_name' => $this->subscription->plan->name,
            'product_name' => $this->subscription->plan->product->name,
            'days_remaining' => $daysRemaining,
            'ends_at' => $this->subscription->ends_at->format('Y-m-d'),
            'url' => route('admin.subscriptions.show', $this->subscription->id),
            'action_text' => 'عرض التفاصيل',
        ];
    }
}
