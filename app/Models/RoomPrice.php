<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPrice extends Model
{
    protected $fillable = ['room_type_id', 'start_date', 'end_date', 'base_price', 'extra_person_price', 'currency'];
    protected $casts = [
        'base_price' => 'integer',
    ];
}
