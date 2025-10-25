<?php
// app/Notifications/ApiKeyExpiringNotification.php

namespace App\Notifications;

use App\Models\ApiKey;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApiKeyExpiringNotification extends Notification
{
    use Queueable;

    protected $apiKey;

    /**
     * Create a new notification instance.
     */
    public function __construct(ApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
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
        $daysRemaining = $this->apiKey->daysUntilExpiry();

        return [
            'type' => 'api_key_expiring',
            'title' => 'مفتاح API على وشك الانتهاء',
            'message' => "مفتاح API '{$this->apiKey->client_name}' سينتهي خلال {$daysRemaining} يوم",
            'icon' => 'key',
            'color' => 'orange',
            'api_key_id' => $this->apiKey->id,
            'client_name' => $this->apiKey->client_name,
            'days_remaining' => $daysRemaining,
            'expires_at' => $this->apiKey->expires_at ? $this->apiKey->expires_at->format('Y-m-d H:i') : null,
            'scopes' => $this->apiKey->scopes,
            'last_used_at' => $this->apiKey->last_used_at ? $this->apiKey->last_used_at->format('Y-m-d H:i') : 'لم يُستخدم بعد',
            'usage_count' => $this->apiKey->usage_count,
            'url' => route('admin.api-keys.index'),
            'action_text' => 'عرض المفاتيح',
        ];
    }
}
