<?php
// database/migrations/2025_10_24_000001_add_expires_at_to_api_keys_table.php
// database/migrations/xxxx_xx_xx_add_api_keys_usage_columns.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            if (!Schema::hasColumn('api_keys', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('api_keys', 'last_used_at')) {
                $table->timestamp('last_used_at')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('api_keys', 'usage_count')) {
                $table->integer('usage_count')->default(0)->after('last_used_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'last_used_at', 'usage_count']);
        });
    }
};
