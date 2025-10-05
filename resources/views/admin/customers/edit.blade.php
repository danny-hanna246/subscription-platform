@extends('layouts.admin')

@section('title', 'تعديل عميل')
@section('page-title', 'تعديل العميل: ' . $customer->name)

@section('content')
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">الاسم الكامل *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                @error('name')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                @error('email')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">اسم الشركة</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $customer->company_name) }}">
            </div>

            <div class="form-group" style="grid-column: 1/-1;">
                <label class="form-label">العنوان</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address', $customer->address) }}</textarea>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
