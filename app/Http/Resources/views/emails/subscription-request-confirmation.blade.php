<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { background: #eff6ff; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Request Received</h1>
        </div>

        <div class="content">
            <p>Dear {{ $request->customer->name }},</p>

            <p>We have received your subscription request for <strong>{{ $request->plan->product->name }}</strong> ({{ $request->plan->name }}).</p>

            @if($request->payment_method === 'cash')
                <p><strong>Payment Method:</strong> Cash Payment</p>
                <p>Your request is pending approval. Our team will review it and notify you once it's approved.</p>
                <p>Please visit our office to complete the payment process.</p>
            @else
                <p><strong>Payment Method:</strong> Online Payment</p>
                <p>Please complete the payment to activate your subscription.</p>
            @endif

            <p>Request ID: #{{ $request->id }}</p>
            <p>Amount: {{ $request->amount }} {{ $request->currency }}</p>

            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>
