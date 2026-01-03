<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingCancellation extends Model
{
    use HasFactory;

    protected $primaryKey = 'cancel_id';

    protected $fillable = [
        'booking_id',
        'refund_amount',
        'deduction percentage',
        'cancel_reason',
        'cancelled_at',
        'cancelled_by',
        'status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
