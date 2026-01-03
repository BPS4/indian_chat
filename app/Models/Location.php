<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['city', 'state', 'country', 'zipcode', 'image'];


    public function locality()
    {
        return $this->belongsTo(Localty::class, 'id', 'location_id');
    }
}
