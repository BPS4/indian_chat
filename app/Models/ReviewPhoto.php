<?php

// app/Models/ReviewPhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'photo_url',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
