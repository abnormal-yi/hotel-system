<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30d');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $dateFrom = match ($period) {
            '7d' => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '90d' => now()->subDays(90)->startOfDay(),
            '1y' => now()->subYear()->startOfDay(),
            'custom' => $dateFrom ? now()->parse($dateFrom)->startOfDay() : now()->subDays(30)->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };
        $dateTo = match ($period) {
            'custom' => $dateTo ? now()->parse($dateTo)->endOfDay() : now(),
            default => now(),
        };

        $revenue = DB::table('payments')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $occupancyByType = DB::table('rooms')
            ->selectRaw('room_types.name, COUNT(rooms.id) as total, SUM(CASE WHEN rooms.status = "occupied" THEN 1 ELSE 0 END) as occupied')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->groupBy('room_types.name')
            ->get();

        $bookingTrends = DB::table('bookings')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $statusCounts = DB::table('bookings')
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $dailyRevenue = DB::table('bookings')
            ->selectRaw('DATE(check_in) as date, COUNT(*) as bookings, COALESCE(SUM(total_amount), 0) as revenue')
            ->whereBetween('check_in', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('charts.index', compact(
            'revenue', 'occupancyByType', 'bookingTrends', 'statusCounts', 'dailyRevenue', 'period', 'dateFrom', 'dateTo'
        ));
    }

    public function pdf(Request $request)
    {
        $period = $request->get('period', '30d');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $dateFrom = match ($period) {
            '7d' => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '90d' => now()->subDays(90)->startOfDay(),
            '1y' => now()->subYear()->startOfDay(),
            'custom' => $dateFrom ? now()->parse($dateFrom)->startOfDay() : now()->subDays(30)->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };
        $dateTo = match ($period) {
            'custom' => $dateTo ? now()->parse($dateTo)->endOfDay() : now(),
            default => now(),
        };

        $revenue = DB::table('payments')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $occupancyByType = DB::table('rooms')
            ->selectRaw('room_types.name, COUNT(rooms.id) as total, SUM(CASE WHEN rooms.status = "occupied" THEN 1 ELSE 0 END) as occupied')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->groupBy('room_types.name')
            ->get();

        $bookingTrends = DB::table('bookings')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $statusCounts = DB::table('bookings')
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $dailyRevenue = DB::table('bookings')
            ->selectRaw('DATE(check_in) as date, COUNT(*) as bookings, COALESCE(SUM(total_amount), 0) as revenue')
            ->whereBetween('check_in', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $revenue->sum();
        $totalBookings = $bookingTrends->sum();
        $periodLabel = match ($period) {
            '7d' => 'Last 7 Days',
            '30d' => 'Last 30 Days',
            '90d' => 'Last 90 Days',
            '1y' => 'Last Year',
            'custom' => $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y'),
            default => 'Last 30 Days',
        };

        $pdf = Pdf::loadView('charts.pdf', compact(
            'revenue', 'occupancyByType', 'bookingTrends', 'statusCounts', 'dailyRevenue',
            'period', 'dateFrom', 'dateTo', 'totalRevenue', 'totalBookings', 'periodLabel'
        ));

        return $pdf->download('analytics-report.pdf');
    }
}
