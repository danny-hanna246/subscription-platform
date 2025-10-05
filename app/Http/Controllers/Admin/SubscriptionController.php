<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function index(Request $request)
    {
        $query = Subscription::with(['customer', 'plan.product', 'license']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate(15);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load([
            'customer',
            'plan.product',
            'license.devices',
            'payments'
        ]);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function renew(Subscription $subscription)
    {
        try {
            $this->subscriptionService->renewSubscription($subscription);

            return back()->with('success', 'تم تجديد الاشتراك بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        try {
            $this->subscriptionService->cancelSubscription($subscription, $request->reason);

            return back()->with('success', 'تم إلغاء الاشتراك بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
