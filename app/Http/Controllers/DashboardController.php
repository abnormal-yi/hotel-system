<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\FeatureFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $data = [];

        $uniqueRooms = Room::orderBy('room_number')->get();
        $usedStatuses = $uniqueRooms->pluck('status')->unique()->toArray();

        if ($user->role === 'creator') {
            $data = [
                'totalUsers' => User::count(),
                'totalRooms' => Room::count(),
                'totalBookings' => Booking::count(),
                'totalRevenue' => Payment::sum('amount'),
                'featureFlags' => FeatureFlag::all(),
                'uniqueRooms' => $uniqueRooms,
                'usedStatuses' => $usedStatuses,
            ];
        } elseif ($user->role === 'manager') {
            $totalRooms = Room::count();
            $occupiedRooms = Room::where('status', 'occupied')->count();
            $today = now()->startOfDay();
            $recentActivity = DB::table('activity_log')
                ->orderBy('created_at', 'desc')
                ->limit(15)
                ->get()
                ->map(function ($a) {
                    $causer = $a->causer_id
                        ? DB::table('users')->where('id', $a->causer_id)->value('name')
                        : 'System';
                    $a->causer_name = $causer;
                    return $a;
                });
            $todayPayments = Payment::with('booking')
                ->whereDate('created_at', $today)
                ->orderBy('created_at', 'desc')
                ->get();
            $paymentBreakdown = Payment::whereDate('created_at', $today)
                ->selectRaw("method, COUNT(*) as count, SUM(amount) as total")
                ->groupBy('method')
                ->pluck('total', 'method')
                ->toArray();
            $paymentMethods = [
                'cash' => ['label' => 'Cash', 'total' => $paymentBreakdown['cash'] ?? 0, 'count' => Payment::whereDate('created_at', $today)->where('method', 'cash')->count()],
                'mobile_money' => ['label' => 'Mobile Money', 'total' => $paymentBreakdown['mobile_money'] ?? 0, 'count' => Payment::whereDate('created_at', $today)->where('method', 'mobile_money')->count()],
                'card' => ['label' => 'Card', 'total' => $paymentBreakdown['card'] ?? 0, 'count' => Payment::whereDate('created_at', $today)->where('method', 'card')->count()],
                'bank_transfer' => ['label' => 'Bank Transfer', 'total' => $paymentBreakdown['bank_transfer'] ?? 0, 'count' => Payment::whereDate('created_at', $today)->where('method', 'bank_transfer')->count()],
            ];
            $overdueCheckOutsCount = Booking::where('status', 'checked_in')
                ->whereDate('check_out', '<', $today)->count();
            $overdueCheckOuts = Booking::with(['guests', 'rooms'])
                ->where('status', 'checked_in')
                ->whereDate('check_out', '<', $today)
                ->orderBy('check_out')
                ->get()
                ->map(function ($b) {
                    $b->days_overdue = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($b->check_out), false);
                    $b->days_overdue = abs($b->days_overdue);
                    return $b;
                });
            $rooms = Room::with('roomType')->orderBy('room_number')->get();

            $data = [
                'todayRevenue' => Payment::whereDate('created_at', today())->sum('amount'),
                'occupancyRate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0,
                'totalBookings' => Booking::count(),
                'availableRooms' => Room::where('status', 'available')->count(),
                'recentBookings' => Booking::with('guests')->latest()->take(5)->get(),
                'recentActivity' => $recentActivity,
                'todayPayments' => $todayPayments,
                'paymentMethods' => $paymentMethods,
                'overdueCheckOutsCount' => $overdueCheckOutsCount,
                'overdueCheckOuts' => $overdueCheckOuts,
                'rooms' => $rooms,
                'uniqueRooms' => $uniqueRooms,
                'usedStatuses' => $usedStatuses,
            ];
        } else {
            $data = $this->receptionistData($uniqueRooms, $usedStatuses);
        }

        return view('dashboard.index', $data);
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $guests = DB::table('guests')
            ->where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->orWhere('id_number', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn($g) => ['type' => 'guest', 'id' => $g->id, 'label' => $g->name, 'sub' => $g->phone ?? $g->id_number ?? '']);

        $bookings = DB::table('bookings')
            ->where('booking_number', 'like', "%{$q}%")
            ->orWhere('status', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn($b) => ['type' => 'booking', 'id' => $b->id, 'label' => $b->booking_number, 'sub' => $b->status]);

        $rooms = DB::table('rooms')
            ->where('room_number', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn($r) => ['type' => 'room', 'id' => $r->id, 'label' => "Room {$r->room_number}", 'sub' => $r->status]);

        return response()->json(array_merge(
            $guests->toArray(),
            $bookings->toArray(),
            $rooms->toArray()
        ));
    }

    private function receptionistData($uniqueRooms = null, $usedStatuses = null)
    {
        $uniqueRooms ??= Room::orderBy('room_number')->get();
        $usedStatuses ??= $uniqueRooms->pluck('status')->unique()->toArray();
        $today = now()->startOfDay();

        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $reservedRooms = Room::where('status', 'reserved')->count();

        $todayCheckIns = Booking::where('status', '!=', 'cancelled')
            ->whereDate('check_in', $today)->count();

        $todayCheckOuts = Booking::where('status', '!=', 'cancelled')
            ->whereDate('check_out', $today)->count();

        $todayRevenue = Payment::whereDate('created_at', $today)->sum('amount');

        $activeGuests = Guest::whereHas('bookings', function ($q) {
            $q->where('status', 'checked_in');
        })->count();

        $totalRooms = Room::count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $todayBookings = Booking::with(['guests', 'rooms'])
            ->where(function ($q) use ($today) {
                $q->whereDate('check_in', $today)
                  ->orWhereDate('check_out', $today);
            })
            ->where('status', '!=', 'cancelled')
            ->orderBy('check_in')
            ->get();

        $rooms = Room::with('roomType')->orderBy('room_number')->get();

        $pendingCheckIns = Booking::whereDate('check_in', $today)
            ->where('status', 'confirmed')->count();

        $overdueCheckOuts = Booking::where('status', 'checked_in')
            ->whereDate('check_out', '<', $today)->count();

        $overdueCheckOutsList = Booking::with(['guests', 'rooms'])
            ->where('status', 'checked_in')
            ->whereDate('check_out', '<', $today)
            ->orderBy('check_out')
            ->get()
            ->map(function ($b) {
                $b->days_overdue = abs(now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($b->check_out), false));
                return $b;
            });

        $cleaningRooms = Room::where('status', 'cleaning')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        $unpaidInvoices = Booking::whereIn('status', ['confirmed', 'checked_in'])
            ->whereColumn('total_amount', '>', 'paid_amount')
            ->count();

        $tomorrowCheckIns = Booking::where('status', 'confirmed')
            ->whereDate('check_in', now()->addDay()->startOfDay())
            ->count();

        // --- NEW: Occupied rooms with guest details and days remaining ---
        $occupiedRoomsList = Room::with(['bookings' => function ($q) {
                $q->where('status', 'checked_in')->with('guests');
            }])
            ->where('status', 'occupied')
            ->orderBy('room_number')
            ->get()
            ->map(function ($room) {
                $booking = $room->bookings->first();
                return (object) [
                    'room_number' => $room->room_number,
                    'room_id' => $room->id,
                    'guest_name' => $booking?->guests->first()?->name ?? 'N/A',
                    'booking_id' => $booking?->id,
                    'booking_number' => $booking?->booking_number,
                    'days_remaining' => $booking ? now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($booking->check_out), false) : 0,
                    'check_out' => $booking?->check_out,
                ];
            });

        // --- NEW: Reserved rooms (future confirmed bookings) ---
        $reservedBookings = Booking::with(['guests', 'rooms'])
            ->where('status', 'confirmed')
            ->whereDate('check_in', '>=', $today)
            ->orderBy('check_in')
            ->take(10)
            ->get();

        return compact(
            'availableRooms', 'occupiedRooms', 'reservedRooms',
            'todayCheckIns', 'todayCheckOuts', 'todayRevenue',
            'activeGuests', 'occupancyRate',
            'todayBookings', 'rooms',
            'pendingCheckIns', 'overdueCheckOuts',
            'cleaningRooms', 'maintenanceRooms',
            'occupiedRoomsList', 'reservedBookings',
            'overdueCheckOutsList',
            'unpaidInvoices', 'tomorrowCheckIns',
            'uniqueRooms', 'usedStatuses'
        );
    }
}
