@extends('layouts.admin')

@section('title', 'الكوبونات')
@section('page-title', 'إدارة الكوبونات')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة الكوبونات</h3>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">إضافة كوبون جديد</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px;">
        @forelse($coupons as $coupon)
            <div style="border: 1px solid #ecf0f1; border-radius: 8px; padding: 20px; background: white;">
                <div style="margin-bottom: 15px;">
                    <code style="font-size: 20px; font-weight: 700; color: #3498db; background: #f8f9fa; padding: 8px 15px; border-radius: 5px; display: inline-block;">
                        {{ $coupon->code }}
                    </code>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <span class="badge badge-{{ $coupon->isValid() ? 'success' : 'danger' }}">
                        {{ $coupon->isValid() ? 'نشط' : 'منتهي' }}
                    </span>
                    <span class="badge badge-info">
                        {{ $coupon->type === 'percent' ? $coupon->value . '%' : '$' . $coupon->value }}
                    </span>
                </div>

                <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 15px;">
                    @if($coupon->valid_from)
                        <div>من: {{ $coupon->valid_from->format('Y-m-d') }}</div>
                    @endif
                    @if($coupon->valid_to)
                        <div>إلى: {{ $coupon->valid_to->format('Y-m-d') }}</div>
                    @endif
                    @if($coupon->usage_limit)
                        <div>الاستخدام: {{ $coupon->used_count }} / {{ $coupon->usage_limit }}</div>
                    @else
                        <div>استخدم: {{ $coupon->used_count }} مرة</div>
                    @endif
                </div>

                <div style="display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid #ecf0f1;">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-secondary" style="flex: 1;">تعديل</a>
                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="flex: 1;" onsubmit="return confirm('هل أنت متأكد؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" style="width: 100%;">حذف</button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #95a5a6;">
                لا توجد كوبونات
            </div>
        @endforelse
    </div>

    @if($coupons->hasPages())
        <div style="padding: 20px;">
            {{ $coupons->links() }}
        </div>
    @endif
</div>
@endsection
