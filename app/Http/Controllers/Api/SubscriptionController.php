<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index()
    {
        $subscriptions = Subscription::with(['customer', 'plan.product', 'license'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('customer_id'), function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when(request('expiring_soon'), function ($query) {
                $query->expiringSoon(7);
            })
            ->latest()
            ->paginate(15);

        return SubscriptionResource::collection($subscriptions);
    }

    public function show(Subscription $subscription)
    {
        return new SubscriptionResource(
            $subscription->load(['customer', 'plan.product', 'license.devices'])
        );
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        $request->validate([
            'reason' => 'nullable|string',
        ]);

        $this->subscriptionService->cancelSubscription($subscription, $request->reason);

        return response()->json([
            'message' => 'Subscription cancelled successfully',
        ]);
    }

    public function renew(Subscription $subscription)
    {
        if ($subscription->status !== 'active' && $subscription->status !== 'expired') {
            return response()->json([
                'message' => 'Only active or expired subscriptions can be renewed',
            ], 400);
        }

        $renewed = $this->subscriptionService->renewSubscription($subscription);

        return response()->json([
            'message' => 'Subscription renewed successfully',
            'data' => new SubscriptionResource($renewed),
        ]);
    }
}
