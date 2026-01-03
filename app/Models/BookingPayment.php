<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_method',
        'amount',
        'currency',
        'payment_status',
        'transaction_id',
        'transaction_details',
        'razorpay_order_id',
        'payment_date'

    ];

    // Optional: Define relationship
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
