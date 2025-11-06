<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class LicenseGeoRestrictionController extends Controller
{
    /**
     * عرض صفحة إدارة التقييد الجغرافي
     */
    public function edit(License $license)
    {
        $license->load(['subscription.customer', 'subscription.plan.product']);
        return view('admin.licenses.geo-restriction', compact('license'));
    }

    /**
     * تحديث إعدادات التقييد الجغرافي
     */
    public function update(Request $request, License $license)
    {
        $request->validate([
            'geo_restriction_enabled' => 'boolean',
            'allowed_countries' => 'nullable|array',
            'allowed_countries.*' => 'string|size:2', // ISO 3166-1 alpha-2 codes
        ]);

        $geoEnabled = $request->has('geo_restriction_enabled');
        $allowedCountries = $request->input('allowed_countries', []);

        // التحقق من أنه تم تحديد دول إذا كان التقييد مفعل
        if ($geoEnabled && empty($allowedCountries)) {
            return back()->withErrors([
                'allowed_countries' => 'يجب تحديد دولة واحدة على الأقل عند تفعيل التقييد الجغرافي'
            ])->withInput();
        }

        // تحديث الترخيص
        $license->update([
            'geo_restriction_enabled' => $geoEnabled,
            'allowed_countries' => $geoEnabled ? $allowedCountries : null,
        ]);

        // تسجيل في Audit Log
        AuditLog::log('License', $license->id, 'update_geo_restriction', [
            'geo_restriction_enabled' => $geoEnabled,
            'allowed_countries' => $allowedCountries,
            'countries_count' => count($allowedCountries),
        ]);

        return redirect()
            ->route('admin.licenses.show', $license)
            ->with('success', 'تم تحديث إعدادات التقييد الجغرافي بنجاح');
    }

    /**
     * تعطيل التقييد الجغرافي بسرعة
     */
    public function disable(License $license)
    {
        $license->update([
            'geo_restriction_enabled' => false,
        ]);

        AuditLog::log('License', $license->id, 'disable_geo_restriction', null);

        return back()->with('success', 'تم تعطيل التقييد الجغرافي');
    }

    /**
     * عرض إحصائيات التحقق الجغرافي
     */
    public function stats(License $license)
    {
        // الحصول على سجلات التحقق من الـ 30 يوم الماضية
        $validationLogs = $license->validationLogs()
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        // تجميع حسب الدولة
        $countryStats = $validationLogs
            ->groupBy('country_code')
            ->map(function ($logs, $countryCode) {
                return [
                    'country_code' => $countryCode,
                    'country_name' => $logs->first()->country_name ?? 'Unknown',
                    'total_attempts' => $logs->count(),
                    'successful' => $logs->where('status', 'valid')->count(),
                    'blocked' => $logs->where('status', 'geo_restricted')->count(),
                    'last_attempt' => $logs->max('created_at'),
                ];
            })
            ->sortByDesc('total_attempts')
            ->values();

        return view('admin.licenses.geo-stats', compact('license', 'countryStats'));
    }
}
