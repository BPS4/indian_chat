<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'type', // private | group
        'name',
        'created_by'
    ];

//    public function users()
//     {
//         return $this->belongsToMany(
//             User::class,
//             'conversation_users'   // ✅ IMPORTANT
//         )
//         ->withPivot('is_admin')
//         ->withTimestamps();
//     }


  public function users()
    {
        return $this->belongsToMany(
            User::class,
            'conversation_users',   // pivot table
            'conversation_id',
            'user_id'
        );
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}