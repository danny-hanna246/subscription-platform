<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionCancelledNotification extends Notification
{
    use Queueable;

    protected $subscription;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription, ?string $reason = null)
    {
        $this->subscription = $subscription;
        $this->reason = $reason;
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
        return [
            'type' => 'subscription_cancelled',
            'title' => 'تم إلغاء اشتراك',
            'message' => "تم إلغاء اشتراك {$this->subscription->customer->name}",
            'icon' => 'x-circle',
            'color' => 'red',
            'subscription_id' => $this->subscription->id,
            'customer_name' => $this->subscription->customer->name,
            'customer_email' => $this->subscription->customer->email,
            'plan_name' => $this->subscription->plan->name,
            'product_name' => $this->subscription->plan->product->name,
            'reason' => $this->reason,
            'cancelled_at' => now()->format('Y-m-d H:i'),
            'url' => route('admin.subscriptions.show', $this->subscription->id),
            'action_text' => 'عرض التفاصيل',
        ];
    }
}
