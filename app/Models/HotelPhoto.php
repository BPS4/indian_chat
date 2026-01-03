<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPhoto extends Model
{
    protected $table = 'hotel_photos'; // your table name

    protected $fillable = [
        'hotel_id',
        'photo_url',
        'is_cover'
    ];
}
