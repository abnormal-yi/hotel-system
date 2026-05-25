<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'floor',
        'status',
        'custom_price',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'custom_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_room')
            ->withPivot('price', 'check_in', 'check_out')
            ->withTimestamps();
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class);
    }
}
