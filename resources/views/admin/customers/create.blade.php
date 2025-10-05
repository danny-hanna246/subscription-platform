@extends('layouts.admin')

@section('title', 'إضافة عميل')
@section('page-title', 'إضافة عميل جديد')

@section('content')
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.customers.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">الاسم الكامل *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                @error('email')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                @error('phone')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">اسم الشركة</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}">
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">العنوان</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <button type="submit" class="btn btn-primary">حفظ العميل</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
