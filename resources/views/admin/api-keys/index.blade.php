@extends('layouts.admin')

@section('title', 'مفاتيح API')
@section('page-title', 'إدارة مفاتيح API')

@section('content')
@if(session('api_key'))
    <div class="alert alert-success">
        <h4 style="margin-bottom: 15px;">✅ تم إنشاء API Key بنجاح</h4>
        <p style="margin-bottom: 10px;"><strong>⚠️ احفظ هذه المعلومات الآن - لن تظهر مرة أخرى!</strong></p>

        <div style="background: white; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
            <label style="font-size: 12px; color: #666;">API Key:</label>
            <code style="display: block; background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 14px; word-break: break-all;">
                {{ session('api_key') }}
            </code>
        </div>

        <div style="background: white; padding: 15px; border-radius: 5px;">
            <label style="font-size: 12px; color: #666;">API Secret:</label>
            <code style="display: block; background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 14px; word-break: break-all;">
                {{ session('api_secret') }}
            </code>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة مفاتيح API</h3>
        <a href="{{ route('admin.api-keys.create') }}" class="btn btn-primary">إنشاء مفتاح جديد</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>اسم العميل</th>
                    <th>API Key</th>
                    <th>IP المسموح</th>
                    <th>الصلاحيات</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($apiKeys as $key)
                    <tr>
                        <td>{{ $key->id }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $key->client_name }}</div>
                        </td>
                        <td>
                            <code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">
                                {{ Str::limit($key->api_key, 20) }}...
                            </code>
                        </td>
                        <td>{{ $key->allowed_ips ?? 'الكل' }}</td>
                        <td>
                            @if($key->scopes)
                                @foreach($key->scopes as $scope)
                                    <span class="badge badge-info" style="margin: 2px;">{{ $scope }}</span>
                                @endforeach
                            @else
                                <span class="badge badge-info">الكل</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $key->status === 'active' ? 'success' : 'danger' }}">
                                {{ $key->status === 'active' ? 'نشط' : 'موقوف' }}
                            </span>
                        </td>
                        <td>{{ $key->created_at->format('Y-m-d') }}</td>
                        <td>
                            @if($key->status === 'active')
                                <form action="{{ route('admin.api-keys.destroy', $key) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من إيقاف هذا المفتاح؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">إيقاف</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #95a5a6;">
                            لا توجد مفاتيح API
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($apiKeys->hasPages())
        <div style="padding: 20px;">
            {{ $apiKeys->links() }}
        </div>
    @endif
</div>

<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 20px;">📘 كيفية استخدام API</h3>

    <div style="margin-bottom: 20px;">
        <h4 style="font-size: 16px; margin-bottom: 10px;">1. إضافة API Key في الـ Headers</h4>
        <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>X-API-Key: your_api_key_here</code></pre>
    </div>

    <div style="margin-bottom: 20px;">
        <h4 style="font-size: 16px; margin-bottom: 10px;">2. مثال: الحصول على الخطط</h4>
        <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>curl -X GET "{{ url('/api/integration/v1/plans') }}" \
  -H "X-API-Key: your_api_key_here" \
  -H "Accept: application/json"</code></pre>
    </div>

    <div style="margin-bottom: 20px;">
        <h4 style="font-size: 16px; margin-bottom: 10px;">3. مثال: إنشاء عميل</h4>
        <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>curl -X POST "{{ url('/api/integration/v1/customers') }}" \
  -H "X-API-Key: your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmed Ali",
    "email": "ahmed@example.com",
    "phone": "+963123456789"
  }'</code></pre>
    </div>

    <div>
        <h4 style="font-size: 16px; margin-bottom: 10px;">4. مثال: إنشاء اشتراك</h4>
        <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>curl -X POST "{{ url('/api/integration/v1/subscriptions') }}" \
  -H "X-API-Key: your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "plan_id": 1,
    "payment_method": "online",
    "coupon_code": "SUMMER2024"
  }'</code></pre>
    </div>
</div>
@endsection
