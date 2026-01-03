<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeAddonPrice extends Model
{
    protected $fillable = ['room_type_id', 'addon_id', 'price', 'per_person'];
}
