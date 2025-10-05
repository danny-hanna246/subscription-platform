@extends('layouts.admin')

@section('title', 'طلبات الاشتراك')
@section('page-title', 'إدارة طلبات الاشتراك')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة الطلبات</h3>
        <form method="GET" style="display: flex; gap: 10px;">
            <select name="status" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                <option value="">جميع الحالات</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>موافق عليه</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
            </select>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>الخطة</th>
                    <th>المبلغ</th>
                    <th>طريقة الدفع</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td><strong>#{{ $request->id }}</strong></td>
                        <td>
                            <div style="font-weight: 600;">{{ $request->customer->name }}</div>
                            <div style="font-size: 13px; color: #7f8c8d;">{{ $request->customer->email }}</div>
                        </td>
                        <td>
                            <div>{{ $request->plan->product->name }}</div>
                            <div style="font-size: 13px; color: #7f8c8d;">{{ $request->plan->name }}</div>
                        </td>
                        <td style="font-weight: 600;">${{ number_format($request->amount, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $request->payment_method === 'cash' ? 'warning' : 'info' }}">
                                {{ $request->payment_method === 'cash' ? 'نقدي' : 'إلكتروني' }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'completed' => 'success',
                                ];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$request->status] ?? 'info' }}">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.subscription-requests.show', $request) }}" class="btn btn-sm btn-secondary">عرض التفاصيل</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #95a5a6;">
                            لا توجد طلبات
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div style="padding: 20px;">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
