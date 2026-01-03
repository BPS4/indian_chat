<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

    protected $fillable = [
        'user_id',
        'booking_id',
        'hotel_id',
        'rating',
        'review',
        'image',
        'is_approved',
        'reply'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
