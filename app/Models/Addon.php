<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $fillable = ['name', 'description'];

    public function roomTypes()
    {
        return $this->belongsToMany(
            RoomType::class,
            'room_type_addon_prices',
            'addon_id',
            'room_type_id'
        )->withPivot(['price', 'per_person']);
    }
}


