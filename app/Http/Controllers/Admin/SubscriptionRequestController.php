<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionRequest;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SubscriptionRequestController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $query = SubscriptionRequest::with(['customer', 'plan.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15);

        return view('admin.subscription-requests.index', compact('requests'));
    }

    public function show(SubscriptionRequest $subscriptionRequest)
    {
        $subscriptionRequest->load([
            'customer',
            'plan.product',
            'payments',
            'subscription.license'
        ]);

        return view('admin.subscription-requests.show', compact('subscriptionRequest'));
    }

    public function approve(SubscriptionRequest $subscriptionRequest)
    {
        if ($subscriptionRequest->status !== 'pending' || $subscriptionRequest->payment_method !== 'cash') {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        try {
            $this->paymentService->processCashPayment($subscriptionRequest, Auth::id());

            return redirect()->route('admin.subscription-requests.index')
                ->with('success', 'تم الموافقة على الطلب وإنشاء الاشتراك بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, SubscriptionRequest $subscriptionRequest)
    {
        $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        $subscriptionRequest->update([
            'status' => 'rejected',
            'notes' => $request->reason,
        ]);

        return redirect()->route('admin.subscription-requests.index')
            ->with('success', 'تم رفض الطلب');
    }
}
