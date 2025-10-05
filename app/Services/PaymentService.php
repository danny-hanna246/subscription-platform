<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SubscriptionRequest;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionApprovedMail;

class PaymentService
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function processOnlinePayment(SubscriptionRequest $request, $gatewayData)
    {
        return DB::transaction(function () use ($request, $gatewayData) {
            // إنشاء سجل الدفع
            $payment = Payment::create([
                'subscription_request_id' => $request->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'gateway' => $gatewayData['gateway'] ?? 'stripe',
                'gateway_transaction_id' => $gatewayData['transaction_id'] ?? null,
                'status' => 'succeeded',
                'paid_at' => Carbon::now(),
                'receipt_url' => $gatewayData['receipt_url'] ?? null,
                'meta' => $gatewayData['meta'] ?? null,
            ]);

            // إنشاء الاشتراك والترخيص
            $result = $this->subscriptionService->createSubscriptionFromRequest($request);

            // ربط الدفع بالاشتراك
            $payment->update(['subscription_id' => $result['subscription']->id]);

            AuditLog::log('Payment', $payment->id, 'online_payment_success', [
                'gateway' => $gatewayData['gateway'] ?? 'stripe',
            ]);

            return [
                'payment' => $payment,
                'subscription' => $result['subscription'],
                'license' => $result['license'],
            ];
        });
    }

    public function processCashPayment(SubscriptionRequest $request, $adminId)
    {
        return DB::transaction(function () use ($request, $adminId) {
            // تحديث حالة الطلب
            $request->update(['status' => 'approved']);

            // إنشاء سجل الدفع
            $payment = Payment::create([
                'subscription_request_id' => $request->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'gateway' => 'cash',
                'status' => 'succeeded',
                'paid_at' => Carbon::now(),
            ]);

            // إنشاء الاشتراك والترخيص
            $result = $this->subscriptionService->createSubscriptionFromRequest($request);

            // ربط الدفع بالاشتراك
            $payment->update(['subscription_id' => $result['subscription']->id]);
            Mail::to($result['subscription']->customer->email)
                ->queue(new SubscriptionApprovedMail($result['subscription'], $result['license']));

            AuditLog::log('Payment', $payment->id, 'cash_payment_approved', [
                'approved_by' => $adminId,
            ]);

            return [
                'payment' => $payment,
                'subscription' => $result['subscription'],
                'license' => $result['license'],
            ];
        });
    }

    public function generatePaymentToken()
    {
        return 'PAY_' . Str::random(32);
    }
}
