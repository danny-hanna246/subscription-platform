@extends('layouts.admin')

@section('title', 'تفاصيل الطلب')
@section('page-title', 'تفاصيل طلب الاشتراك #' . $subscriptionRequest->id)

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <div>
        <div class="card" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">معلومات العميل</h3>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الاسم</div>
                <div style="font-weight: 600;">{{ $subscriptionRequest->customer->name }}</div>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">البريد الإلكتروني</div>
                <div>{{ $subscriptionRequest->customer->email }}</div>
            </div>

            @if($subscriptionRequest->customer->phone)
                <div style="margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الهاتف</div>
                    <div>{{ $subscriptionRequest->customer->phone }}</div>
                </div>
            @endif

            @if($subscriptionRequest->customer->company_name)
                <div style="margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الشركة</div>
                    <div>{{ $subscriptionRequest->customer->company_name }}</div>
                </div>
            @endif
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">تفاصيل الخطة</h3>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">المنتج</div>
                <div style="font-weight: 600;">{{ $subscriptionRequest->plan->product->name }}</div>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الخطة</div>
                <div>{{ $subscriptionRequest->plan->name }}</div>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">المدة</div>
                <div>{{ $subscriptionRequest->plan->duration_days }} يوم</div>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">السعر الأصلي</div>
                <div>${{ number_format($subscriptionRequest->plan->price, 2) }}</div>
            </div>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">معلومات الدفع</h3>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">طريقة الدفع</div>
                <span class="badge badge-{{ $subscriptionRequest->payment_method === 'cash' ? 'warning' : 'info' }}">
                    {{ $subscriptionRequest->payment_method === 'cash' ? 'نقدي' : 'إلكتروني' }}
                </span>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">المبلغ النهائي</div>
                <div style="font-size: 24px; font-weight: 700; color: #3498db;">${{ number_format($subscriptionRequest->amount, 2) }}</div>
            </div>

            @if($subscriptionRequest->coupon_code)
                <div style="margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">كوبون الخصم</div>
                    <code style="background: #f8f9fa; padding: 8px; border-radius: 4px; display: inline-block;">
                        {{ $subscriptionRequest->coupon_code }}
                    </code>
                </div>
            @endif

            <div style="margin-bottom: 15px;">
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">الحالة</div>
                <span class="badge badge-{{ $subscriptionRequest->status === 'completed' ? 'success' : 'warning' }}">
                    {{ $subscriptionRequest->status }}
                </span>
            </div>

            @if($subscriptionRequest->notes)
                <div style="margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">ملاحظات</div>
                    <div style="background: #fff3cd; padding: 10px; border-radius: 5px; color: #856404;">
                        {{ $subscriptionRequest->notes }}
                    </div>
                </div>
            @endif
        </div>

        @if($subscriptionRequest->subscription)
            <div class="card" style="background: #d4edda; border: 1px solid #c3e6cb;">
                <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #c3e6cb; color: #155724;">تم إنشاء الاشتراك</h3>

                <div style="margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #155724; margin-bottom: 5px;">رقم الاشتراك</div>
                    <div style="font-weight: 600;">#{{ $subscriptionRequest->subscription->id }}</div>
                </div>

                @if($subscriptionRequest->subscription->license)
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 12px; color: #155724; margin-bottom: 5px;">مفتاح الترخيص</div>
                        <code style="background: white; padding: 8px; border-radius: 4px; display: block; color: #3498db; font-size: 14px;">
                            {{ $subscriptionRequest->subscription->license->license_key }}
                        </code>
                    </div>
                @endif

                <div style="margin-top: 15px;">
                    <a href="{{ route('admin.subscriptions.show', $subscriptionRequest->subscription) }}" class="btn btn-success" style="width: 100%;">
                        عرض تفاصيل الاشتراك
                    </a>
                </div>
            </div>
        @endif

        @if($subscriptionRequest->status === 'pending' && $subscriptionRequest->payment_method === 'cash')
            <div class="card" style="margin-top: 20px;">
                <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">إجراءات الطلب</h3>

                <form action="{{ route('admin.subscription-requests.approve', $subscriptionRequest) }}" method="POST" style="margin-bottom: 10px;">
                    @csrf
                    <button type="submit" class="btn btn-success" style="width: 100%;" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                        الموافقة وتفعيل الاشتراك
                    </button>
                </form>

                <button onclick="document.getElementById('rejectForm').style.display='block'" class="btn btn-danger" style="width: 100%;">
                    رفض الطلب
                </button>

                <div id="rejectForm" style="display: none; margin-top: 15px; padding: 15px; background: #f8d7da; border-radius: 5px;">
                    <form action="{{ route('admin.subscription-requests.reject', $subscriptionRequest) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">سبب الرفض *</label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="أدخل سبب رفض الطلب..."></textarea>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                            <button type="button" onclick="document.getElementById('rejectForm').style.display='none'" class="btn btn-secondary">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<div style="margin-top: 20px;">
    <a href="{{ route('admin.subscription-requests.index') }}" class="btn btn-secondary">العودة للقائمة</a>
</div>
@endsection
