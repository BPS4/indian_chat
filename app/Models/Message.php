<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'media',
        'youtube_link',
        'description',
        'calling_number',
        'website_link',
        'instagram_link',
        'facebook_link',
        'telegram_link',
        'country',
        'state',
        'city',
        'auto_send',
        'total_users'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
