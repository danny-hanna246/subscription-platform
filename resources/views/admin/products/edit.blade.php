@extends('layouts.admin')

@section('title', 'تعديل منتج')
@section('page-title', 'تعديل المنتج: ' . $product->name)

@section('content')
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">اسم المنتج *</label>
            <input
                type="text"
                name="name"
                class="form-control"
                value="{{ old('name', $product->name) }}"
                required
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
                class="form-control"
                value="{{ old('slug', $product->slug) }}"
                required
            >
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
            >{{ old('description', $product->description) }}</textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
