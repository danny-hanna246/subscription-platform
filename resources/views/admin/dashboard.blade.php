@extends('layouts.admin')

@section('title', 'الرئيسية')
@section('page-title', 'لوحة التحكم')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">إجمالي العملاء</div>
        <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
        <div class="stat-desc">عميل مسجل في النظام</div>
    </div>

    <div class="stat-card success">
        <div class="stat-label">الاشتراكات النشطة</div>
        <div class="stat-value">{{ number_format($stats['active_subscriptions']) }}</div>
        <div class="stat-desc">اشتراك نشط حالياً</div>
    </div>

    <div class="stat-card warning">
        <div class="stat-label">طلبات قيد الانتظار</div>
        <div class="stat-value">{{ number_format($stats['pending_requests']) }}</div>
        <div class="stat-desc">طلب يحتاج موافقة</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">الإيرادات الكلية</div>
        <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="stat-desc">هذا الشهر: ${{ number_format($stats['revenue_this_month'], 2) }}</div>
    </div>
</div>

@if($stats['expiring_soon'] > 0 || $stats['pending_requests'] > 0)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">التنبيهات</h3>
    </div>
    <div style="padding: 0 20px 20px;">
        @if($stats['expiring_soon'] > 0)
            <div class="alert alert-info">
                <strong>تنبيه:</strong> يوجد {{ $stats['expiring_soon'] }} اشتراك سينتهي خلال 7 أيام
            </div>
        @endif

        @if($stats['pending_requests'] > 0)
            <div class="alert" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
                <strong>طلبات جديدة:</strong> يوجد {{ $stats['pending_requests'] }} طلب بانتظار الموافقة
                <a href="{{ route('admin.subscription-requests.index') }}" style="color: #856404; text-decoration: underline;">عرض الطلبات</a>
            </div>
        @endif
    </div>
</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">آخر الاشتراكات</h3>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-secondary">عرض الكل</a>
        </div>
        <div style="padding: 0;">
            @forelse($recentSubscriptions as $subscription)
                <div style="padding: 15px 20px; border-bottom: 1px solid #ecf0f1;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div style="font-weight: 600;">{{ $subscription->customer->name }}</div>
                            <div style="font-size: 13px; color: #7f8c8d;">
                                {{ $subscription->plan->product->name }} - {{ $subscription->plan->name }}
                            </div>
                            <div style="font-size: 12px; color: #95a5a6; margin-top: 5px;">
                                {{ $subscription->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : 'warning' }}">
                            {{ $subscription->status === 'active' ? 'نشط' : $subscription->status }}
                        </span>
                    </div>
                </div>
            @empty
                <div style="padding: 40px; text-align: center; color: #95a5a6;">
                    لا توجد اشتراكات حديثة
                </div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">طلبات بانتظار الموافقة</h3>
            <a href="{{ route('admin.subscription-requests.index') }}" class="btn btn-sm btn-secondary">عرض الكل</a>
        </div>
        <div style="padding: 0;">
            @forelse($pendingRequests as $request)
                <div style="padding: 15px 20px; border-bottom: 1px solid #ecf0f1; background: #fffbf0;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <div style="font-weight: 600;">{{ $request->customer->name }}</div>
                            <div style="font-size: 13px; color: #7f8c8d;">
                                {{ $request->plan->product->name }} - {{ $request->plan->name }}
                            </div>
                            <div style="font-size: 12px; color: #95a5a6; margin-top: 5px;">
                                {{ $request->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; color: #2c3e50;">${{ number_format($request->amount, 2) }}</div>
                            <span class="badge badge-warning" style="margin-top: 5px;">{{ $request->payment_method === 'cash' ? 'نقدي' : 'إلكتروني' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding: 40px; text-align: center; color: #95a5a6;">
                    لا توجد طلبات بانتظار الموافقة
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
