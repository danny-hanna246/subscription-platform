<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * GeoIP Service للتحقق من الموقع الجغرافي باستخدام IP Address
 *
 * يدعم عدة خدمات:
 * 1. ip-api.com (مجاني - 45 طلب/دقيقة)
 * 2. ipapi.co (مجاني - 1000 طلب/يوم)
 * 3. ipinfo.io (مجاني - 50,000 طلب/شهر)
 */
class GeoIpService
{
    /**
     * الحصول على معلومات الموقع الجغرافي من IP
     *
     * @param string $ip
     * @return array|null
     */
    public function getLocation(string $ip): ?array
    {
        // تجاهل IPs المحلية
        if ($this->isLocalIp($ip)) {
            return [
                'country_code' => 'LOCAL',
                'country_name' => 'Local Network',
                'ip' => $ip,
                'is_local' => true,
            ];
        }

        // التحقق من الكاش أولاً (حفظ لمدة 24 ساعة)
        $cacheKey = "geoip_{$ip}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // محاولة الحصول على الموقع من الخدمات المختلفة
        $location = $this->tryIpApi($ip)
            ?? $this->tryIpApiCo($ip)
            ?? $this->tryIpInfo($ip);

        if ($location) {
            // حفظ في الكاش لمدة 24 ساعة
            Cache::put($cacheKey, $location, now()->addHours(24));
        }

        return $location;
    }

    /**
     * التحقق من صلاحية IP للاستخدام في دولة معينة
     *
     * @param string $ip
     * @param array $allowedCountries - مثال: ['US', 'SA', 'AE']
     * @return bool
     */
    public function isAllowedCountry(string $ip, array $allowedCountries): bool
    {
        if (empty($allowedCountries)) {
            return true; // إذا لم يتم تحديد دول، السماح للجميع
        }

        $location = $this->getLocation($ip);

        if (!$location || !isset($location['country_code'])) {
            // إذا فشل التحقق من الموقع، نسمح بالوصول (لتجنب blocking خاطئ)
            Log::warning("GeoIP check failed for IP: {$ip}. Allowing access by default.");
            return true;
        }

        // IPs المحلية دائماً مسموحة
        if ($location['is_local'] ?? false) {
            return true;
        }

        return in_array($location['country_code'], $allowedCountries);
    }

    /**
     * محاولة الحصول على الموقع من ip-api.com
     *
     * @param string $ip
     * @return array|null
     */
    private function tryIpApi(string $ip): ?array
    {
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,country,countryCode,regionName,city,zip,lat,lon,timezone,isp,query',
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                $data = $response->json();
                return [
                    'country_code' => $data['countryCode'],
                    'country_name' => $data['country'],
                    'region' => $data['regionName'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'isp' => $data['isp'] ?? null,
                    'ip' => $ip,
                    'provider' => 'ip-api.com',
                    'is_local' => false,
                ];
            }
        } catch (\Exception $e) {
            Log::debug("ip-api.com failed for {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * محاولة الحصول على الموقع من ipapi.co
     *
     * @param string $ip
     * @return array|null
     */
    private function tryIpApiCo(string $ip): ?array
    {
        try {
            $response = Http::timeout(3)->get("https://ipapi.co/{$ip}/json/");

            if ($response->successful() && !isset($response->json()['error'])) {
                $data = $response->json();
                return [
                    'country_code' => $data['country_code'] ?? null,
                    'country_name' => $data['country_name'] ?? null,
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'isp' => $data['org'] ?? null,
                    'ip' => $ip,
                    'provider' => 'ipapi.co',
                    'is_local' => false,
                ];
            }
        } catch (\Exception $e) {
            Log::debug("ipapi.co failed for {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * محاولة الحصول على الموقع من ipinfo.io
     *
     * @param string $ip
     * @return array|null
     */
    private function tryIpInfo(string $ip): ?array
    {
        try {
            // يمكن إضافة API token من .env إذا كان متوفراً
            $token = config('services.ipinfo.token');
            $url = $token
                ? "https://ipinfo.io/{$ip}?token={$token}"
                : "https://ipinfo.io/{$ip}";

            $response = Http::timeout(3)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $loc = isset($data['loc']) ? explode(',', $data['loc']) : [null, null];

                return [
                    'country_code' => $data['country'] ?? null,
                    'country_name' => $data['country'] ?? null, // ipinfo لا يعطي اسم الدولة كامل
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => $loc[0] ?? null,
                    'longitude' => $loc[1] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'isp' => $data['org'] ?? null,
                    'ip' => $ip,
                    'provider' => 'ipinfo.io',
                    'is_local' => false,
                ];
            }
        } catch (\Exception $e) {
            Log::debug("ipinfo.io failed for {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * التحقق من IP محلي
     *
     * @param string $ip
     * @return bool
     */
    private function isLocalIp(string $ip): bool
    {
        $localRanges = [
            '127.0.0.0/8',    // Loopback
            '10.0.0.0/8',     // Private
            '172.16.0.0/12',  // Private
            '192.168.0.0/16', // Private
            '::1',            // IPv6 loopback
            'fe80::/10',      // IPv6 link-local
        ];

        // التحقق من IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            if ($ip === '127.0.0.1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
                return true;
            }
        }

        // التحقق من IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if ($ip === '::1') {
                return true;
            }
        }

        return false;
    }

    /**
     * الحصول على قائمة الدول المتاحة (ISO 3166-1 alpha-2)
     *
     * @return array
     */
    public static function getCountryList(): array
    {
        return [
            'AF' => 'Afghanistan',
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'EG' => 'Egypt',
            'IQ' => 'Iraq',
            'JO' => 'Jordan',
            'KW' => 'Kuwait',
            'LB' => 'Lebanon',
            'OM' => 'Oman',
            'QA' => 'Qatar',
            'SY' => 'Syria',
            'YE' => 'Yemen',
            'BH' => 'Bahrain',
            'PS' => 'Palestine',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'DE' => 'Germany',
            'FR' => 'France',
            'CA' => 'Canada',
            'AU' => 'Australia',
            // يمكن إضافة المزيد من الدول حسب الحاجة
        ];
    }
}
