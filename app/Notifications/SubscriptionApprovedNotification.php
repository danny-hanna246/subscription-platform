<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionApprovedNotification extends Notification
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
        return [
            'type' => 'subscription_approved',
            'title' => 'تم الموافقة على طلب اشتراك',
            'message' => "تم الموافقة على طلب {$this->subscription->customer->name} وتفعيل الاشتراك",
            'icon' => 'check-circle',
            'color' => 'green',
            'subscription_id' => $this->subscription->id,
            'customer_name' => $this->subscription->customer->name,
            'plan_name' => $this->subscription->plan->name,
            'product_name' => $this->subscription->plan->product->name,
            'starts_at' => $this->subscription->starts_at->format('Y-m-d'),
            'ends_at' => $this->subscription->ends_at->format('Y-m-d'),
            'url' => route('admin.subscriptions.show', $this->subscription->id),
            'action_text' => 'عرض الاشتراك',
        ];
    }
}
