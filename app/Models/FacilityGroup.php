<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityGroup extends Model
{
    protected $fillable = ['group_name', 'description'];

    public function facilities()
    {
        return $this->hasMany(FacilityMaster::class, 'group_id');
    }
}
