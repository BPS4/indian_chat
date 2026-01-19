<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Laravel\Reverb\Pulse\Livewire\Messages;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageDelivered;
use App\Events\MessageSeen;
use App\Events\TypingStatus;
use App\Http\Resources\ConversationResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function send_message(Request $request)
    {



        // $data = $request->validate([
        //     'sender_name' => 'required',
        //     'message' => 'required',
        // ]);

        $message = $request->input('message');
        $chatId = $request->input('chat_id');
        $senderId = auth()->id();

        // dd($request->all());
        MessageSent::dispatch($request->input('message'), $request->input('sender_name'), $chatId);
        return ['success' => true];
    }


    /**
     * Get all broadcast messages for user's city with read/unread status
     */
    public function all_chats(Request $request)
    {
        $user = auth()->user();
        $user_city = $user->city;
        $user_id = $user->id;

        // Get messages for user's city with read status
        $chats = DB::table('messages')
            ->leftJoin('message_reads', function($join) use ($user_id) {
                $join->on('messages.id', '=', 'message_reads.message_id')
                     ->where('message_reads.user_id', '=', $user_id);
            })
            ->where(function($query) use ($user_city) {
                $query->where('messages.city', $user_city)
                      ->orWhere('messages.city', 'all');
            })
            ->select(
                'messages.*',
                DB::raw('CASE WHEN message_reads.id IS NOT NULL THEN 1 ELSE 0 END as is_read'),
                'message_reads.read_at'
            )
            ->orderBy('messages.created_at', 'desc')
            ->get();

        // Count unread messages
        $unread_count = $chats->where('is_read', 0)->count();

        return response()->json([
            'status' => true,
            'unread_count' => $unread_count,
            'messages' => $chats
        ]);
    }

    /**
     * Get only unread messages
     */
    public function getUnreadMessages(Request $request)
    {
        $user = auth()->user();
        $user_city = $user->city;
        $user_id = $user->id;

        $unread = DB::table('messages')
            ->leftJoin('message_reads', function($join) use ($user_id) {
                $join->on('messages.id', '=', 'message_reads.message_id')
                     ->where('message_reads.user_id', '=', $user_id);
            })
            ->where(function($query) use ($user_city) {
                $query->where('messages.city', $user_city)
                      ->orWhere('messages.city', 'all');
            })
            ->whereNull('message_reads.id')
            ->select('messages.*')
            ->orderBy('messages.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'count' => $unread->count(),
            'messages' => $unread
        ]);
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount(Request $request)
    {
        $user = auth()->user();
        $user_city = $user->city;
        $user_id = $user->id;

        $count = DB::table('messages')
            ->leftJoin('message_reads', function($join) use ($user_id) {
                $join->on('messages.id', '=', 'message_reads.message_id')
                     ->where('message_reads.user_id', '=', $user_id);
            })
            ->where(function($query) use ($user_city) {
                $query->where('messages.city', $user_city)
                      ->orWhere('messages.city', 'all');
            })
            ->whereNull('message_reads.id')
            ->count();

        return response()->json([
            'status' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Mark a single message as read
     */
    public function markAsRead(Request $request, $messageId)
    {
        $user_id = auth()->id();

        // Check if message exists
        $message = Message::find($messageId);
        
        if (!$message) {
            return response()->json([
                'status' => false,
                'message' => 'Message not found'
            ], 404);
        }

        // Check if already marked as read
        $existingRead = DB::table('message_reads')
            ->where('message_id', $messageId)
            ->where('user_id', $user_id)
            ->first();

        if ($existingRead) {
            return response()->json([
                'status' => true,
                'message' => 'Message already marked as read'
            ]);
        }

        // Mark as read
        DB::table('message_reads')->insert([
            'message_id' => $messageId,
            'user_id' => $user_id,
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message marked as read'
        ]);
    }

    /**
     * Mark all messages as read for current user
     */
    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $user_city = $user->city;

        // Get all unread message IDs for user's city
        $unreadMessageIds = DB::table('messages')
            ->leftJoin('message_reads', function($join) use ($user_id) {
                $join->on('messages.id', '=', 'message_reads.message_id')
                     ->where('message_reads.user_id', '=', $user_id);
            })
            ->where(function($query) use ($user_city) {
                $query->where('messages.city', $user_city)
                      ->orWhere('messages.city', 'all');
            })
            ->whereNull('message_reads.id')
            ->pluck('messages.id');

        if ($unreadMessageIds->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No unread messages',
                'marked_count' => 0
            ]);
        }

        // Bulk insert read records
        $insertData = [];
        foreach ($unreadMessageIds as $messageId) {
            $insertData[] = [
                'message_id' => $messageId,
                'user_id' => $user_id,
                'read_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('message_reads')->insert($insertData);

        return response()->json([
            'status' => true,
            'message' => 'All messages marked as read',
            'marked_count' => count($insertData)
        ]);
    }

    /**
     * Mark specific messages as read (bulk)
     */
    public function markMultipleAsRead(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id'
        ]);

        $user_id = auth()->id();
        $messageIds = $request->message_ids;

        // Get messages that aren't already read
        $alreadyRead = DB::table('message_reads')
            ->where('user_id', $user_id)
            ->whereIn('message_id', $messageIds)
            ->pluck('message_id')
            ->toArray();

        $toMarkAsRead = array_diff($messageIds, $alreadyRead);

        if (empty($toMarkAsRead)) {
            return response()->json([
                'status' => true,
                'message' => 'All specified messages already marked as read',
                'marked_count' => 0
            ]);
        }

        // Bulk insert
        $insertData = [];
        foreach ($toMarkAsRead as $messageId) {
            $insertData[] = [
                'message_id' => $messageId,
                'user_id' => $user_id,
                'read_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('message_reads')->insert($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Messages marked as read',
            'marked_count' => count($insertData)
        ]);
    }


    public function startPrivateChat(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id'
        ]);

        $sender = auth()->user();
        $receiverId = $request->receiver_id;

        if ($sender->id == $receiverId) {
            return response()->json(['error' => 'Invalid user'], 422);
        }

        // Check existing private chat
        $conversation = Conversation::where('type', 'private')
            ->whereHas('users', fn($q) => $q->where('user_id', $sender->id))
            ->whereHas('users', fn($q) => $q->where('user_id', $receiverId))
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'private',
                'created_by' => $sender->id
            ]);

            $conversation->users()->attach([
                $sender->id   => ['is_admin' => true],
                $receiverId  => ['is_admin' => false]
            ]);
        }

        return response()->json($conversation);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'members' => 'required|array|min:2',
            'members.*' => 'exists:users,id'
        ]);

        $creator = auth()->user();

        $conversation = Conversation::create([
            'type' => 'group',
            'name' => $request->name,
            'created_by' => $creator->id
        ]);

        $users = collect($request->members)->push($creator->id)->unique();

        foreach ($users as $userId) {
            $conversation->users()->attach($userId, [
                'is_admin' => $userId == $creator->id
            ]);
        }

        return response()->json($conversation);
    }


    public function sendMessage(Request $request)
    {
        // $request->validate([
        //     'conversation_id' => 'required|exists:conversations,id',
        //     'message' => 'required|string|max:5000'
        // ]);

        $user = auth()->user();

        $message = $request->input('message');
        $chatId = $request->input('chat_id');


        // Verify sender is part of conversation
        $conversation = Conversation::where('id', $request->chat_id)
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        // dd($conversation);


        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Broadcast event (group or private)
        broadcast(new MessageSent($message, $user->id, $conversation->id))->toOthers();

        return response()->json([
            'status' => true,
            'data' => $message
        ]);
    }


    public function myConversations(Request $request)
    {
        $user = auth()->user();
        // dd($user);
        $conversations = Conversation::whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);   // ✅ Correct
        })
            ->with([
                'users:id,name,email,profile_pic',
                'latestMessage.sender:id,name'
            ])
            ->withCount([
                'messages as unread_count' => function ($q) use ($user) {
                    $q->where('is_read', 0)
                        ->where('sender_id', '!=', $user->id);
                }
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->take(1)
            )
            ->get();


        // dd($conversations);

        return response()->json([
            'status' => true,
            'data' => ConversationResource::collection($conversations)
        ]);
    }


    public function conversationMessages(Request $request, $conversationId)
    {
        $user = auth()->user();

        // ✅ Conversation must exist
        $conversation = Conversation::with('users:id,name')
            ->findOrFail($conversationId);

        // ✅ User must belong to conversation
        if (!$conversation->users->contains($user->id)) {
            return response()->json([
                'status' => false,
                'message' => 'You are not part of this conversation'
            ], 403);
        }

        // ✅ Load messages
        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'conversation' => [
                'id' => $conversation->id,
                'type' => $conversation->type,
                'name' => $conversation->name,
                'users' => $conversation->users
            ],
            'messages' => $messages
        ]);
    }




    // public function send_Message(Request $request)
    // {
    //     $message = $request->input('message');
    //     $chatId = $request->input('chat_id');
    //     $senderId = auth()->id();

    //     // Save message to DB (optional)

    //     broadcast(new MessageSent($message, $senderId, $chatId))->toOthers();

    //     return response()->json(['status' => 'Message sent']);
    // }

    public function typing(Request $request)
    {
        $chatId = $request->input('chat_id');
        $userId = auth()->id();
        $isTyping = $request->input('is_typing');

        broadcast(new TypingStatus($chatId, $userId, $isTyping))->toOthers();

        return response()->json(['status' => 'Typing status sent']);
    }

    /**
     * Get admin conversation - default for all users
     */
    public function getAdminConversation(Request $request)
    {
        try {
            // Get all messages from messages table (admin broadcasts)
            $messages = Message::whereNotNull('description')
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'conversation' => [
                    'type' => 'admin_broadcast',
                    'name' => 'Admin Announcements',
                    'is_admin_conversation' => true
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching admin messages: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
