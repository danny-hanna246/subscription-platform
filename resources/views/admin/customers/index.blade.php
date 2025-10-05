@extends('layouts.admin')

@section('title', 'العملاء')
@section('page-title', 'إدارة العملاء')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة العملاء</h3>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">إضافة عميل جديد</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الشركة</th>
                    <th>الاشتراكات</th>
                    <th>تاريخ التسجيل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $customer->name }}</div>
                            @if($customer->phone)
                                <div style="font-size: 13px; color: #7f8c8d;">{{ $customer->phone }}</div>
                            @endif
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->company_name ?? '-' }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <span class="badge badge-info">{{ $customer->subscriptions_count }} إجمالي</span>
                                @if($customer->active_subscriptions_count > 0)
                                    <span class="badge badge-success">{{ $customer->active_subscriptions_count }} نشط</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-secondary">عرض</a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-secondary">تعديل</a>
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #95a5a6;">
                            لا يوجد عملاء
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div style="padding: 20px;">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
