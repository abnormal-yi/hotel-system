<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('nida_number', 'like', "%{$search}%");
            });
        }

        $guests = $query->withCount('bookings')->latest()->paginate(20);

        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        return view('guests.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50|regex:/^\+255[0-9]{9}$/',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'nida_number' => 'nullable|string|size:20|unique:guests,nida_number',
            'address' => 'nullable|string|max:500',
            'nationality' => 'nullable|string|max:100',
            'guest_type' => 'nullable|string|max:50',
            'status' => 'nullable|in:new,active,vip,blacklisted',
            'notes' => 'nullable|string|max:500',
        ]);

        $existing = null;
        if (!empty($validated['nida_number'])) {
            $existing = Guest::where('nida_number', $validated['nida_number'])->first();
        } elseif (!empty($validated['phone'])) {
            $existing = Guest::where('phone', $validated['phone'])->first();
        }

        if ($existing) {
            $existing->update($validated);
            activity()->causedBy(auth()->user())->log('Updated guest (duplicate merged): ' . $existing->name);
            return redirect()->route('guests.show', $existing)->with('success', 'Existing guest updated.');
        }

        $validated['status'] ??= 'new';
        $guest = Guest::create($validated);

        activity()->causedBy(auth()->user())->log('Created guest: ' . $guest->name);

        return redirect()->route('guests.show', $guest)->with('success', 'Guest created successfully.');
    }

    public function show(Guest $guest)
    {
        $guest->load('bookings.rooms', 'bookings.payments');
        $user = auth()->user();

        $totalPaid = $guest->bookings->sum('paid_amount');
        $totalDue = $guest->bookings->sum('total_amount') - $totalPaid;
        $lastBooking = $guest->bookings->sortByDesc('check_in')->first();

        $hasNidaAccess = $user->role === 'creator' || $user->role === 'manager';

        return view('guests.show', compact('guest', 'totalPaid', 'totalDue', 'lastBooking', 'hasNidaAccess'));
    }

    public function edit(Guest $guest)
    {
        return view('guests.form', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50|regex:/^\+255[0-9]{9}$/',
            'email' => 'nullable|email|max:255',
            'id_number' => 'nullable|string|max:50',
            'nida_number' => 'nullable|string|size:20|unique:guests,nida_number,' . $guest->id,
            'address' => 'nullable|string|max:500',
            'nationality' => 'nullable|string|max:100',
            'guest_type' => 'nullable|string|max:50',
            'status' => 'nullable|in:new,active,vip,blacklisted',
            'notes' => 'nullable|string|max:500',
        ]);

        $guest->update($validated);

        activity()->causedBy(auth()->user())->log('Updated guest: ' . $guest->name);

        return redirect()->route('guests.show', $guest)->with('success', 'Guest updated successfully.');
    }

    public function blacklist(Guest $guest)
    {
        $guest->update([
            'blacklisted' => true,
            'blacklisted_at' => now(),
            'blacklist_reason' => 'Overdue check-out - auto flagged',
            'status' => 'blacklisted',
        ]);

        activity()->causedBy(auth()->user())->log('Blacklisted guest: ' . $guest->name);

        return redirect()->back()->with('success', 'Guest blacklisted successfully.');
    }

    public function destroy(Guest $guest)
    {
        DB::table('booking_guest')->where('guest_id', $guest->id)->delete();
        $guest->delete();

        activity()->causedBy(auth()->user())->log('Deleted guest: ' . $guest->name);

        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully.');
    }
}
