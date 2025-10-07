@extends('layouts.admin')

@section('title', 'إنشاء API Key')
@section('page-title', 'إنشاء مفتاح API جديد')

@section('content')
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.api-keys.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">اسم التطبيق/العميل *</label>
            <input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required placeholder="مثال: موقعي الشخصي">
            @error('client_name')
                <div style="color: #e74c3c; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">عناوين IP المسموح لها (اختياري)</label>
            <input type="text" name="allowed_ips" class="form-control" value="{{ old('allowed_ips') }}" placeholder="192.168.1.1, 192.168.1.2 (اتركه فارغاً للسماح لجميع IPs)">
            <small style="color: #7f8c8d; font-size: 12px;">افصل بين IPs بفاصلة (,) - اتركه فارغاً للسماح من أي مكان</small>
        </div>

        <div class="form-group">
            <label class="form-label">الصلاحيات</label>
            <div style="border: 1px solid #ddd; border-radius: 5px; padding: 15px;">
                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer;">
                    <input type="checkbox" name="scopes[]" value="integration" checked>
                    <div>
                        <div style="font-weight: 600;">Integration API</div>
                        <div style="font-size: 12px; color: #7f8c8d;">إنشاء العملاء والاشتراكات</div>
                    </div>
                </label>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="scopes[]" value="validate_license">
                    <div>
                        <div style="font-weight: 600;">License Validation</div>
                        <div style="font-size: 12px; color: #7f8c8d;">التحقق من التراخيص</div>
                    </div>
                </label>
            </div>
        </div>

        <div class="alert" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; margin-bottom: 20px;">
            <strong>⚠️ تنبيه مهم:</strong> سيتم عرض API Key و Secret مرة واحدة فقط بعد الإنشاء. تأكد من حفظهما في مكان آمن!
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">إنشاء API Key</button>
            <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
