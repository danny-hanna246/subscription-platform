@extends('layouts.admin')

@section('title', 'الاشتراكات')
@section('page-title', 'إدارة الاشتراكات')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة الاشتراكات</h3>
        <form method="GET" style="display: flex; gap: 10px;">
            <select name="status" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                <option value="">جميع الحالات</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>متوقف</option>
            </select>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>رقم الاشتراك</th>
                    <th>العميل</th>
                    <th>الخطة</th>
                    <th>تاريخ البداية</th>
                    <th>تاريخ الانتهاء</th>
                    <th>الأيام المتبقية</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $subscription)
                    <tr>
                        <td><strong>#{{ $subscription->id }}</strong></td>
                        <td>
                            <div style="font-weight: 600;">{{ $subscription->customer->name }}</div>
                            <div style="font-size: 13px; color: #7f8c8d;">{{ $subscription->customer->email }}</div>
                        </td>
                        <td>
                            <div>{{ $subscription->plan->product->name }}</div>
                            <div style="font-size: 13px; color: #7f8c8d;">{{ $subscription->plan->name }}</div>
                        </td>
                        <td>{{ $subscription->starts_at->format('Y-m-d') }}</td>
                        <td>{{ $subscription->ends_at->format('Y-m-d') }}</td>
                        <td>
                            @php
                                $remaining = $subscription->remainingDays();
                            @endphp
                            @if($remaining > 0)
                                <span style="color: #27ae60; font-weight: 600;">{{ $remaining }} يوم</span>
                            @elseif($remaining == 0)
                                <span style="color: #f39c12; font-weight: 600;">ينتهي اليوم</span>
                            @else
                                <span style="color: #e74c3c; font-weight: 600;">منتهي</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'expired' => 'danger',
                                    'cancelled' => 'danger',
                                    'paused' => 'warning',
                                ];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$subscription->status] ?? 'info' }}">
                                {{ $subscription->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-sm btn-secondary">التفاصيل</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #95a5a6;">
                            لا توجد اشتراكات
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($subscriptions->hasPages())
        <div style="padding: 20px;">
            {{ $subscriptions->links() }}
        </div>
    @endif
</div>
@endsection
