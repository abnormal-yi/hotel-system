<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analytics Report - {{ $periodLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 20px; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .period { font-size: 11px; color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f5f5f5; text-align: left; padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; }
        td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        .summary { margin: 12px 0; }
        .summary td { font-size: 11px; font-weight: bold; }
        .grid-2 { display: flex; gap: 20px; }
        .grid-2 > div { flex: 1; }
        .stat-box { background: #f9f9f9; padding: 8px 12px; margin-bottom: 6px; }
        .stat-label { font-size: 9px; color: #888; }
        .stat-value { font-size: 14px; font-weight: bold; }
        ul { margin: 0; padding-left: 16px; }
        li { margin-bottom: 3px; }
    </style>
</head>
<body>
    <h1>Analytics Report</h1>
    <div class="period">Period: {{ $periodLabel }}</div>

    <div class="summary">
        <table>
            <tr>
                <td>Total Revenue</td>
                <td>TZS {{ number_format($totalRevenue) }}</td>
                <td>Total Bookings</td>
                <td>{{ $totalBookings }}</td>
            </tr>
        </table>
    </div>

    <h2>Revenue by Date</h2>
    @if($revenue->count())
    <table>
        <tr><th>Date</th><th>Revenue</th></tr>
        @foreach($revenue as $date => $total)
        <tr><td>{{ $date }}</td><td>TZS {{ number_format($total) }}</td></tr>
        @endforeach
    </table>
    @else
    <p>No revenue data for this period.</p>
    @endif

    <h2>Booking Trends</h2>
    @if($bookingTrends->count())
    <table>
        <tr><th>Date</th><th>Bookings</th></tr>
        @foreach($bookingTrends as $date => $count)
        <tr><td>{{ $date }}</td><td>{{ $count }}</td></tr>
        @endforeach
    </table>
    @else
    <p>No booking data for this period.</p>
    @endif

    <h2>Booking Status Breakdown</h2>
    <table>
        <tr><th>Status</th><th>Count</th></tr>
        @foreach($statusCounts as $status => $count)
        <tr><td>{{ ucfirst($status) }}</td><td>{{ $count }}</td></tr>
        @endforeach
    </table>

    <h2>Occupancy by Room Type</h2>
    <table>
        <tr><th>Room Type</th><th>Total Rooms</th><th>Occupied</th><th>Available</th></tr>
        @foreach($occupancyByType as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td>{{ $row->total }}</td>
            <td>{{ $row->occupied }}</td>
            <td>{{ $row->total - $row->occupied }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Daily Revenue Details</h2>
    @if($dailyRevenue->count())
    <table>
        <tr><th>Date</th><th>Bookings</th><th>Revenue</th></tr>
        @foreach($dailyRevenue as $dr)
        <tr><td>{{ $dr->date }}</td><td>{{ $dr->bookings }}</td><td>TZS {{ number_format($dr->revenue) }}</td></tr>
        @endforeach
    </table>
    @else
    <p>No daily revenue data for this period.</p>
    @endif

    <div style="text-align: center; margin-top: 30px; font-size: 9px; color: #aaa;">
        Generated on {{ now()->format('d/m/Y H:i') }} | Inshotel Analytics
    </div>
</body>
</html>
