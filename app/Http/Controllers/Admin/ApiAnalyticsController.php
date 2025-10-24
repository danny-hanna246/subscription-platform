<?php
// app/Http/Controllers/Admin/ApiAnalyticsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiAnalyticsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_keys' => ApiKey::count(),
            'active_keys' => ApiKey::active()->count(),
            'expired_keys' => ApiKey::expired()->count(),
            'total_requests_today' => ApiAccessLog::whereDate('created_at', today())->count(),
            'failed_requests_today' => ApiAccessLog::whereDate('created_at', today())
                ->where('status_code', '>=', 400)
                ->count(),
        ];

        // الطلبات حسب الوقت (آخر 24 ساعة)
        $requestsByHour = ApiAccessLog::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // أكثر Endpoints استخداماً
        $topEndpoints = ApiAccessLog::select('endpoint', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('endpoint')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // أكثر API Keys استخداماً
        $topApiKeys = ApiKey::withCount(['accessLogs' => function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        }])
            ->orderByDesc('access_logs_count')
            ->limit(10)
            ->get();

        return view('admin.api-analytics.index', compact(
            'stats',
            'requestsByHour',
            'topEndpoints',
            'topApiKeys'
        ));
    }

    /**
     * تفاصيل API Key معين
     */
    public function show(ApiKey $apiKey)
    {
        $apiKey->load(['accessLogs' => function ($query) {
            $query->latest()->limit(100);
        }]);

        $stats = [
            'total_requests' => $apiKey->usage_count,
            'requests_today' => $apiKey->accessLogs()
                ->whereDate('created_at', today())
                ->count(),
            'failed_requests' => $apiKey->accessLogs()
                ->where('status_code', '>=', 400)
                ->count(),
            'average_response_time' => $apiKey->accessLogs()
                ->whereNotNull('response_time')
                ->avg('response_time'),
        ];

        return view('admin.api-analytics.show', compact('apiKey', 'stats'));
    }
}
