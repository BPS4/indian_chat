<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGuest extends Model
{
    protected $fillable = [
        'booking_id',
        'guest_name',
        'email',
        'mobile',
        'aadhar_no',
        'is_primary',
    ];

    // Optional: Define relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
