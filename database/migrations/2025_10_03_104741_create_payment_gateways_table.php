<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Stripe, PayPal, Syriatel Cash, etc
            $table->string('code', 50)->unique(); // stripe, paypal, syriatel_cash
            $table->enum('type', ['international', 'local'])->default('international');
            $table->text('description')->nullable();
            $table->text('config')->nullable(); // API keys, secrets, etc (encrypted)
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->json('supported_currencies')->nullable();
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
