<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use App\Models\License;
use App\Models\Payment;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LicenseKeyMail;

class SubscriptionService
{
    public function createSubscriptionFromRequest(SubscriptionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // إنشاء الاشتراك
            $subscription = Subscription::create([
                'subscription_request_id' => $request->id,
                'customer_id' => $request->customer_id,
                'plan_id' => $request->plan_id,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays($request->plan->duration_days),
                'status' => 'active',
                'gateway_transaction_id' => $request->payment_token,
            ]);

            // إنشاء الترخيص
            $license = $this->generateLicense($subscription);

            // تحديث حالة الطلب
            $request->update(['status' => 'completed']);
            Mail::to($subscription->customer->email)
                ->queue(new LicenseKeyMail($license));
            // تسجيل في audit log
            AuditLog::log('Subscription', $subscription->id, 'create', [
                'request_id' => $request->id,
            ]);

            return [
                'subscription' => $subscription->load('plan', 'customer'),
                'license' => $license,
            ];
        });
    }

    public function generateLicense(Subscription $subscription)
    {
        $licenseKey = License::generateLicenseKey('LIC');

        $license = License::create([
            'subscription_id' => $subscription->id,
            'license_key' => $licenseKey,
            'issued_at' => Carbon::now(),
            'expires_at' => $subscription->ends_at,
            'status' => 'active',
        ]);

        return $license;
    }

    public function renewSubscription(Subscription $subscription, $paymentId = null)
    {
        return DB::transaction(function () use ($subscription, $paymentId) {
            $plan = $subscription->plan;

            $subscription->update([
                'starts_at' => $subscription->ends_at,
                'ends_at' => Carbon::parse($subscription->ends_at)->addDays($plan->duration_days),
                'status' => 'active',
            ]);

            // تحديث الترخيص
            if ($subscription->license) {
                $subscription->license->update([
                    'expires_at' => $subscription->ends_at,
                    'status' => 'active',
                ]);
            }

            AuditLog::log('Subscription', $subscription->id, 'renew', [
                'payment_id' => $paymentId,
            ]);

            return $subscription->fresh();
        });
    }

    public function cancelSubscription(Subscription $subscription, $reason = null)
    {
        return DB::transaction(function () use ($subscription, $reason) {
            $subscription->update(['status' => 'cancelled']);

            if ($subscription->license) {
                $subscription->license->update(['status' => 'revoked']);
            }

            AuditLog::log('Subscription', $subscription->id, 'cancel', [
                'reason' => $reason,
            ]);

            return $subscription;
        });
    }

    public function checkAndUpdateExpiredSubscriptions()
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '<', Carbon::now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);

            if ($subscription->license) {
                $subscription->license->update(['status' => 'expired']);
            }

            AuditLog::log('Subscription', $subscription->id, 'auto_expire', null);
        }

        return $expiredSubscriptions->count();
    }
}
