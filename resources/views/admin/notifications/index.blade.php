@extends('layouts.admin')

@section('title', 'الإشعارات')
@section('page-title', 'الإشعارات')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">الإشعارات</h3>
            @if ($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">
                        تمييز الكل كمقروء
                    </button>
                </form>
            @endif
        </div>

        <div style="padding: 0;">
            @forelse($notifications as $notification)
                <div
                    style="padding: 15px 20px; border-bottom: 1px solid #ecf0f1; {{ $notification->read_at ? 'opacity: 0.7;' : 'background: #fffbf0;' }}">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 5px;">
                                {{ $notification->data['title'] ?? 'إشعار' }}
                            </div>
                            <div style="font-size: 14px; color: #7f8c8d; margin-bottom: 10px;">
                                {{ $notification->data['message'] ?? '' }}
                            </div>
                            <div style="font-size: 12px; color: #95a5a6;">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            @if (!$notification->read_at)
                                <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary">تمييز كمقروء</button>
                                </form>
                            @endif
                            @if (isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-primary">عرض</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding: 40px; text-align: center; color: #95a5a6;">
                    لا توجد إشعارات
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div style="padding: 20px;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
