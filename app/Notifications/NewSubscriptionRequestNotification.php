<?php

namespace App\Notifications;

use App\Models\SubscriptionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSubscriptionRequestNotification extends Notification
{
    use Queueable;

    protected $subscriptionRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(SubscriptionRequest $subscriptionRequest)
    {
        $this->subscriptionRequest = $subscriptionRequest;
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
            'type' => 'new_subscription_request',
            'title' => 'طلب اشتراك جديد',
            'message' => "طلب جديد من {$this->subscriptionRequest->customer->name}",
            'icon' => 'document',
            'color' => 'blue',
            'subscription_request_id' => $this->subscriptionRequest->id,
            'customer_name' => $this->subscriptionRequest->customer->name,
            'customer_email' => $this->subscriptionRequest->customer->email,
            'plan_name' => $this->subscriptionRequest->plan->name,
            'product_name' => $this->subscriptionRequest->plan->product->name,
            'amount' => (float) $this->subscriptionRequest->amount,
            'currency' => $this->subscriptionRequest->currency,
            'payment_method' => $this->subscriptionRequest->payment_method,
            'url' => route('admin.subscription-requests.show', $this->subscriptionRequest->id),
            'action_text' => 'عرض الطلب',
        ];
    }
}
