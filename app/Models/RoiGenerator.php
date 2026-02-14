<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoiGenerator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'investment_amount',
        'roi_percentage',
        'roi_amount',
        'roi_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
