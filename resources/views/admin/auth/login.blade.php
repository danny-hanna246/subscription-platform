<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>تسجيل الدخول</h1>
                <p>لوحة تحكم {{ config('app.name') }}</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="admin@example.com"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">كلمة المرور</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                        placeholder="••••••••"
                    >
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="remember" value="1">
                        <span style="font-size: 14px;">تذكرني</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    تسجيل الدخول
                </button>
            </form>
        </div>
    </div>
</body>
</html>
