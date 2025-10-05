<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; }
        .content { background: #d1fae5; padding: 20px; }
        .license-box { background: white; border: 2px dashed #10b981; padding: 20px; margin: 20px 0; text-align: center; }
        .license-key { font-size: 24px; font-weight: bold; color: #10b981; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎊 Subscription Approved!</h1>
        </div>

        <div class="content">
            <p>Dear {{ $subscription->customer->name }},</p>

            <p>Great news! Your subscription request has been approved and your payment has been confirmed.</p>

            <div class="license-box">
                <p style="margin: 0; color: #666;">Your License Key</p>
                <p class="license-key">{{ $license->license_key }}</p>
            </div>

            <p><strong>Subscription Details:</strong></p>
            <ul>
                <li>Product: {{ $subscription->plan->product->name }}</li>
                <li>Plan: {{ $subscription->plan->name }}</li>
                <li>Valid until: {{ $subscription->ends_at->format('F d, Y') }}</li>
            </ul>

            <p>You can now start using your subscription!</p>

            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>
