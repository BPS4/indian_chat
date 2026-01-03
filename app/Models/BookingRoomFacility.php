<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRoomFacility extends Model
{
    protected $fillable = [
        'booking_id',
        'booking_room_type_id',
        'icon',
        'title',
    ];
}
