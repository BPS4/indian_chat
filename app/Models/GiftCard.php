<?php

// app/Models/GiftCard.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $primaryKey = 'giftcard_id';

    protected $fillable = [
        'code',
        'balance_amount',
        'expiry_date',
        'is_active',
    ];
}
