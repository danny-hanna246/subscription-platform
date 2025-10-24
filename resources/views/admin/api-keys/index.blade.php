@extends('layouts.admin')

@section('title', 'مفاتيح API')
@section('page-title', 'إدارة مفاتيح API')

@section('content')

    {{-- عرض المفتاح الجديد إذا تم إنشاؤه --}}
    @if (session('api_key'))
        <div class="alert alert-success" style="position: relative;">
            <button type="button" class="close-btn" onclick="this.parentElement.remove()"
                style="position: absolute; top: 10px; left: 10px; background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>

            <h4 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">✅</span>
                <span>تم إنشاء API Key بنجاح</span>
            </h4>

            <div
                style="background: #ffe8e8; border: 2px solid #ff6b6b; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <p style="margin: 0; color: #c92a2a; font-weight: 600; font-size: 16px;">
                    ⚠️ <strong>مهم جداً:</strong> احفظ هذه المعلومات الآن - لن تظهر مرة أخرى!
                </p>
            </div>

            <div
                style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e9ecef;">
                <label style="font-size: 13px; color: #666; font-weight: 600; margin-bottom: 8px; display: block;">
                    🔑 API Key
                </label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <code id="api-key"
                        style="flex: 1; display: block; background: #f8f9fa; padding: 12px 15px; border-radius: 6px; font-size: 14px; word-break: break-all; font-family: 'Courier New', monospace; border: 1px solid #dee2e6;">{{ session('api_key') }}</code>
                    <button onclick="copyToClipboard('api-key')" class="btn btn-secondary" style="min-width: 100px;">
                        📋 نسخ
                    </button>
                </div>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                <label style="font-size: 13px; color: #666; font-weight: 600; margin-bottom: 8px; display: block;">
                    🔒 API Secret
                </label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <code id="api-secret"
                        style="flex: 1; display: block; background: #f8f9fa; padding: 12px 15px; border-radius: 6px; font-size: 14px; word-break: break-all; font-family: 'Courier New', monospace; border: 1px solid #dee2e6;">{{ session('api_secret') }}</code>
                    <button onclick="copyToClipboard('api-secret')" class="btn btn-secondary" style="min-width: 100px;">
                        📋 نسخ
                    </button>
                </div>
            </div>

            <div
                style="background: #e7f5ff; border-left: 4px solid #1c7ed6; padding: 15px; margin-top: 15px; border-radius: 4px;">
                <p style="margin: 0; color: #1864ab; font-size: 14px;">
                    💡 <strong>نصيحة:</strong> احفظ هذه المفاتيح في ملف <code>.env</code> في مشروعك ولا تشاركها مع أحد.
                </p>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">قائمة مفاتيح API</h3>
            <a href="{{ route('admin.api-keys.create') }}" class="btn btn-primary">➕ إنشاء مفتاح جديد</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>اسم العميل</th>
                        <th>API Key</th>
                        <th>IP المسموح</th>
                        <th>الصلاحيات</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th style="width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apiKeys as $key)
                        <tr>
                            <td>{{ $key->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: #2c3e50;">{{ $key->client_name }}</div>
                            </td>
                            <td>
                                <code
                                    style="background: #f8f9fa; padding: 6px 10px; border-radius: 4px; font-size: 12px; color: #495057;">
                                    {{ Str::limit($key->api_key, 25) }}...
                                </code>
                            </td>
                            <td>
                                @if ($key->allowed_ips)
                                    <span
                                        style="font-size: 12px; color: #6c757d;">{{ Str::limit($key->allowed_ips, 30) }}</span>
                                @else
                                    <span class="badge badge-info">جميع IPs</span>
                                @endif
                            </td>
                            <td>
                                @if ($key->scopes)
                                    @foreach ($key->scopes as $scope)
                                        <span class="badge badge-info"
                                            style="margin: 2px 4px; font-size: 11px;">{{ $scope }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-secondary">لا يوجد</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $key->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $key->status === 'active' ? '✓ نشط' : '✗ معطل' }}
                                </span>
                            </td>
                            <td style="font-size: 12px; color: #6c757d;">{{ $key->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.api-keys.destroy', $key) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('هل أنت متأكد من إلغاء هذا المفتاح؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="إلغاء المفتاح">
                                        🗑️ إلغاء
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">
                                <div style="font-size: 48px; margin-bottom: 15px;">🔑</div>
                                <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">لا توجد مفاتيح API</div>
                                <div style="font-size: 14px;">ابدأ بإنشاء مفتاح API جديد للتكامل مع تطبيقاتك</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($apiKeys->hasPages())
            <div class="card-footer">
                {{ $apiKeys->links() }}
            </div>
        @endif
    </div>

    {{-- قسم التوثيق --}}
    <div class="card" style="margin-top: 30px;">
        <div class="card-header">
            <h3 class="card-title">📚 دليل استخدام API</h3>
        </div>

        <div style="padding: 25px;">

            {{-- معلومات أساسية --}}
            <div
                style="background: #e7f5ff; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #1c7ed6;">
                <h4
                    style="font-size: 16px; margin-bottom: 12px; color: #1864ab; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">💡</span>
                    <span>معلومات أساسية</span>
                </h4>
                <ul style="margin: 0; padding-right: 20px; line-height: 1.8; color: #1971c2;">
                    <li><strong>Base URL:</strong> <code
                            style="background: white; padding: 4px 8px; border-radius: 4px;">{{ url('/api/integration/v1') }}</code>
                    </li>
                    <li><strong>Authentication:</strong> يتم إرسال API Key في Header باسم <code
                            style="background: white; padding: 4px 8px; border-radius: 4px;">X-API-Key</code></li>
                    <li><strong>Content-Type:</strong> جميع الطلبات والاستجابات بتنسيق <code
                            style="background: white; padding: 4px 8px; border-radius: 4px;">application/json</code></li>
                    <li><strong>Rate Limiting:</strong> 60 طلب في الدقيقة</li>
                </ul>
            </div>

            {{-- 1. الحصول على المنتجات --}}
            <div style="margin-bottom: 30px; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #e9ecef;">
                    <h4 style="font-size: 16px; margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
                        <span
                            style="background: #28a745; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">GET</span>
                        <span>1. الحصول على المنتجات والخطط</span>
                    </h4>
                </div>
                <div style="padding: 20px;">
                    <p style="margin-bottom: 15px; color: #6c757d; font-size: 14px;">
                        احصل على قائمة بجميع المنتجات المتاحة والخطط المرتبطة بها. يمكنك استخدام هذا للعرض في موقعك.
                    </p>

                    <div
                        style="background: #2c3e50; border-radius: 6px; padding: 15px; margin-bottom: 15px; position: relative;">
                        <button onclick="copyToClipboard('code-1')"
                            style="position: absolute; top: 10px; left: 10px; background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            📋 نسخ
                        </button>
                        <pre style="margin: 0; overflow-x: auto;"><code id="code-1" style="color: #ecf0f1; font-family: 'Courier New', monospace; font-size: 13px;">curl -X GET "{{ url('/api/integration/v1/products') }}" \
  -H "X-API-Key: YOUR_API_KEY_HERE" \
  -H "Accept: application/json"</code></pre>
                    </div>

                    <details style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; background: #f8f9fa;">
                        <summary style="cursor: pointer; font-weight: 600; color: #28a745;">✅ مثال على الاستجابة</summary>
                        <pre style="margin-top: 15px; background: white; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;"><code>{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "برنامج المحاسبة",
      "description": "نظام محاسبي متكامل",
      "plans": [
        {
          "id": 1,
          "name": "خطة شهرية",
          "price": "99.99",
          "currency": "USD",
          "duration_days": 30,
          "device_limit": 1
        },
        {
          "id": 2,
          "name": "خطة سنوية",
          "price": "999.99",
          "currency": "USD",
          "duration_days": 365,
          "device_limit": 3
        }
      ]
    }
  ]
}</code></pre>
                    </details>
                </div>
            </div>

            {{-- 2. إنشاء عميل --}}
            <div style="margin-bottom: 30px; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #e9ecef;">
                    <h4 style="font-size: 16px; margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
                        <span
                            style="background: #007bff; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">POST</span>
                        <span>2. إنشاء عميل جديد</span>
                    </h4>
                </div>
                <div style="padding: 20px;">
                    <p style="margin-bottom: 15px; color: #6c757d; font-size: 14px;">
                        أنشئ عميلاً جديداً في النظام. يجب إنشاء العميل قبل إنشاء اشتراك له.
                    </p>

                    <div
                        style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 12px; margin-bottom: 15px;">
                        <strong>📋 الحقول المطلوبة:</strong>
                        <ul style="margin: 8px 0 0 0; padding-right: 20px; font-size: 13px;">
                            <li><code>name</code> - اسم العميل (مطلوب)</li>
                            <li><code>email</code> - البريد الإلكتروني (مطلوب، فريد)</li>
                            <li><code>phone</code> - رقم الهاتف (اختياري)</li>
                            <li><code>company_name</code> - اسم الشركة (اختياري)</li>
                        </ul>
                    </div>

                    <div
                        style="background: #2c3e50; border-radius: 6px; padding: 15px; margin-bottom: 15px; position: relative;">
                        <button onclick="copyToClipboard('code-2')"
                            style="position: absolute; top: 10px; left: 10px; background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            📋 نسخ
                        </button>
                        <pre style="margin: 0; overflow-x: auto;"><code id="code-2" style="color: #ecf0f1; font-family: 'Courier New', monospace; font-size: 13px;">curl -X POST "{{ url('/api/integration/v1/customers') }}" \
  -H "X-API-Key: YOUR_API_KEY_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "أحمد علي",
    "email": "ahmed@example.com",
    "phone": "+963123456789",
    "company_name": "شركة التقنية"
  }'</code></pre>
                    </div>

                    <details style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; background: #f8f9fa;">
                        <summary style="cursor: pointer; font-weight: 600; color: #28a745;">✅ مثال على الاستجابة</summary>
                        <pre
                            style="margin-top: 15px; background: white; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;"><code>{
  "success": true,
  "message": "Customer created successfully",
  "data": {
    "id": 1,
    "name": "أحمد علي",
    "email": "ahmed@example.com",
    "phone": "+963123456789",
    "company_name": "شركة التقنية",
    "created_at": "2025-10-24T10:30:00.000000Z"
  }
}</code></pre>
                    </details>
                </div>
            </div>

            {{-- 3. التحقق من الكوبون --}}
            <div style="margin-bottom: 30px; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #e9ecef;">
                    <h4 style="font-size: 16px; margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
                        <span
                            style="background: #007bff; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">POST</span>
                        <span>3. التحقق من صحة كوبون الخصم (اختياري)</span>
                    </h4>
                </div>
                <div style="padding: 20px;">
                    <p style="margin-bottom: 15px; color: #6c757d; font-size: 14px;">
                        تحقق من صحة كوبون الخصم واحصل على السعر بعد الخصم قبل إنشاء الاشتراك.
                    </p>

                    <div
                        style="background: #2c3e50; border-radius: 6px; padding: 15px; margin-bottom: 15px; position: relative;">
                        <button onclick="copyToClipboard('code-3')"
                            style="position: absolute; top: 10px; left: 10px; background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            📋 نسخ
                        </button>
                        <pre style="margin: 0; overflow-x: auto;"><code id="code-3" style="color: #ecf0f1; font-family: 'Courier New', monospace; font-size: 13px;">curl -X POST "{{ url('/api/integration/v1/coupons/validate') }}" \
  -H "X-API-Key: YOUR_API_KEY_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "coupon_code": "SUMMER2024",
    "plan_id": 1
  }'</code></pre>
                    </div>

                    <details style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; background: #f8f9fa;">
                        <summary style="cursor: pointer; font-weight: 600; color: #28a745;">✅ مثال على الاستجابة</summary>
                        <pre
                            style="margin-top: 15px; background: white; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;"><code>{
  "success": true,
  "data": {
    "coupon_code": "SUMMER2024",
    "coupon_type": "percent",
    "coupon_value": 20.0,
    "original_price": 99.99,
    "discount": 20.00,
    "final_price": 79.99,
    "currency": "USD"
  }
}</code></pre>
                    </details>
                </div>
            </div>

            {{-- 4. إنشاء اشتراك --}}
            <div style="margin-bottom: 30px; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #e9ecef;">
                    <h4 style="font-size: 16px; margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
                        <span
                            style="background: #007bff; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">POST</span>
                        <span>4. إنشاء طلب اشتراك</span>
                    </h4>
                </div>
                <div style="padding: 20px;">
                    <p style="margin-bottom: 15px; color: #6c757d; font-size: 14px;">
                        أنشئ طلب اشتراك جديد لعميل موجود. سيتم توليد رابط للدفع إذا كانت طريقة الدفع إلكترونية.
                    </p>

                    <div
                        style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 12px; margin-bottom: 15px;">
                        <strong>📋 الحقول المطلوبة:</strong>
                        <ul style="margin: 8px 0 0 0; padding-right: 20px; font-size: 13px;">
                            <li><code>email</code> - البريد الإلكتروني للعميل (مطلوب)</li>
                            <li><code>plan_id</code> - رقم الخطة (مطلوب)</li>
                            <li><code>payment_method</code> - طريقة الدفع: <code>online</code> أو <code>cash</code> (مطلوب)
                            </li>
                            <li><code>coupon_code</code> - رمز الكوبون (اختياري)</li>
                        </ul>
                    </div>

                    <div
                        style="background: #2c3e50; border-radius: 6px; padding: 15px; margin-bottom: 15px; position: relative;">
                        <button onclick="copyToClipboard('code-4')"
                            style="position: absolute; top: 10px; left: 10px; background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            📋 نسخ
                        </button>
                        <pre style="margin: 0; overflow-x: auto;"><code id="code-4" style="color: #ecf0f1; font-family: 'Courier New', monospace; font-size: 13px;">curl -X POST "{{ url('/api/integration/v1/subscriptions') }}" \
  -H "X-API-Key: YOUR_API_KEY_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "plan_id": 1,
    "payment_method": "online",
    "coupon_code": "SUMMER2024"
  }'</code></pre>
                    </div>

                    <details style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; background: #f8f9fa;">
                        <summary style="cursor: pointer; font-weight: 600; color: #28a745;">✅ مثال على الاستجابة</summary>
                        <pre
                            style="margin-top: 15px; background: white; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;"><code>{
  "success": true,
  "message": "Subscription request created successfully",
  "data": {
    "subscription_request_id": 123,
    "customer": {
      "id": 1,
      "name": "أحمد علي",
      "email": "ahmed@example.com"
    },
    "plan": {
      "id": 1,
      "name": "خطة شهرية",
      "product": "برنامج المحاسبة"
    },
    "amount": 79.99,
    "currency": "USD",
    "payment_method": "online",
    "status": "processing",
    "payment_url": "https://your-domain.com/payment/process?token=PAY_...",
    "payment_token": "PAY_abc123..."
  }
}</code></pre>
                    </details>

                    <div
                        style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 6px; padding: 12px; margin-top: 15px;">
                        <strong style="color: #0c5460;">💡 ملاحظة:</strong>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #0c5460;">
                            إذا كانت طريقة الدفع <code>online</code>، ستحصل على <code>payment_url</code> - قم بتوجيه العميل
                            لهذا الرابط لإكمال الدفع.
                            بعد الدفع، سيتم إنشاء الاشتراك والترخيص تلقائياً وإرسال إيميل للعميل.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 5. التحقق من حالة الاشتراك --}}
            <div style="margin-bottom: 30px; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #e9ecef;">
                    <h4 style="font-size: 16px; margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
                        <span
                            style="background: #28a745; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">GET</span>
                        <span>5. التحقق من حالة الاشتراك</span>
                    </h4>
                </div>
                <div style="padding: 20px;">
                    <p style="margin-bottom: 15px; color: #6c757d; font-size: 14px;">
                        تحقق من حالة طلب الاشتراك واحصل على الترخيص إذا تمت الموافقة.
                    </p>

                    <div
                        style="background: #2c3e50; border-radius: 6px; padding: 15px; margin-bottom: 15px; position: relative;">
                        <button onclick="copyToClipboard('code-5')"
                            style="position: absolute; top: 10px; left: 10px; background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            📋 نسخ
                        </button>
                        <pre style="margin: 0; overflow-x: auto;"><code id="code-5" style="color: #ecf0f1; font-family: 'Courier New', monospace; font-size: 13px;">curl -X GET "{{ url('/api/integration/v1/subscriptions/123/status') }}" \
  -H "X-API-Key: YOUR_API_KEY_HERE" \
  -H "Accept: application/json"</code></pre>
                    </div>

                    <details style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; background: #f8f9fa;">
                        <summary style="cursor: pointer; font-weight: 600; color: #28a745;">✅ مثال على الاستجابة - بعد
                            الموافقة</summary>
                        <pre
                            style="margin-top: 15px; background: white; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;"><code>{
  "success": true,
  "data": {
    "subscription_request_id": 123,
    "status": "completed",
    "payment_method": "online",
    "amount": 79.99,
    "currency": "USD",
    "subscription": {
      "id": 456,
      "status": "active",
      "starts_at": "2025-10-24 10:35:00",
      "ends_at": "2025-11-23 10:35:00"
    },
    "license_key": "LIC-ABC1-DEF2-GHI3-JKL4"
  }
}</code></pre>
                    </details>
                </div>
            </div>

            {{-- معالجة الأخطاء --}}
            <div style="border: 1px solid #f5c6cb; border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
                <div style="background: #f8d7da; padding: 15px; border-bottom: 1px solid #f5c6cb;">
                    <h4 style="font-size: 16px; margin: 0; color: #721c24; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">⚠️</span>
                        <span>معالجة الأخطاء</span>
                    </h4>
                </div>
                <div style="padding: 20px; background: #fff;">
                    <div style="margin-bottom: 20px;">
                        <strong style="color: #dc3545;">❌ خطأ 401 - Unauthorized</strong>
                        <pre
                            style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-top: 8px; font-size: 12px; border: 1px solid #e9ecef;"><code>{
  "error": "API key is required",
  "message": "Please provide X-API-Key header"
}</code></pre>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #6c757d;">
                            <strong>الحل:</strong> تأكد من إرسال API Key في الـ Header
                        </p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong style="color: #dc3545;">❌ خطأ 422 - Validation Error</strong>
                        <pre
                            style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-top: 8px; font-size: 12px; border: 1px solid #e9ecef;"><code>{
  "success": false,
  "errors": {
    "email": ["The email field is required."],
    "plan_id": ["The selected plan id is invalid."]
  }
}</code></pre>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #6c757d;">
                            <strong>الحل:</strong> تحقق من البيانات المرسلة وصحح الأخطاء
                        </p>
                    </div>

                    <div>
                        <strong style="color: #dc3545;">❌ خطأ 429 - Too Many Requests</strong>
                        <pre
                            style="background: #f8f9fa; padding: 12px; border-radius: 4px; margin-top: 8px; font-size: 12px; border: 1px solid #e9ecef;"><code>{
  "error": "Too many requests. Please try again later.",
  "retry_after": 60
}</code></pre>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #6c757d;">
                            <strong>الحل:</strong> انتظر المدة المحددة في <code>retry_after</code> قبل المحاولة مرة أخرى
                        </p>
                    </div>
                </div>
            </div>

            {{-- نصائح الأمان --}}
            <div style="border: 1px solid #d4edda; border-radius: 8px; overflow: hidden;">
                <div style="background: #d4edda; padding: 15px; border-bottom: 1px solid #c3e6cb;">
                    <h4 style="font-size: 16px; margin: 0; color: #155724; display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">🔒</span>
                        <span>نصائح الأمان</span>
                    </h4>
                </div>
                <div style="padding: 20px; background: #fff;">
                    <ul style="margin: 0; padding-right: 20px; line-height: 2;">
                        <li>احفظ API Key في ملف <code>.env</code> ولا تشاركه في الكود البرمجي</li>
                        <li>استخدم HTTPS دائماً للاتصال بالـ API</li>
                        <li>إذا كنت تعمل من خادم ثابت، حدد IP الخادم في إعدادات API Key</li>
                        <li>راقب استخدام API Key وتأكد من عدم وجود نشاط غير عادي</li>
                        <li>أعط كل تطبيق الصلاحيات التي يحتاجها فقط</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- JavaScript للنسخ --}}
    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent || element.innerText;

            navigator.clipboard.writeText(text).then(function() {
                // عرض رسالة نجاح
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '✅ تم النسخ';
                button.style.background = '#28a745';

                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.style.background = '#3498db';
                }, 2000);
            }).catch(function(err) {
                console.error('فشل النسخ: ', err);
                alert('فشل نسخ النص');
            });
        }
    </script>

    <style>
        details summary {
            transition: color 0.2s;
        }

        details summary:hover {
            color: #1e7e34 !important;
        }

        details[open] summary {
            margin-bottom: 15px;
        }
    </style>

@endsection
