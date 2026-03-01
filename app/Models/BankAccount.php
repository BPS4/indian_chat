<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
     protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'account_holder_name',
        'document',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
