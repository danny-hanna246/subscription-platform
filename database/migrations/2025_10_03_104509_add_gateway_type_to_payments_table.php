<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway_type', 50)->nullable()->after('gateway')
                ->comment('stripe, paypal, syriatel_cash, mtn_cash, etc');
        });

        Schema::table('subscription_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE subscription_requests MODIFY payment_method ENUM('online_stripe', 'online_paypal', 'cash', 'local_gateway') DEFAULT 'cash'");
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('gateway_type');
        });
    }
};
