<?php

// app/Models/Offer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $primaryKey = 'offer_id';

    protected $fillable = [
        'title',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'status',
        'hotel_id',
        'min_booking_amount',
        'max_discount_amount',
    ];
    protected $casts = [
        'discount_value' => 'integer',
    ];

    // Optional: relation to Hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
