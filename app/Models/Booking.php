<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'hotel_id',
        'user_id',
        'check_in',
        'check_out',
        'status',
        'booking_type',
        'total_amount',
        'paid_amount',
        'source',
        'notes',
        'cancellation_reason',
        'checked_in_at',
        'checked_out_at',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'booking_room')
            ->withPivot('price', 'check_in', 'check_out')
            ->withTimestamps();
    }

    public function guests()
    {
        return $this->belongsToMany(Guest::class, 'booking_guest')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeFutureBookings($query)
    {
        return $query->where('booking_type', 'advance');
    }
}
