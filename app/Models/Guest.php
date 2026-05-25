<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'id_number',
        'nida_number',
        'address',
        'nationality',
        'guest_type',
        'status',
        'notes',
        'total_bookings',
        'total_spent',
        'last_visit_at',
    ];

    protected $casts = [
        'nida_number' => 'encrypted',
        'blacklisted' => 'boolean',
        'blacklisted_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'total_spent' => 'integer',
        'total_bookings' => 'integer',
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_guest')
            ->withPivot('is_primary')
            ->withTimestamps();
    }
}
