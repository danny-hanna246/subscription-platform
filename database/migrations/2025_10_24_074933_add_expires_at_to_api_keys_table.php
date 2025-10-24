<?php
// database/migrations/2025_10_24_000001_add_expires_at_to_api_keys_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->timestamp('last_used_at')->nullable()->after('expires_at');
            $table->integer('usage_count')->default(0)->after('last_used_at');
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'last_used_at', 'usage_count']);
        });
    }
};
