<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

Broadcast::routes([
    'middleware' => ['jwt.auth'], // ✅ Your JWT middleware
]);

// Private chat channel
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    Log::info('Chat channel auth attempt', [
        'user_payload' => $user,
        'chat_id' => $chatId,
    ]);

    // Make sure $user is the actual authenticated user
    if (!$user || !isset($user->id)) {
        Log::error('Chat channel auth failed: no user found', [
            'user_payload' => $user,
            'chat_id' => $chatId,
        ]);
        return false;
    }

    // Check if the user is part of this conversation
    $authorized = Conversation::where('id', $chatId)
        ->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })
        ->exists();

    Log::info('Chat channel auth result', [
        'user_id' => $user->id,
        'chat_id' => $chatId,
        'authorized' => $authorized,
    ]);

    return $authorized;
});

// Optional: User-specific notifications channel
Broadcast::channel('user.{userId}', function ($user, $userId) {
    Log::info('User channel auth attempt', [
        'authenticated_user_id' => $user->id ?? 'null',
        'requested_user_id' => $userId,
    ]);

    $authorized = (int) $user->id === (int) $userId;

    if (!$authorized) {
        Log::error('User channel auth failed', [
            'authenticated_user_id' => $user->id ?? 'null',
            'requested_user_id' => $userId,
        ]);
    } else {
        Log::info('User channel auth success', [
            'authenticated_user_id' => $user->id,
            'requested_user_id' => $userId,
        ]);
    }

    return $authorized;
});
