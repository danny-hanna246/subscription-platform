<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licensed_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->string('device_id', 255);
            $table->text('device_info')->nullable();
            $table->dateTime('activated_at');
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['license_id', 'device_id']);
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licensed_devices');
    }
};
