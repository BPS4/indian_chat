<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPolicy extends Model
{
    protected $table = 'hotel_policies'; // your table name
    protected $fillable = [
        'hotel_id',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'extra_bed_policy',
        'child_policy',
    ];
}
