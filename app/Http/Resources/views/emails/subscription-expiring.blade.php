<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f59e0b; color: white; padding: 20px; text-align: center; }
        .content { background: #fef3c7; padding: 20px; }
        .warning { background: white; border-left: 4px solid #f59e0b; padding: 15px; margin: 15px 0; }
        .button { display: inline-block; padding: 12px 30px; background: #f59e0b; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Subscription Expiring Soon</h1>
        </div>

        <div class="content">
            <p>Dear {{ $subscription->customer->name }},</p>

            <div class="warning">
                <p><strong>Your subscription will expire in {{ $subscription->remainingDays() }} day(s)</strong></p>
                <p>Product: {{ $subscription->plan->product->name }}</p>
                <p>Plan: {{ $subscription->plan->name }}</p>
                <p>Expiry Date: {{ $subscription->ends_at->format('F d, Y') }}</p>
            </div>

            <p>To continue enjoying uninterrupted access, please renew your subscription before it expires.</p>

            <p style="text-align: center;">
                <a href="{{ url('/subscriptions/' . $subscription->id . '/renew') }}" class="button">Renew Now</a>
            </p>

            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>
