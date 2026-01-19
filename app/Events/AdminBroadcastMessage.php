<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $adminId;
    public $adminName;
    public $messageId;
    public $city;

    public function __construct($message, $adminId, $adminName, $city, $messageId = null)
    {
        $this->message = $message;
        $this->adminId = $adminId;
        $this->adminName = $adminName;
        $this->city = $city;
        $this->messageId = $messageId;
    }

    /**
     * Broadcast on city-specific channel or multiple channels for all cities
     */
    public function broadcastOn()
    {
        // If 'all' cities selected, broadcast to all unique cities
        if (strtolower($this->city) === 'all' || empty($this->city)) {
            $cities = \App\Models\User::select('city')
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->distinct()
                ->pluck('city')
                ->toArray();
            
            $channels = [];
            foreach ($cities as $city) {
                $channels[] = new Channel('admin-broadcast.' . $this->sanitizeCityName($city));
            }
            return $channels;
        }
        
        // Broadcast to specific city channel
        return new Channel('admin-broadcast.' . $this->sanitizeCityName($this->city));
    }
    
    /**
     * Sanitize city name for channel name (remove spaces, special chars)
     */
    private function sanitizeCityName($city)
    {
        return strtolower(str_replace([' ', '-', ',', '.'], '_', trim($city)));
    }

    /**
     * Event name
     */
    public function broadcastAs()
    {
        return 'admin-message';
    }

    /**
     * Data to broadcast
     */
    public function broadcastWith()
    {
        return [
            'message_id' => $this->message->id,
            'description' => $this->message->description,
            'media' => $this->message->media ?? null,
            'links' => [
                'youtube'   => $this->message->youtube_link,
                'website'   => $this->message->website_link,
                'instagram' => $this->message->instagram_link,
                'facebook'  => $this->message->facebook_link,
                'telegram'  => $this->message->telegram_link,
                'call'      => $this->message->calling_number,
            ],
            'admin_name' => $this->adminName,
            'target_city' => $this->city,
            'state' => $this->message->state ?? null,
            'country' => $this->message->country ?? 'India',
            'timestamp' => now()->toIso8601String(),
            'broadcast_type' => (strtolower($this->city) === 'all' || empty($this->city)) ? 'all_cities' : 'specific_city',
        ];
    }
}
