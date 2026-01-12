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

    Route::post('/send-message', [ChatController::class, 'send_message']);

    Route::post('/chat/private', [ChatController::class, 'startPrivateChat']);
    Route::post('/create-chat/group', [ChatController::class, 'createGroup']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);

    Route::get('conversations', [ChatController::class, 'myConversations']);

    Route::get('conversations-details/{id}', [ChatController::class, 'conversationMessages']);
    
    // Admin conversation (default for all users)
    Route::get('admin-messages', [ChatController::class, 'getAdminConversation']);






    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);


    Route::post('create-order', [BookingController::class, 'store']);

    Route::get('all-bookings', [BookingController::class, 'all_bookings']);
    Route::get('booking-details/{id}', [BookingController::class, 'booking_details']);

    Route::get('cancel-bookings/{id}', [BookingController::class, 'cancel_booking']);
    Route::get('cancel-bookings-details/{id}', [BookingController::class, 'cancel_bookings_details']);


    // All Transactions
    Route::get('all-transactions', [BookingController::class, 'all_transactions']);
    // Add Review
    Route::post('add-review', [ReviewController::class, 'add_review']);
});

// Review
Route::get('reviews/{hotel_id}', [ReviewController::class, 'all_reviews']);


// Webhook
Route::post('/verify-payment', [BookingController::class, 'handleWebhook']);
