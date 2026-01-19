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
        'total_users',
        'sender_id',
        'conversation_id',
        'message',
        'is_read',
        'created_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'auto_send' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get users who have read this message
     */
    public function readBy()
    {
        return $this->belongsToMany(User::class, 'message_reads', 'message_id', 'user_id')
            ->withTimestamps()
            ->withPivot('read_at');
    }

    /**
     * Check if message is read by specific user
     */
    public function isReadBy($userId)
    {
        return $this->readBy()->where('user_id', $userId)->exists();
    }

    /**
     * Get read count for this message
     */
    public function getReadCountAttribute()
    {
        return $this->readBy()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
