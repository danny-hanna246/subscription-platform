<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
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
        $customerName = $this->payment->subscriptionRequest
            ? $this->payment->subscriptionRequest->customer->name
            : 'عميل';

        return [
            'type' => 'payment_received',
            'title' => 'دفعة جديدة',
            'message' => "تم استلام دفعة بقيمة {$this->payment->amount} {$this->payment->currency} من {$customerName}",
            'icon' => 'currency-dollar',
            'color' => 'green',
            'payment_id' => $this->payment->id,
            'amount' => (float) $this->payment->amount,
            'currency' => $this->payment->currency,
            'gateway' => $this->payment->gateway,
            'customer_name' => $customerName,
            'paid_at' => $this->payment->paid_at ? $this->payment->paid_at->format('Y-m-d H:i') : null,
            'url' => route('admin.payments.index'),
            'action_text' => 'عرض المدفوعات',
        ];
    }
}
