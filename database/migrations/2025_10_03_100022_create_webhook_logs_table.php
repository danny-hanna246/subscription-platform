<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 100);
            $table->string('event_type', 100);
            $table->json('payload');
            $table->timestamp('received_at')->useCurrent();
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error')->nullable();

            $table->index(['source', 'event_type']);
            $table->index('processed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
