<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->boolean('blacklisted')->default(false)->after('guest_type');
            $table->timestamp('blacklisted_at')->nullable()->after('blacklisted');
            $table->text('blacklist_reason')->nullable()->after('blacklisted_at');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('late_fee', 10, 2)->default(0)->after('paid_amount');
            $table->timestamp('late_fee_applied_at')->nullable()->after('late_fee');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['blacklisted', 'blacklisted_at', 'blacklist_reason']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['late_fee', 'late_fee_applied_at']);
        });
    }
};
