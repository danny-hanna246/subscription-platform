@extends('layouts.admin')

@section('title', 'المدفوعات')
@section('page-title', 'إدارة المدفوعات')

@section('content')
<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card success">
        <div class="stat-label">إجمالي الإيرادات</div>
        <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
        <div class="stat-desc">من جميع المدفوعات الناجحة</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">سجل المدفوعات</h3>
        <form method="GET" style="display: flex; gap: 10px;">
            <select name="status" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                <option value="">جميع الحالات</option>
                <option value="succeeded" {{ request('status') === 'succeeded' ? 'selected' : '' }}>ناجح</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>فاشل</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>مسترد</option>
            </select>
            <select name="gateway" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                <option value="">جميع البوابات</option>
                <option value="cash" {{ request('gateway') === 'cash' ? 'selected' : '' }}>نقدي</option>
                <option value="stripe" {{ request('gateway') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                <option value="paypal" {{ request('gateway') === 'paypal' ? 'selected' : '' }}>PayPal</option>
            </select>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>رقم الدفع</th>
                    <th>العميل</th>
                    <th>المبلغ</th>
                    <th>البوابة</th>
                    <th>الحالة</th>
                    <th>تاريخ الدفع</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td><strong>#{{ $payment->id }}</strong></td>
                        <td>
                            @if($payment->subscriptionRequest)
                                <div style="font-weight: 600;">{{ $payment->subscriptionRequest->customer->name }}</div>
                                <div style="font-size: 13px; color: #7f8c8d;">{{ $payment->subscriptionRequest->customer->email }}</div>
                            @else
                                <span style="color: #95a5a6;">-</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 16px; color: #2c3e50;">
                                ${{ number_format($payment->amount, 2) }}
                            </div>
                            <div style="font-size: 12px; color: #7f8c8d;">{{ $payment->currency }}</div>
                        </td>
                        <td>{{ ucfirst($payment->gateway) }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'succeeded' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'info',
                                ];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$payment->status] ?? 'info' }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td>
                            @if($payment->paid_at)
                                {{ $payment->paid_at->format('Y-m-d H:i') }}
                            @else
                                <span style="color: #95a5a6;">لم يدفع بعد</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #95a5a6;">
                            لا توجد مدفوعات
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div style="padding: 20px;">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
