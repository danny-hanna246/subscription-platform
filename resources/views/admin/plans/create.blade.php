@extends('layouts.admin')

@section('title', 'إضافة خطة')
@section('page-title', 'إضافة خطة جديدة')

@section('content')
<div class="card" style="max-width: 900px;">
    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">المنتج *</label>
                <select name="product_id" class="form-control" required>
                    <option value="">اختر المنتج</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">اسم الخطة *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="الخطة الاحترافية">
                @error('name')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">الرابط (Slug) *</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required placeholder="professional-plan">
                @error('slug')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">السعر *</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required placeholder="25.00">
                @error('price')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">العملة *</label>
                <select name="currency" class="form-control" required>
                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                    <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED</option>
                    <option value="SYP" {{ old('currency') == 'SYP' ? 'selected' : '' }}>SYP</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">المدة (بالأيام) *</label>
                <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', 30) }}" required>
                @error('duration_days')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">عدد المستخدمين *</label>
                <input type="number" name="user_limit" class="form-control" value="{{ old('user_limit', 1) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">عدد الأجهزة *</label>
                <input type="number" name="device_limit" class="form-control" value="{{ old('device_limit', 1) }}" required>
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">المميزات (اختياري)</label>
                <div id="features-container">
                    <div class="feature-input" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="features[]" class="form-control" placeholder="أضف ميزة">
                    </div>
                </div>
                <button type="button" onclick="addFeature()" class="btn btn-sm btn-secondary" style="margin-top: 10px;">إضافة ميزة أخرى</button>
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                    <span>الخطة نشطة</span>
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <button type="submit" class="btn btn-primary">حفظ الخطة</button>
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
