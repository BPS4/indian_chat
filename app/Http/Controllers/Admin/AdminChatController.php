<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\AdminBroadcastMessage;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminChatController extends Controller
{
    /**
     * Send broadcast message to all users
     */
    public function sendBroadcastMessage(Request $request)
    {

    // dd('hi');
        $validated = $request->validate([
            'description'      => 'required|string|max:5000',
            'media'            => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:10240',
            'youtube_link'     => 'nullable|url',
            'calling_number'   => 'required|string|max:15',
            'website_link'     => 'nullable|url',
            'instagram_link'   => 'nullable|url',
            'facebook_link'    => 'nullable|url',
            'telegram_link'    => 'nullable|url',
            'state'            => 'required|string',
            'city'             => 'nullable|string',
            'total_users'      => 'nullable|integer',
        ]);

        $admin = auth()->user();
        
        // If using session-based auth for admin
        if (!$admin) {
            $adminId = session()->get('id');
            $admin = User::find($adminId);
        }

        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        DB::beginTransaction();

        try {
            // Prepare message data
            $messageData = [
                'description'     => $request->description,
            ];

            // Handle additional fields from hotel controller
            $additionalData = $request->only([
                'youtube_link',
                'calling_number',
                'website_link',
                'instagram_link',
                'facebook_link',
                'telegram_link',
                'state',
                'city',
                'total_users',
            ]);

            // Set country default
            if ($request->filled('state') || $request->filled('city')) {
                $additionalData['country'] = 'India';
            }

            // Handle auto_send checkbox
            $additionalData['auto_send'] = $request->has('auto_send');

            // Handle media file upload
            if ($request->hasFile('media')) {
                $additionalData['media'] = $request->file('media')->store('messages', 'public');
            }

            // Merge all data
            $messageData = array_merge($messageData, array_filter($additionalData));

            // Save message to database
            $message = Message::create($messageData);

            DB::commit();

            // Broadcast to all users (non-blocking, continues even if broadcast fails)
            try {
                broadcast(new AdminBroadcastMessage(
                    $request->description,
                    $admin->id,
                    $admin->name ?? 'Admin',
                    $message->id
                ));
                $broadcastStatus = 'Message saved and broadcasted successfully!';
            } catch (\Exception $broadcastError) {
                Log::warning('Broadcast failed but message saved: ' . $broadcastError->getMessage());
                $broadcastStatus = 'Message saved successfully! (Broadcasting unavailable - please start Reverb server)';
            }

            return redirect()->route('message.list')
                ->with('success', $broadcastStatus);
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Broadcast message error: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to send message: ' . $e->getMessage()]);
        }
    }

    /**
     * Get admin conversation messages
     */
    public function getAdminMessages(Request $request)
    {
        // Get admin conversation
        $adminConversation = Conversation::where('type', 'admin_broadcast')
            ->orWhere('id', 1)
            ->first();

        if (!$adminConversation) {
            return response()->json([
                'success' => true,
                'messages' => [],
                'conversation' => null
            ]);
        }

        // Get messages
        $messages = Message::where('conversation_id', $adminConversation->id)
            ->with('sender:id,name,email,profile_pic')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'conversation' => $adminConversation,
            'messages' => $messages
        ]);
    }

    /**
     * Get all users for admin panel
     */
    public function getAllUsers(Request $request)
    {
        $users = User::where('role_id', User::CUSTOMER)
            ->select('id', 'name', 'email', 'mobile', 'profile_pic', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}
