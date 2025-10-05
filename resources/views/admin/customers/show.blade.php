@extends('layouts.admin')

@section('title', 'تفاصيل العميل')
@section('page-title', 'تفاصيل العميل: ' . $customer->name)

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <div class="card">
        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">معلومات العميل</h3>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الاسم</div>
            <div style="font-weight: 600;">{{ $customer->name }}</div>
        </div>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">البريد الإلكتروني</div>
            <div>{{ $customer->email }}</div>
        </div>

        @if($customer->phone)
            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الهاتف</div>
                <div>{{ $customer->phone }}</div>
            </div>
        @endif

        @if($customer->company_name)
            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الشركة</div>
                <div>{{ $customer->company_name }}</div>
            </div>
        @endif

        @if($customer->address)
            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">العنوان</div>
                <div>{{ $customer->address }}</div>
            </div>
        @endif

        <div style="margin-bottom: 15px;">
            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">تاريخ التسجيل</div>
            <div>{{ $customer->created_at->format('Y-m-d H:i') }}</div>
        </div>

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">تعديل البيانات</a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary" style="width: 100%;">العودة للقائمة</a>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">اشتراكات العميل</h3>

        @forelse($customer->subscriptions as $subscription)
            <div style="padding: 15px; margin-bottom: 15px; border: 1px solid #ecf0f1; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                    <div>
                        <div style="font-weight: 600; font-size: 16px;">{{ $subscription->plan->product->name }}</div>
                        <div style="font-size: 13px; color: #7f8c8d;">{{ $subscription->plan->name }}</div>
                    </div>
                    <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'danger' : 'warning') }}">
                        {{ $subscription->status }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #7f8c8d; margin-bottom: 10px;">
                    <div>
                        <strong>البداية:</strong> {{ $subscription->starts_at->format('Y-m-d') }}
                    </div>
                    <div>
                        <strong>النهاية:</strong> {{ $subscription->ends_at->format('Y-m-d') }}
                    </div>
                </div>

                @if($subscription->license)
                    <div style="padding: 10px; background: #f8f9fa; border-radius: 5px; margin-top: 10px;">
                        <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">مفتاح الترخيص</div>
                        <code style="background: white; padding: 8px; border-radius: 4px; display: block; font-size: 14px; color: #3498db;">
                            {{ $subscription->license->license_key }}
                        </code>
                    </div>
                @endif

                <div style="margin-top: 10px;">
                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-sm btn-secondary">عرض التفاصيل</a>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: #95a5a6;">
                لا يوجد اشتراكات لهذا العميل
            </div>
        @endforelse
    </div>
</div>
@endsection
