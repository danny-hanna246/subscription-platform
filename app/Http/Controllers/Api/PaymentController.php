<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['subscriptionRequest.customer', 'subscription'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('gateway'), function ($query, $gateway) {
                $query->where('gateway', $gateway);
            })
            ->latest()
            ->paginate(15);

        return PaymentResource::collection($payments);
    }

    public function show(Payment $payment)
    {
        return new PaymentResource(
            $payment->load(['subscriptionRequest.customer', 'subscription'])
        );
    }
    
}
