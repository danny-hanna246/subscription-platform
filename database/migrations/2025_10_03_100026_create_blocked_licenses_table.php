<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->string('reason', 255);
            $table->foreignId('revoked_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->dateTime('revoked_at');
            $table->dateTime('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('license_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_licenses');
    }
};
