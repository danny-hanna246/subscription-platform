<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequestRequest;
use App\Http\Resources\SubscriptionRequestResource;
use App\Models\Admin;
use App\Models\SubscriptionRequest;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\AuditLog;
use App\Notifications\NewSubscriptionRequestNotification;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class SubscriptionRequestController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $requests = SubscriptionRequest::with(['customer', 'plan.product'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('payment_method'), function ($query, $method) {
                $query->where('payment_method', $method);
            })
            ->latest()
            ->paginate(15);

        return SubscriptionRequestResource::collection($requests);
    }

    public function store(StoreSubscriptionRequestRequest $request)
    {
        $plan = Plan::findOrFail($request->plan_id);
        $amount = $plan->price;

        // تطبيق الكوبون إن وجد
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();

            if ($coupon && $coupon->isValid($plan->id)) {
                $discount = $coupon->calculateDiscount($amount);
                $amount -= $discount;
            }
        }

        $subscriptionRequest = SubscriptionRequest::create([
            'customer_id' => $request->customer_id,
            'plan_id' => $request->plan_id,
            'payment_method' => $request->payment_method,
            'status' => $request->payment_method === 'online' ? 'processing' : 'pending',
            'amount' => $amount,
            'currency' => $plan->currency,
            'payment_token' => $request->payment_method === 'online'
                ? $this->paymentService->generatePaymentToken()
                : null,
            'coupon_code' => $request->coupon_code,
            'notes' => $request->notes,
        ]);

        // زيادة عداد استخدام الكوبون
        if (isset($coupon)) {
            $coupon->incrementUsage();
        }

        AuditLog::log('SubscriptionRequest', $subscriptionRequest->id, 'create', $request->validated());
        $admins = Admin::where('is_active', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewSubscriptionRequestNotification($subscriptionRequest));
        }
        return new SubscriptionRequestResource($subscriptionRequest->load(['customer', 'plan']));
    }

    public function show(SubscriptionRequest $subscriptionRequest)
    {
        return new SubscriptionRequestResource(
            $subscriptionRequest->load(['customer', 'plan.product', 'payments', 'subscription'])
        );
    }

    public function approve(SubscriptionRequest $subscriptionRequest)
    {
        if ($subscriptionRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending requests can be approved',
            ], 400);
        }

        if ($subscriptionRequest->payment_method !== 'cash') {
            return response()->json([
                'message' => 'Only cash payment requests can be manually approved',
            ], 400);
        }

        $result = $this->paymentService->processCashPayment(
            $subscriptionRequest,
            auth('admin')->id()
        );

        return response()->json([
            'message' => 'Subscription request approved and activated successfully',
            'data' => [
                'subscription' => $result['subscription'],
                'license' => $result['license'],
            ],
        ]);
    }

    public function reject(Request $request, SubscriptionRequest $subscriptionRequest)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $subscriptionRequest->update([
            'status' => 'rejected',
            'notes' => $request->reason,
        ]);

        AuditLog::log('SubscriptionRequest', $subscriptionRequest->id, 'reject', [
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Subscription request rejected',
        ]);
    }
}
