@extends('layouts.admin')

@section('title', 'إدارة التقييد الجغرافي')
@section('page-title', 'إدارة التقييد الجغرافي للترخيص')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <form action="{{ route('admin.licenses.update-geo-restriction', $license) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- معلومات الترخيص --}}
            <div
                style="background: #e3f2fd; border: 1px solid #90caf9; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                <h3 style="margin: 0 0 15px 0; color: #1976d2; font-size: 18px;">📋 معلومات الترخيص</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                    <div>
                        <strong>مفتاح الترخيص:</strong>
                        <code
                            style="background: white; padding: 6px 10px; border-radius: 4px; display: inline-block; margin-top: 5px; color: #1976d2;">
                            {{ $license->license_key }}
                        </code>
                    </div>
                    <div>
                        <strong>العميل:</strong>
                        <div style="margin-top: 5px;">{{ $license->subscription->customer->name }}</div>
                    </div>
                    <div>
                        <strong>المنتج:</strong>
                        <div style="margin-top: 5px;">{{ $license->subscription->plan->product->name }}</div>
                    </div>
                    <div>
                        <strong>الخطة:</strong>
                        <div style="margin-top: 5px;">{{ $license->subscription->plan->name }}</div>
                    </div>
                </div>
            </div>

            {{-- تفعيل/تعطيل التقييد الجغرافي --}}
            <div class="form-group">
                <label
                    style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 2px solid {{ old('geo_restriction_enabled', $license->geo_restriction_enabled) ? '#28a745' : '#dee2e6' }};">
                    <input type="checkbox" name="geo_restriction_enabled" id="geo_restriction_enabled" value="1"
                        {{ old('geo_restriction_enabled', $license->geo_restriction_enabled) ? 'checked' : '' }}
                        onchange="this.parentElement.style.borderColor = this.checked ? '#28a745' : '#dee2e6'"
                        style="width: 20px; height: 20px; cursor: pointer;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 5px;">
                            🌍 تفعيل التقييد الجغرافي
                        </div>
                        <div style="font-size: 13px; color: #6c757d;">
                            عند التفعيل، سيتم السماح بالوصول فقط من الدول المحددة أدناه
                        </div>
                    </div>
                </label>
            </div>

            {{-- اختيار الدول المسموح بها --}}
            <div class="form-group" id="countries-container"
                style="display: {{ old('geo_restriction_enabled', $license->geo_restriction_enabled) ? 'block' : 'none' }};">
                <label class="form-label">الدول المسموح بها *</label>
                <div
                    style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; max-height: 400px; overflow-y: auto;">

                    {{-- زر تحديد/إلغاء الكل --}}
                    <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px solid #dee2e6;">
                        <button type="button" onclick="selectAllCountries()" class="btn btn-sm btn-secondary"
                            style="margin-left: 10px;">
                            ✓ تحديد الكل
                        </button>
                        <button type="button" onclick="deselectAllCountries()" class="btn btn-sm btn-secondary">
                            ✗ إلغاء الكل
                        </button>
                    </div>

                    @php
                        $currentCountries = old('allowed_countries', $license->allowed_countries ?? []);
                        $countries = \App\Services\GeoIpService::getCountryList();

                        // تقسيم الدول إلى مجموعات
                        $arabCountries = [
                            'SA' => 'Saudi Arabia',
                            'AE' => 'United Arab Emirates',
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
                        ];

                        $westernCountries = [
                            'US' => 'United States',
                            'GB' => 'United Kingdom',
                            'CA' => 'Canada',
                            'DE' => 'Germany',
                            'FR' => 'France',
                            'AU' => 'Australia',
                        ];
                    @endphp

                    {{-- الدول العربية --}}
                    <div style="margin-bottom: 25px;">
                        <h4
                            style="font-size: 14px; font-weight: 600; color: #2c3e50; margin-bottom: 12px; padding: 8px; background: white; border-radius: 4px;">
                            🌙 الدول العربية
                        </h4>
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                            @foreach ($arabCountries as $code => $name)
                                <label
                                    style="display: flex; align-items: center; gap: 8px; padding: 10px; background: white; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#e3f2fd'"
                                    onmouseout="this.style.background='white'">
                                    <input type="checkbox" name="allowed_countries[]" value="{{ $code }}"
                                        {{ in_array($code, $currentCountries) ? 'checked' : '' }} class="country-checkbox"
                                        style="width: 18px; height: 18px; cursor: pointer;">
                                    <span
                                        style="font-size: 20px;">{{ $code === 'SA' ? '🇸🇦' : ($code === 'AE' ? '🇦🇪' : ($code === 'EG' ? '🇪🇬' : '🌍')) }}</span>
                                    <span style="font-size: 13px; font-weight: 500;">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- الدول الغربية --}}
                    <div style="margin-bottom: 25px;">
                        <h4
                            style="font-size: 14px; font-weight: 600; color: #2c3e50; margin-bottom: 12px; padding: 8px; background: white; border-radius: 4px;">
                            🌎 دول أخرى
                        </h4>
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                            @foreach ($westernCountries as $code => $name)
                                <label
                                    style="display: flex; align-items: center; gap: 8px; padding: 10px; background: white; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#e3f2fd'"
                                    onmouseout="this.style.background='white'">
                                    <input type="checkbox" name="allowed_countries[]" value="{{ $code }}"
                                        {{ in_array($code, $currentCountries) ? 'checked' : '' }} class="country-checkbox"
                                        style="width: 18px; height: 18px; cursor: pointer;">
                                    <span
                                        style="font-size: 20px;">{{ $code === 'US' ? '🇺🇸' : ($code === 'GB' ? '🇬🇧' : '🌍') }}</span>
                                    <span style="font-size: 13px; font-weight: 500;">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 10px;">
                    💡 حدد الدول التي يُسمح للعملاء فيها باستخدام هذا الترخيص. إذا لم تحدد أي دولة، سيتم رفض جميع محاولات
                    التحقق.
                </small>

                @error('allowed_countries')
                    <div style="color: #e74c3c; font-size: 13px; margin-top: 8px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- تحذير مهم --}}
            <div class="alert"
                style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; margin-bottom: 20px;">
                <strong>⚠️ تنبيه مهم:</strong>
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    <li>تأكد من تحديد الدول بشكل صحيح قبل الحفظ</li>
                    <li>العملاء خارج الدول المحددة لن يتمكنوا من استخدام الترخيص</li>
                    <li>يتم التحقق من الموقع الجغرافي عند كل محاولة تحقق من الترخيص</li>
                </ul>
            </div>

            {{-- أزرار الحفظ والإلغاء --}}
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('admin.licenses.show', $license) }}" class="btn btn-secondary">
                    إلغاء
                </a>
                <button type="submit" class="btn btn-primary" style="min-width: 150px;">
                    💾 حفظ التغييرات
                </button>
            </div>
        </form>
    </div>

    <script>
        // إظهار/إخفاء قائمة الدول عند تفعيل/تعطيل التقييد
        document.getElementById('geo_restriction_enabled').addEventListener('change', function() {
            const container = document.getElementById('countries-container');
            container.style.display = this.checked ? 'block' : 'none';
        });

        // تحديد جميع الدول
        function selectAllCountries() {
            document.querySelectorAll('.country-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        // إلغاء تحديد جميع الدول
        function deselectAllCountries() {
            document.querySelectorAll('.country-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
@endsection
