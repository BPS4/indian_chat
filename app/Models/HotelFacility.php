<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelFacility extends Model
{
    protected $table = 'hotel_facilities'; // your table name
    protected $fillable = [
        'hotel_id',
        'facility_id',
    ];
}
