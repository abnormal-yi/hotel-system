<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('housekeeping_tasks', function (Blueprint $table) {
            $table->string('priority')->default('medium')->after('task_type');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete()->after('assigned_to');
            $table->string('room_number')->nullable()->after('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('housekeeping_tasks', function (Blueprint $table) {
            $table->dropColumn(['priority', 'assigned_by', 'room_number']);
        });
    }
};
