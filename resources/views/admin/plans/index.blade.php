@extends('layouts.admin')

@section('title', 'الخطط')
@section('page-title', 'إدارة الخطط')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة الخطط</h3>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">إضافة خطة جديدة</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px;">
        @forelse($plans as $plan)
            <div style="border: 1px solid #ecf0f1; border-radius: 8px; padding: 20px; background: white;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h4 style="margin: 0; font-size: 18px; color: #2c3e50;">{{ $plan->name }}</h4>
                        <p style="margin: 5px 0 0; font-size: 13px; color: #7f8c8d;">{{ $plan->product->name }}</p>
                    </div>
                    <span class="badge badge-{{ $plan->active ? 'success' : 'danger' }}">
                        {{ $plan->active ? 'نشط' : 'متوقف' }}
                    </span>
                </div>

                <div style="margin-bottom: 15px;">
                    <div style="font-size: 32px; font-weight: 700; color: #3498db;">
                        ${{ number_format($plan->price, 2) }}
                    </div>
                    <div style="font-size: 13px; color: #7f8c8d;">
                        {{ $plan->duration_days }} يوم / {{ $plan->currency }}
                    </div>
                </div>

                <div style="border-top: 1px solid #ecf0f1; padding-top: 15px; margin-bottom: 15px;">
                    <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 8px;">
                        ✓ {{ $plan->user_limit }} مستخدم
                    </div>
                    <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 8px;">
                        ✓ {{ $plan->device_limit }} جهاز
                    </div>
                    @if($plan->features && count($plan->features) > 0)
                        @foreach(array_slice($plan->features, 0, 2) as $feature)
                            <div style="font-size: 13px; color: #7f8c8d; margin-bottom: 8px;">
                                ✓ {{ $feature }}
                            </div>
                        @endforeach
                    @endif
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-secondary" style="flex: 1;">تعديل</a>
                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" style="flex: 1;" onsubmit="return confirm('هل أنت متأكد؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" style="width: 100%;">حذف</button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #95a5a6;">
                لا توجد خطط. <a href="{{ route('admin.plans.create') }}">إضافة خطة جديدة</a>
            </div>
        @endforelse
    </div>

    @if($plans->hasPages())
        <div style="padding: 20px;">
            {{ $plans->links() }}
        </div>
    @endif
</div>
@endsection
