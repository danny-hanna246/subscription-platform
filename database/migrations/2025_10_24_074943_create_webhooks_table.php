<?php
// database/migrations/2025_10_24_000003_create_webhooks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained()->onDelete('cascade');
            $table->string('url', 500);
            $table->json('events'); // subscription.created, subscription.renewed, etc.
            $table->string('secret', 255);
            $table->enum('status', ['active', 'paused', 'failed'])->default('active');
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->timestamps();

            $table->index(['api_key_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
