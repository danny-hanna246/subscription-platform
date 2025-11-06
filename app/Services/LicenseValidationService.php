<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicensedDevice;
use App\Models\ValidationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LicenseValidationService
{
    protected $geoIpService;

    public function __construct(GeoIpService $geoIpService)
    {
        $this->geoIpService = $geoIpService;
    }

    public function validate($licenseKey, $deviceId, $deviceInfo = null)
    {
        $userIp = request()->ip();
        $geoLocation = null;

        // البحث عن الترخيص
        $license = License::with(['subscription.plan', 'devices'])
            ->where('license_key', $licenseKey)
            ->first();

        // الترخيص غير موجود
        if (!$license) {
            $this->logValidation($licenseKey, null, 'not_found', $deviceId, 404, null, null);

            return [
                'valid' => false,
                'status' => 'not_found',
                'message' => 'License key not found.',
                'data' => null,
                'response_code' => 404,
            ];
        }

        // ==================================================
        // 🌍 التحقق من التقييد الجغرافي
        // ==================================================
        if ($license->geo_restriction_enabled && !empty($license->allowed_countries)) {
            $geoLocation = $this->geoIpService->getLocation($userIp);

            // التحقق من صلاحية الدولة
            if (!$this->geoIpService->isAllowedCountry($userIp, $license->allowed_countries)) {
                $this->logValidation(
                    $licenseKey,
                    $license->id,
                    'geo_restricted',
                    $deviceId,
                    403,
                    $geoLocation['country_code'] ?? null,
                    $geoLocation['country_name'] ?? null
                );

                return [
                    'valid' => false,
                    'status' => 'geo_restricted',
                    'message' => 'This license is not available in your country.',
                    'data' => [
                        'your_country' => $geoLocation['country_name'] ?? 'Unknown',
                        'allowed_countries' => $license->allowed_countries,
                    ],
                    'response_code' => 403,
                ];
            }
        }

        // التحقق من حالة الترخيص
        if ($license->status === 'revoked') {
            $this->logValidation(
                $licenseKey,
                $license->id,
                'revoked',
                $deviceId,
                403,
                $geoLocation['country_code'] ?? null,
                $geoLocation['country_name'] ?? null
            );

            return [
                'valid' => false,
                'status' => 'revoked',
                'message' => 'This license has been revoked.',
                'data' => null,
                'response_code' => 403,
            ];
        }

        // التحقق من تاريخ الانتهاء
        if ($license->expires_at && Carbon::now()->greaterThan($license->expires_at)) {
            $license->update(['status' => 'expired']);
            $this->logValidation(
                $licenseKey,
                $license->id,
                'expired',
                $deviceId,
                403,
                $geoLocation['country_code'] ?? null,
                $geoLocation['country_name'] ?? null
            );

            return [
                'valid' => false,
                'status' => 'expired',
                'message' => 'This license has expired.',
                'data' => [
                    'expired_at' => $license->expires_at->toDateTimeString(),
                ],
                'response_code' => 403,
            ];
        }

        // التحقق من الاشتراك
        $subscription = $license->subscription;
        if (!$subscription->isActive()) {
            $this->logValidation(
                $licenseKey,
                $license->id,
                'subscription_inactive',
                $deviceId,
                403,
                $geoLocation['country_code'] ?? null,
                $geoLocation['country_name'] ?? null
            );

            return [
                'valid' => false,
                'status' => 'subscription_inactive',
                'message' => 'Subscription is not active.',
                'data' => null,
                'response_code' => 403,
            ];
        }

        // التحقق من حد الأجهزة
        $existingDevice = $license->devices()->where('device_id', $deviceId)->first();

        if (!$existingDevice) {
            $deviceLimit = $subscription->plan->device_limit;
            $currentDevicesCount = $license->devices()->count();

            if ($currentDevicesCount >= $deviceLimit) {
                $this->logValidation(
                    $licenseKey,
                    $license->id,
                    'device_limit_reached',
                    $deviceId,
                    403,
                    $geoLocation['country_code'] ?? null,
                    $geoLocation['country_name'] ?? null
                );

                return [
                    'valid' => false,
                    'status' => 'device_limit_reached',
                    'message' => 'Device limit reached for this license.',
                    'data' => [
                        'device_limit' => $deviceLimit,
                        'current_devices' => $currentDevicesCount,
                    ],
                    'response_code' => 403,
                ];
            }

            // تسجيل الجهاز الجديد
            $existingDevice = LicensedDevice::create([
                'license_id' => $license->id,
                'device_id' => $deviceId,
                'device_info' => $deviceInfo,
                'activated_at' => Carbon::now(),
                'last_seen_at' => Carbon::now(),
            ]);
        } else {
            // تحديث آخر ظهور للجهاز
            $existingDevice->updateLastSeen();
        }

        // الترخيص صالح
        $this->logValidation(
            $licenseKey,
            $license->id,
            'valid',
            $deviceId,
            200,
            $geoLocation['country_code'] ?? null,
            $geoLocation['country_name'] ?? null
        );

        return [
            'valid' => true,
            'status' => 'active',
            'message' => 'License is valid. Access granted.',
            'data' => [
                'plan_name' => $subscription->plan->name,
                'expires_at' => $license->expires_at?->toDateTimeString(),
                'allowed_users' => $subscription->plan->user_limit,
                'allowed_devices' => $subscription->plan->device_limit,
                'current_devices' => $license->devices()->count(),
                'remaining_days' => $subscription->remainingDays(),
                'geo_restriction_enabled' => $license->geo_restriction_enabled,
            ],
            'response_code' => 200,
        ];
    }

    protected function logValidation($licenseKey, $licenseId, $status, $deviceId, $responseCode, $countryCode = null, $countryName = null)
    {
        ValidationLog::create([
            'license_key_attempted' => $licenseKey,
            'license_id' => $licenseId,
            'status' => $status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_id' => $deviceId,
            'response_code' => $responseCode,
            'country_code' => $countryCode,
            'country_name' => $countryName,
        ]);
    }
}
