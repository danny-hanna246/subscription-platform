@extends('layouts.admin')

@section('title', 'تفاصيل الاشتراك')
@section('page-title', 'تفاصيل الاشتراك #' . $subscription->id)

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    <div class="card">
        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">معلومات العميل</h3>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الاسم</div>
            <div style="font-weight: 600;">{{ $subscription->customer->name }}</div>
        </div>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">البريد الإلكتروني</div>
            <div>{{ $subscription->customer->email }}</div>
        </div>

        @if($subscription->customer->phone)
            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الهاتف</div>
                <div>{{ $subscription->customer->phone }}</div>
            </div>
        @endif

        <div style="margin-top: 20px;">
            <a href="{{ route('admin.customers.show', $subscription->customer) }}" class="btn btn-secondary" style="width: 100%;">
                عرض ملف العميل
            </a>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">معلومات الخطة</h3>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">المنتج</div>
            <div style="font-weight: 600;">{{ $subscription->plan->product->name }}</div>
        </div>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الخطة</div>
            <div>{{ $subscription->plan->name }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">حد المستخدمين</div>
                <div style="font-weight: 600;">{{ $subscription->plan->user_limit }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">حد الأجهزة</div>
                <div style="font-weight: 600;">{{ $subscription->plan->device_limit }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">معلومات الاشتراك</h3>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px;">
        <div>
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">تاريخ البداية</div>
            <div style="font-weight: 600;">{{ $subscription->starts_at->format('Y-m-d') }}</div>
        </div>
        <div>
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">تاريخ الانتهاء</div>
            <div style="font-weight: 600;">{{ $subscription->ends_at->format('Y-m-d') }}</div>
        </div>
        <div>
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الأيام المتبقية</div>
            <div style="font-weight: 600; font-size: 18px; color: {{ $subscription->remainingDays() > 0 ? '#27ae60' : '#e74c3c' }};">
                {{ $subscription->remainingDays() }} يوم
            </div>
        </div>
        <div>
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الحالة</div>
            <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : 'danger' }}">
                {{ $subscription->status }}
            </span>
        </div>
    </div>

    @if($subscription->status === 'active' || $subscription->status === 'expired')
        <div style="display: flex; gap: 10px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <form action="{{ route('admin.subscriptions.renew', $subscription) }}" method="POST" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-success" style="width: 100%;" onclick="return confirm('هل تريد تجديد هذا الاشتراك؟')">
                    تجديد الاشتراك
                </button>
            </form>

            @if($subscription->status === 'active')
                <button onclick="document.getElementById('cancelForm').style.display='block'" class="btn btn-danger" style="flex: 1;">
                    إلغاء الاشتراك
                </button>
            @endif
        </div>

        <div id="cancelForm" style="display: none; margin-top: 15px; padding: 15px; background: #f8d7da; border-radius: 5px;">
            <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">سبب الإلغاء *</label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                    <button type="button" onclick="document.getElementById('cancelForm').style.display='none'" class="btn btn-secondary">إلغاء</button>
                </div>
            </form>
        </div>
    @endif
</div>

@if($subscription->license)
    <div class="card" style="margin-bottom: 20px; background: #d4edda; border: 1px solid #c3e6cb;">
        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #c3e6cb; color: #155724;">معلومات الترخيص</h3>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #155724; margin-bottom: 5px;">مفتاح الترخيص</div>
            <code style="background: white; padding: 12px; border-radius: 5px; display: block; font-size: 16px; color: #3498db; font-weight: 600;">
                {{ $subscription->license->license_key }}
            </code>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <div style="font-size: 12px; color: #155724; margin-bottom: 5px;">حالة الترخيص</div>
                <span class="badge badge-{{ $subscription->license->status === 'active' ? 'success' : 'danger' }}">
                    {{ $subscription->license->status }}
                </span>
            </div>
            <div>
                <div style="font-size: 12px; color: #155724; margin-bottom: 5px;">الأجهزة المفعلة</div>
                <div style="font-weight: 600;">
                    {{ $subscription->license->devices->count() }} / {{ $subscription->plan->device_limit }}
                </div>
            </div>
        </div>

        @if($subscription->license->devices->count() > 0)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #c3e6cb;">
                <h4 style="font-size: 14px; margin-bottom: 15px; color: #155724;">الأجهزة المسجلة</h4>
                @foreach($subscription->license->devices as $device)
                    <div style="background: white; padding: 12px; border-radius: 5px; margin-bottom: 10px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px;">
                            <div>
                                <strong>Device ID:</strong>
                                <code style="background: #f8f9fa; padding: 2px 6px; border-radius: 3px;">{{ Str::limit($device->device_id, 20) }}</code>
                            </div>
                            <div>
                                <strong>تاريخ التفعيل:</strong> {{ $device->activated_at->format('Y-m-d H:i') }}
                            </div>
                            <div style="grid-column: 1/-1;">
                                <strong>آخر اتصال:</strong> {{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'لم يتصل بعد' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

@if($subscription->payments->count() > 0)
    <div class="card">
        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">سجل المدفوعات</h3>

        @foreach($subscription->payments as $payment)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 5px; margin-bottom: 10px;">
                <div>
                    <div style="font-weight: 600; font-size: 18px; color: #2c3e50;">
                        ${{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                    </div>
                    <div style="font-size: 13px; color: #7f8c8d; margin-top: 5px;">
                        {{ $payment->gateway }} • {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : 'لم يدفع بعد' }}
                    </div>
                </div>
                <span class="badge badge-{{ $payment->status === 'succeeded' ? 'success' : 'danger' }}">
                    {{ $payment->status }}
                </span>
            </div>
        @endforeach
    </div>
@endif

<div style="margin-top: 20px;">
    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">العودة للقائمة</a>
</div>
@endsection
