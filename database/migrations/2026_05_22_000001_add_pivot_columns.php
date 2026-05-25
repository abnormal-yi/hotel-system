<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_room', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('room_id');
            $table->date('check_in')->nullable()->after('price');
            $table->date('check_out')->nullable()->after('check_in');
        });
    }

    public function down(): void
    {
        Schema::table('booking_room', function (Blueprint $table) {
            $table->dropColumn(['price', 'check_in', 'check_out']);
        });
    }
};
