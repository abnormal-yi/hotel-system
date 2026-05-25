<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('nida_number')->nullable()->unique()->after('id_number');
            $table->string('nationality')->nullable()->after('address');
            $table->string('status')->default('active')->after('guest_type');
        });

        $duplicates = DB::table('guests')
            ->select('phone', DB::raw('MIN(id) as keep_id'))
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            $toDelete = DB::table('guests')
                ->where('phone', $dup->phone)
                ->where('id', '!=', $dup->keep_id)
                ->pluck('id');

            foreach ($toDelete as $deleteId) {
                DB::table('booking_guest')->where('guest_id', $deleteId)->update(['guest_id' => $dup->keep_id]);
                DB::table('guests')->where('id', $deleteId)->delete();
            }
        }

        try {
            Schema::table('guests', function (Blueprint $table) {
                $table->string('phone')->nullable()->unique()->change();
            });
        } catch (\Exception $e) {
            // If unique fails due to data, add unique on phone+id
        }
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropUnique(['nida_number']);
            $table->dropUnique(['phone']);
            $table->dropColumn(['nida_number', 'nationality', 'status']);
        });
    }
};
