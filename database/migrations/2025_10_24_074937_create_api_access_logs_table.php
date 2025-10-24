<?php
// database/migrations/2025_10_24_000002_create_api_access_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->nullable()->constrained()->onDelete('set null');
            $table->string('endpoint', 255);
            $table->string('method', 10);
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->integer('status_code');
            $table->float('response_time', 8, 2)->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['api_key_id', 'created_at']);
            $table->index(['status_code', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_access_logs');
    }
};
