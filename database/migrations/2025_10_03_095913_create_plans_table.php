<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name', 80);
            $table->string('slug', 100);
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->integer('duration_days');
            $table->integer('user_limit')->default(1);
            $table->integer('device_limit')->default(1);
            $table->json('features')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'active']);
            $table->unique(['product_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
