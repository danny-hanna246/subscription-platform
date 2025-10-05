<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['subscriptionRequest.customer', 'subscription']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        $payments = $query->latest()->paginate(15);
        $totalRevenue = Payment::where('status', 'succeeded')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalRevenue'));
    }
}
