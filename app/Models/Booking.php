<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $primaryKey = 'id'; // Optional if using booking_id as PK

    // ✅ Add all the fields you want to mass assign
    protected $fillable = [
        'id',
        'booking_id',
        'user_id',
        'hotel_id',
        'checkin_date',
        'checkout_date',
        'total_nights',
        'total_guests',
        'status',
        'base_room_charges',
        'taxes',
        'hotel_discount',
        'coupon_discount',
        'total_payable',
        'created_by',
        'city'
    ];

    // Relationships
    public function payment()
    {
        return $this->hasOne(BookingPayment::class, 'booking_id', 'booking_id');
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}

    public function rooms()
    {
        return $this->hasMany(BookingRoom::class, 'booking_id', 'id');
    }

    public function guests()
    {
        return $this->hasMany(BookingGuest::class, 'booking_id', 'id');
    }

    public function gst()
    {
        return $this->hasOne(GstDetail::class, 'booking_id', 'id');
    }

    public function addons()
    {
        return $this->hasMany(BookingAddon::class, 'booking_id', 'id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'booking_id', 'id');
    }

    protected $appends = ['cancellation_policy'];

    public function getCancellationPolicyAttribute()
    {
        return $this->hotel?->hotelPolicies?->cancellation_policy ? $this->hotel?->hotelPolicies?->cancellation_policy : null;
    }
}
