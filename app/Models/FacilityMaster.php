<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityMaster extends Model
{
    protected $fillable = ['facility_name', 'icon', 'group_id', 'facility_for'];

    public function group()
    {
        return $this->belongsTo(FacilityGroup::class, 'group_id');
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_facilities', 'facility_id', 'hotel_id');
    }
}
