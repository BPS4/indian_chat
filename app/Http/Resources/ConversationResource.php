<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authUser = auth()->user();

        $isGroup = $this->type === 'group';

        $otherUser = null;
        $groupMembers = null;

        if (!$isGroup) {
            // One-to-One chat
            $otherUser = $this->users
                ->where('id', '!=', $authUser->id)
                ->first();
        } else {
            // Group chat
            $groupMembers = $this->users->map(fn ($user) => [
                'id'    => (string) $user->id,
                'name'  => $user->name,
                'image' => $user->profile_pic ?? null,
            ]);
        }

        return [
            'id' => (string) $this->id,
            'name' => $isGroup ? $this->name : null,
            'image' => $this->image ?? null,

            'isGroup' => $isGroup,

            'participants' => $this->users->pluck('id')->map(fn ($id) => (string) $id),

            'lastMessage' => $this->latestMessage ? [
                'id' => (string) $this->latestMessage->id,
                'chatId' => (string) $this->id,
                'senderId' => (string) $this->latestMessage->sender_id,
                'content' => $this->latestMessage->message,
                'type' => 'text',
                'createdAt' => $this->latestMessage->created_at,
            ] : null,

            'unreadCount' => $this->unread_count,

            'lastMessageTime' => optional($this->latestMessage)->created_at,

            'createdAt' => $this->created_at,

            // 👇 CONDITIONAL FIELDS
            'otherUser' => $otherUser ? [
                'id' => (string) $otherUser->id,
                'name' => $otherUser->name, 
                'email' => $otherUser->email,
                'image' => $otherUser->profile_pic,
                'isOnline' => (bool) $otherUser->is_online ?? false,
            ] : null,

            'groupMembers' => $groupMembers,
        ];
    }
}