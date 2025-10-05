@extends('layouts.admin')

@section('title', 'إضافة منتج')
@section('page-title', 'إضافة منتج جديد')

@section('content')
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.products.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">اسم المنتج *</label>
            <input
                type="text"
                name="name"
                class="form-control @error('name') error @enderror"
                value="{{ old('name') }}"
                required
                placeholder="مثال: نظام ERP للمبيعات"
            >
            @error('name')
                <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">الرابط (Slug) *</label>
            <input
                type="text"
                name="slug"
                class="form-control @error('slug') error @enderror"
                value="{{ old('slug') }}"
                required
                placeholder="erp-sales-system"
            >
            <small style="color: #7f8c8d; font-size: 12px;">يستخدم في الروابط (بدون مسافات)</small>
            @error('slug')
                <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">الوصف</label>
            <textarea
                name="description"
                class="form-control"
                rows="4"
                placeholder="وصف تفصيلي للمنتج ومميزاته"
            >{{ old('description') }}</textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">حفظ المنتج</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
