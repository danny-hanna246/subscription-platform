@extends('layouts.admin')

@section('title', 'المنتجات')
@section('page-title', 'إدارة المنتجات')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة المنتجات</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">إضافة منتج جديد</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>اسم المنتج</th>
                    <th>الرابط (Slug)</th>
                    <th>عدد الخطط</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $product->name }}</div>
                            @if($product->description)
                                <div style="font-size: 13px; color: #7f8c8d;">{{ Str::limit($product->description, 50) }}</div>
                            @endif
                        </td>
                        <td><code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $product->slug }}</code></td>
                        <td><span class="badge badge-info">{{ $product->plans_count }} خطة</span></td>
                        <td>{{ $product->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-secondary">تعديل</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #95a5a6;">
                            لا توجد منتجات. <a href="{{ route('admin.products.create') }}">إضافة منتج جديد</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div style="padding: 20px;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
