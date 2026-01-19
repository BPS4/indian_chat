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

    // ✅ Validate request
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
        
        // ✅ Get admin user
        $admin = auth()->user();

        if (!$admin) {
        $adminId = session()->get('id');
        $admin = User::find($adminId);
    }
        // dd($admin->id);
    if (!$admin) {
        return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // ✅ Normalize city
        $city = $request->city === 'all' ? 'all' : $request->city;
        
        DB::beginTransaction();
        
        try {
            // ✅ Base message data
            $messageData = [
                'description'   => $request->description,
                'youtube_link'  => $request->youtube_link,
                'calling_number'=> $request->calling_number,
                'website_link'  => $request->website_link,
                'instagram_link'=> $request->instagram_link,
                'facebook_link' => $request->facebook_link,
                'telegram_link' => $request->telegram_link,
                'state'         => $request->state,
                'city'          => $request->city,
                'total_users'   => $request->total_users,
                'country'       => 'India',
                'auto_send'     => $request->has('auto_send'),
                'created_by'    => $admin->id,
                ];
                
                // ✅ Handle media upload (public/messages)
                if ($request->hasFile('media') && $request->file('media')->isValid()) {
                    $file = $request->file('media');
                    $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                    $file->move(public_path('messages'), $filename);
                    $messageData['media'] = 'messages/' . $filename;
                    }
                    
                    // ✅ Save message
                    $message = Message::create($messageData);
             
        DB::commit();

        // ✅ Broadcast message
        try {
            broadcast(new AdminBroadcastMessage(
                $message,
                $admin->id,
                $admin->name ?? 'Admin',
                $city
            ));

            $broadcastStatus = $city === 'all'
                ? 'Message sent to all users'
                : "Message sent to users in {$city}";
        } catch (\Exception $broadcastError) {
            Log::warning('Broadcast failed: ' . $broadcastError->getMessage());
            $broadcastStatus = 'Message saved, but broadcasting is currently unavailable.';
        }

        return redirect()
            ->route('message.list')
            ->with('success', $broadcastStatus);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Broadcast message error: ' . $e->getMessage());

        return back()
            ->withInput()
            ->withErrors(['error' => 'Failed to send message. Please try again.']);
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
