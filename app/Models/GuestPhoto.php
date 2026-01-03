<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestPhoto extends Model
{
    protected $fillable = ['hotel_id', 'photo_url'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
