<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\SubscriptionRequest;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'pending_requests' => SubscriptionRequest::where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'succeeded')->sum('amount'),
            'revenue_this_month' => Payment::where('status', 'succeeded')
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('amount'),
            'expiring_soon' => Subscription::where('status', 'active')
                ->where('ends_at', '<=', Carbon::now()->addDays(7))
                ->where('ends_at', '>', Carbon::now())
                ->count(),
        ];

        $recentSubscriptions = Subscription::with(['customer', 'plan.product'])
            ->latest()
            ->take(5)
            ->get();

        $pendingRequests = SubscriptionRequest::with(['customer', 'plan.product'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentSubscriptions', 'pendingRequests'));
    }
}
