<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\Messagesent;
use Laravel\Reverb\Pulse\Livewire\Messages;



class ContactsController extends Controller
{
    public function get_contacts(Request $request)
    {
        $request->validate([
            'contacts'   => 'required|array',
            'contacts.*' => 'required|string'
        ]);


        $user = auth()->user();
        // dd($user);

        // Normalize numbers (VERY IMPORTANT)
        $contacts = array_map(function ($number) {
            return preg_replace('/\s+|-/', '', $number);
        }, $request->contacts);

        // Get users whose phone exists in contacts
        $matchedUsers = User::whereIn('mobile', $contacts)
            ->where('id', '!=', $user->id) // exclude self
            ->select('id', 'name', 'mobile', 'profile_pic')
            ->get();

        return response()->json([
            'status'  => true,
            'count'   => $matchedUsers->count(),
            'contacts' => $matchedUsers
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
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:5000'
        ]);

        $user = auth()->user();

        // Verify sender is part of conversation
        $conversation = Conversation::where('id', $request->conversation_id)
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message
        ]);

        // Broadcast event (group or private)
        event(new MessageSent($message));

        return response()->json([
            'status' => true,
            'data' => $message
        ]);
    }
}
