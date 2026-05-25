<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('booking', 'user');

        if ($request->filled('booking_id')) {
            $query->where('booking_id', $request->booking_id);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Booking $booking)
    {
        return view('payments.form', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'paid_at' => 'nullable|date',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => $validated['paid_at'] ?? now(),
        ]);

        $booking->increment('paid_amount', $validated['amount']);

        if ($booking->paid_amount >= $booking->total_amount) {
            $booking->invoices()->where('status', '!=', 'paid')->each(function ($invoice) {
                $invoice->update([
                    'paid' => $invoice->booking->paid_amount,
                    'due' => max(0, $invoice->total - $invoice->booking->paid_amount),
                    'status' => 'paid',
                ]);
            });
        } else {
            $booking->invoices()->where('status', '!=', 'paid')->each(function ($invoice) {
                $invoice->update([
                    'paid' => $invoice->booking->paid_amount,
                    'due' => max(0, $invoice->total - $invoice->booking->paid_amount),
                    'status' => 'partial',
                ]);
            });
        }

        activity()->causedBy(auth()->user())->log('Recorded payment of ' . $validated['amount'] . ' for booking: ' . $booking->booking_number);

        return redirect()->route('bookings.show', $booking)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('booking.guests', 'booking.rooms.roomType', 'user');

        return view('payments.show', compact('payment'));
    }

    public function invoice(Payment $payment)
    {
        $payment->load('booking.guests', 'booking.rooms.roomType', 'booking.invoices', 'user');

        return view('payments.invoice', compact('payment'));
    }
}
