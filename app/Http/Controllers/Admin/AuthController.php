<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // جلب المدير حسب الإيميل
        $admin = Admin::where('email', $request->email)->first();

        // تحقق من وجوده ومن كلمة المرور
        $hashed = $admin ? ($admin->password ?? $admin->password_hash ?? null) : null;
        if (!$admin || ! $hashed || ! Hash::check($request->password, $hashed)) {
            return back()->withErrors(['email' => 'البيانات غير صحيحة'])->withInput();
        }

        if (isset($admin->is_active) && ! $admin->is_active) {
            return back()->withErrors(['email' => 'حسابك موقوف'])->withInput();
        }

        // حدّث آخر تسجيل دخول (لو لديك هذه الدالة)
        if (method_exists($admin, 'updateLastLogin')) {
            $admin->updateLastLogin();
        }

        // **المهم**: سجّل الدخول عبر guard الخاص بالـ admin
        Auth::guard('admin')->login($admin, $request->filled('remember'));

        // أمنية: تجديد الجلسة بعد تسجيل الدخول
        $request->session()->regenerate();

        // إعادة التوجيه إلى المكان المقصود أو dashboard
        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        // خروج من guard admin
        Auth::guard('admin')->logout();

        // إبطال الجلسة وتجديد التوكن
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
