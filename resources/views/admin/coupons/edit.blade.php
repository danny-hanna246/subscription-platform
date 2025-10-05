@extends('layouts.admin')

@section('title', 'تعديل كوبون')
@section('page-title', 'تعديل الكوبون: ' . $coupon->code)

@section('content')
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">رمز الكوبون *</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code) }}" required style="text-transform: uppercase;">
                @error('code')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">نوع الخصم *</label>
                <select name="type" class="form-control" required>
                    <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>نسبة مئوية</option>
                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">قيمة الخصم *</label>
                <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $coupon->value) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">تاريخ البداية</label>
                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from', $coupon->valid_from?->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label class="form-label">تاريخ الانتهاء</label>
                <input type="date" name="valid_to" class="form-control" value="{{ old('valid_to', $coupon->valid_to?->format('Y-m-d')) }}">
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">حد الاستخدام</label>
                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}">
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">الخطط المتاحة</label>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px;">
                    @foreach($plans as $plan)
                        <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; cursor: pointer;">
                            <input type="checkbox" name="applicable_plans[]" value="{{ $plan->id }}"
                                {{ in_array($plan->id, $coupon->applicable_plans ?? []) ? 'checked' : '' }}>
                            <span style="font-size: 14px;">{{ $plan->product->name }} - {{ $plan->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
