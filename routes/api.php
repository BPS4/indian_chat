<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Hotel\HotelController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Coupons\CouponsController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\Term\TermController;
use App\Http\Controllers\Api\Hotel\HotelRoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactsController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Admin\ContactController;


use Illuminate\Support\Facades\Broadcast;

Broadcast::routes([
    'middleware' => ['jwt.auth'], // ✅ Use your JWT middleware
]);


Route::fallback(function () {
    return response()->json([
        'message' => 'API route not found. If you believe this is an error, please check the documentation.'
    ], 404);
});

Route::get('session-token', [AuthController::class, 'sessionToken']);

Route::middleware(['session.token'])->group(function () {

    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/admin-login', [AuthController::class, 'admin_login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

  

   
});
Route::middleware(['jwt.auth'])->group(function () {


    Route::post('get-contacts', [ContactsController::class, 'get_contacts']);

    Route::get('/all-chats', [ChatController::class, 'all_chats']);

    Route::get('/contact_details', [ContactController::class, 'contact_details']);
    Route::get('/total_users', [ContactController::class, 'total_users']);

    Route::post('/send-message', [ChatController::class, 'send_message']);

    Route::post('/chat/private', [ChatController::class, 'startPrivateChat']);
    Route::post('/create-chat/group', [ChatController::class, 'createGroup']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

    Route::get('conversations', [ChatController::class, 'myConversations']);

    Route::get('conversations-details/{id}', [ChatController::class, 'conversationMessages']);
    
    // Admin conversation (default for all users)
    Route::get('admin-messages', [ChatController::class, 'getAdminConversation']);

    // Message Read/Unread Functionality
    Route::get('messages/unread', [ChatController::class, 'getUnreadMessages']);
    Route::get('messages/unread-count', [ChatController::class, 'getUnreadCount']);
    Route::post('messages/{messageId}/mark-read', [ChatController::class, 'markAsRead']);
    Route::post('messages/mark-all-read', [ChatController::class, 'markAllAsRead']);
    Route::post('messages/mark-multiple-read', [ChatController::class, 'markMultipleAsRead']);






    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);


    

});

// Review
Route::get('reviews/{hotel_id}', [ReviewController::class, 'all_reviews']);


// Webhook
Route::post('/verify-payment', [BookingController::class, 'handleWebhook']);
