<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .license-box {
            background: white;
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .license-key {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        .info-box {
            background: white;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Your License Key is Ready!</h1>
    </div>

    <div class="content">
        <p>Dear {{ $customer->name }},</p>

        <p>Thank you for your subscription! Your license has been successfully activated.</p>

        <div class="license-box">
            <p style="margin: 0; color: #666; font-size: 14px;">Your License Key</p>
            <p class="license-key">{{ $license->license_key }}</p>
            <p style="margin: 0; color: #666; font-size: 12px;">Keep this key safe and secure</p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">📦 Subscription Details</h3>
            <p><strong>Product:</strong> {{ $plan->product->name }}</p>
            <p><strong>Plan:</strong> {{ $plan->name }}</p>
            <p><strong>Start Date:</strong> {{ $subscription->starts_at->format('F d, Y') }}</p>
            <p><strong>Expiry Date:</strong> {{ $subscription->ends_at->format('F d, Y') }}</p>
            <p><strong>Device Limit:</strong> {{ $plan->device_limit }} device(s)</p>
            <p><strong>User Limit:</strong> {{ $plan->user_limit }} user(s)</p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">🚀 Getting Started</h3>
            <ol>
                <li>Download and install the application</li>
                <li>Enter your license key when prompted</li>
                <li>Start using all the features!</li>
            </ol>
        </div>

        <p style="text-align: center;">
            <a href="{{ url('/') }}" class="button">Visit Dashboard</a>
        </p>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
