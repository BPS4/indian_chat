<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = ['hotel_id', 'room_name', 'description', 'room_size', 'max_guests', 'max_child', 'bed_type', 'photo_url'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function inventories()
    {
        return $this->hasMany(RoomInventory::class);
    }

    public function roomInventory()
    {
        return $this->hasMany(RoomInventory::class, 'room_type_id', 'id');
    }

    public function roomPrices()
    {
        return $this->hasMany(RoomPrice::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(
            FacilityMaster::class,
            'room_facilities',
            'room_type_id',
            'facility_id'
        );
    }

    public function addons()
    {
        return $this->belongsToMany(
            Addon::class,
            'room_type_addon_prices',
            'room_type_id',
            'addon_id'
        )->withPivot(['price', 'per_person']);
    }

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }
}
