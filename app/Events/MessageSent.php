<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender_id;
    public $chat_id;

    public function __construct($message, $sender_id, $chat_id)
    {
        $this->message   = $message;
        $this->sender_id = $sender_id;
        $this->chat_id   = $chat_id;

        Log::info('[MessageSent] Constructed', compact(
            'message', 'sender_id', 'chat_id'
        ));
    }

    /**
     * Private channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->chat_id}");
    }

    /**
     * Event name
     */
    public function broadcastAs()
    {
        return 'client-send-message';
    }

    /**
     * Payload
     */
    public function broadcastWith()
    {
        return [
            'message'   => $this->message,
            'sender_id' => $this->sender_id,
            'chat_id'   => $this->chat_id,
        ];
    }
}
