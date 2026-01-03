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
        Messagesent::dispatch($request->input('message'), $request->input('sender_name'), $chatId);
        return ['success' => true];
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
        // event(new MessageSent($message, $user->id, $chatId));

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
}
