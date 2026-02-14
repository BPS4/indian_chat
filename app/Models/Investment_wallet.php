<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment_wallet extends Model
{
    protected $table = 'investment_wallets';
    protected $fillable = [
        'user_id',
        'investment_balance',
        'roi_balance'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
