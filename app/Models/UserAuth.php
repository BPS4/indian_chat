<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAuth extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'hash',
        'expire_at',
        'is_verified',
    ];
}
