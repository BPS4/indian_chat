<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    protected $fillable = [
        'booking_id',
        'room_type_id',
        'quantity',
        'price_per_room',
        'subtotal',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function roomFacilities()
    {
        return $this->hasMany(BookingRoomFacility::class, 'booking_room_type_id');
    }


}
