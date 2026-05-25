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
            $table->integer('total_bookings')->default(0)->after('status');
            $table->decimal('total_spent', 12, 0)->default(0)->after('total_bookings');
            $table->timestamp('last_visit_at')->nullable()->after('total_spent');
        });

        DB::table('guests')->where('email', 'N/A')->orWhere('email', 'n/a')->update(['email' => null]);

        $guests = DB::table('guests')->get();
        foreach ($guests as $guest) {
            $stats = DB::table('booking_guest')
                ->join('bookings', 'booking_guest.booking_id', '=', 'bookings.id')
                ->where('booking_guest.guest_id', $guest->id)
                ->whereIn('bookings.status', ['checked_in', 'checked_out', 'confirmed'])
                ->selectRaw('COUNT(*) as total_bookings, COALESCE(SUM(bookings.total_amount), 0) as total_spent, MAX(bookings.check_in) as last_visit')
                ->first();

            $update = [
                'total_bookings' => $stats->total_bookings ?? 0,
                'total_spent' => $stats->total_spent ?? 0,
            ];
            if ($stats->last_visit) {
                $update['last_visit_at'] = $stats->last_visit;
            }

            if ($stats->total_bookings > 0) {
                $update['status'] = 'active';
            } else {
                $update['status'] = $guest->blacklisted ? 'blacklisted' : 'new';
            }

            DB::table('guests')->where('id', $guest->id)->update($update);
        }

        try {
            Schema::table('guests', function (Blueprint $table) {
                $table->dropUnique(['phone']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('guests', function (Blueprint $table) {
                $table->string('phone', 20)->nullable()->unique()->change();
            });
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['total_bookings', 'total_spent', 'last_visit_at']);
        });
    }
};
