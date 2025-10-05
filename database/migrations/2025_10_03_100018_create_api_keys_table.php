<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('client_name', 150);
            $table->string('api_key', 255)->unique();
            $table->string('secret_hash', 255)->nullable();
            $table->text('allowed_ips')->nullable();
            $table->json('scopes')->nullable();
            $table->enum('status', ['active', 'revoked', 'suspended'])->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('api_key');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
