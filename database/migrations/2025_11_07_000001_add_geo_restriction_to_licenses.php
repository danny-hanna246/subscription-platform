<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            // إضافة حقل للدول المسموح بها (JSON array)
            // مثال: ["US", "SA", "AE", "EG"]
            $table->json('allowed_countries')->nullable()->after('expires_at');

            // إضافة حقل لتمكين/تعطيل التقييد الجغرافي
            $table->boolean('geo_restriction_enabled')->default(false)->after('allowed_countries');
        });

        Schema::table('validation_logs', function (Blueprint $table) {
            // إضافة حقل لحفظ كود الدولة (Country Code)
            $table->string('country_code', 2)->nullable()->after('ip_address');

            // إضافة حقل لحفظ اسم الدولة
            $table->string('country_name', 100)->nullable()->after('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['allowed_countries', 'geo_restriction_enabled']);
        });

        Schema::table('validation_logs', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'country_name']);
        });
    }
};
