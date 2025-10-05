@extends('layouts.admin')

@section('title', 'تعديل خطة')
@section('page-title', 'تعديل الخطة: ' . $plan->name)

@section('content')
<div class="card" style="max-width: 900px;">
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">المنتج *</label>
                <select name="product_id" class="form-control" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id', $plan->product_id) == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">اسم الخطة *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">الرابط (Slug) *</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $plan->slug) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">السعر *</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $plan->price) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">العملة *</label>
                <select name="currency" class="form-control" required>
                    <option value="USD" {{ old('currency', $plan->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ old('currency', $plan->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="SAR" {{ old('currency', $plan->currency) == 'SAR' ? 'selected' : '' }}>SAR</option>
                    <option value="AED" {{ old('currency', $plan->currency) == 'AED' ? 'selected' : '' }}>AED</option>
                    <option value="SYP" {{ old('currency', $plan->currency) == 'SYP' ? 'selected' : '' }}>SYP</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">المدة (بالأيام) *</label>
                <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $plan->duration_days) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">عدد المستخدمين *</label>
                <input type="number" name="user_limit" class="form-control" value="{{ old('user_limit', $plan->user_limit) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">عدد الأجهزة *</label>
                <input type="number" name="device_limit" class="form-control" value="{{ old('device_limit', $plan->device_limit) }}" required>
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">المميزات</label>
                <div id="features-container">
                    @if($plan->features && count($plan->features) > 0)
                        @foreach($plan->features as $feature)
                            <div class="feature-input" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <input type="text" name="features[]" class="form-control" value="{{ $feature }}">
                                <button type="button" onclick="this.parentElement.remove()" class="btn btn-sm btn-danger">حذف</button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" onclick="addFeature()" class="btn btn-sm btn-secondary" style="margin-top: 10px;">إضافة ميزة</button>
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="active" value="1" {{ old('active', $plan->active) ? 'checked' : '' }}>
                    <span>الخطة نشطة</span>
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'feature-input';
    div.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
    div.innerHTML = `
        <input type="text" name="features[]" class="form-control" placeholder="أضف ميزة">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-sm btn-danger">حذف</button>
    `;
    container.appendChild(div);
}
</script>
@endsection
