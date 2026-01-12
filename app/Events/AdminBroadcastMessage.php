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

    public function __construct($message, $adminId, $adminName, $messageId = null)
    {
        $this->message = $message;
        $this->adminId = $adminId;
        $this->adminName = $adminName;
        $this->messageId = $messageId;
    }

    /**
     * Broadcast on public channel so all authenticated users can listen
     */
    public function broadcastOn()
    {
        return new Channel('admin-broadcast');
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
            'message_id' => $this->messageId,
            'message' => $this->message,
            'admin_id' => $this->adminId,
            'admin_name' => $this->adminName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
