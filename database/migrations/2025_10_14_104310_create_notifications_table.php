<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            
            // هذا السطر يقوم بإنشاء notifiable_type و notifiable_id 
            // ويضيف الفهرس المركب تلقائيًا.
            $table->morphs('notifiable'); 
            
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // الفهرس على notifiable_type و notifiable_id محذوف هنا 
            // لأنه مكرر لعمل دالة morphs()
            
            // الفهرس على read_at ضروري ويجب الاحتفاظ به
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
