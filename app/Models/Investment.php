<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_receipt',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
