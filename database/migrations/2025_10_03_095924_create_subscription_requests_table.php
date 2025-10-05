<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('restrict');
            $table->enum('payment_method', ['online', 'cash']);
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed'])
                ->default('pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('payment_token', 255)->nullable()->unique();
            $table->string('coupon_code', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('payment_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_requests');
    }
};
