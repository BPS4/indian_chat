<?php

use App\Http\Controllers\Admin\AddOnsController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\FacilityGroupController;
use App\Http\Controllers\Admin\GiftCardController;
use App\Http\Controllers\Admin\GuestPhotoController;
use App\Http\Controllers\Admin\HotelRoomController;
use App\Http\Controllers\Admin\LocalityController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\OffersController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\chatcontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\commisionController;
use App\Http\Controllers\Admin\LogoController;


Route::get('', function () {
    if (session()->has('id')) {
        return redirect('/admin/dashboard');
    } else {
        return redirect('/admin/login');
    }
});


Route::get('/welcome',   function () {
    return view('welcome');
});


Route::get('/chat', [chatcontroller::class, 'index']);


Route::post('/send-message', [chatcontroller::class, 'send_message']);







Route::get('/', [LoginController::class, 'main']);
Route::get('/admin', [LoginController::class, 'index']);
Route::get('/admin/login', [LoginController::class, 'index'])->name('admin.login.form');
Route::post('/admin/auth/login', [LoginController::class, 'login'])->name('admin.login');
Route::get('/logout', [LoginController::class, 'logout']);

Route::group(['prefix' => 'admin'], function () {

    Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('admin.forgot.password');
    Route::post('send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('admin.send.otp');
    Route::get('verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('admin.verify.otp');
    Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('admin.otp.verify');
    Route::get('reset-password', [ForgotPasswordController::class, 'resetPasswordForm'])->name('admin.reset.password.form');
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('admin.reset.password');
});

Route::group(['prefix' => 'admin', 'middleware' => ['CheckSession']], function () {

    // Dashboard
    Route::get('dashboard/list', function () {
        return redirect('admin/dashboard');
    });
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    
    // Admin Chat/Broadcast
    Route::post('broadcast-message', [\App\Http\Controllers\Admin\AdminChatController::class, 'sendBroadcastMessage'])->name('admin.broadcast');
    Route::get('admin-messages', [\App\Http\Controllers\Admin\AdminChatController::class, 'getAdminMessages'])->name('admin.messages');
    Route::get('users-list', [\App\Http\Controllers\Admin\AdminChatController::class, 'getAllUsers'])->name('admin.users.list');
    Route::get('broadcast', function() {
        return view('admin.pages.broadcast');
    })->name('admin.broadcast.view');

    // profile

    Route::prefix('profile')->group(function () {
        Route::any('user/profile', [App\Http\Controllers\Admin\ProfileController::class, 'profile'])->name('profile');
        Route::any('user/profile/upload', [App\Http\Controllers\Admin\ProfileController::class, 'upload'])->name('profile.upload');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('admin.password.change');

    });

    // Message Management
    Route::prefix('message')->group(function () {
        Route::get('list', [App\Http\Controllers\Admin\HotelController::class, 'message_list'])->name('message.list');
        Route::get('add', function() {
            return view('admin.pages.messages.add');
        })->name('message.add');
        Route::post('store', [\App\Http\Controllers\Admin\AdminChatController::class, 'sendBroadcastMessage'])->name('message.store');
        Route::get('edit/{id}', [App\Http\Controllers\Admin\HotelController::class, 'edit_message'])->name('message.edit');
        Route::put('update/{id}', [App\Http\Controllers\Admin\HotelController::class, 'update_message'])->name('message.update');
        Route::delete('delete/{id}', [App\Http\Controllers\Admin\HotelController::class, 'delete_message'])->name('message.delete');
    });

   
    // Customers
    Route::prefix('customers')->group(function () {
        Route::get('list', [CustomerController::class, 'customer_list']);
        Route::any('add', [CustomerController::class, 'add_customer']);
        Route::get('view/{id}', [CustomerController::class, 'customer_view']);
    });

    // Search
    Route::prefix('search')->group(function () {
        Route::get('common-search', [DashboardController::class, 'common_search']);
    });

    // Bookings
    Route::prefix('withdrawal')->group(function () {
        Route::get('list', [AdminBookingController::class, 'bookings_list']);
        Route::get('add', [AdminBookingController::class, 'add_booking']);
        Route::post('status/{booking}', [AdminBookingController::class, 'updateStatus']);
        Route::get('/booking_details/{id}', [AdminBookingController::class, 'show'])
     ->name('booking.show');

    });

    // Offers
    Route::prefix('Offers')->group(function () {
        Route::get('list', [OffersController::class, 'Offers_list']);
        Route::get('add', [OffersController::class, 'add_offers']);
    });

    // Payments
    Route::prefix('Payments')->group(function () {
        Route::get('list', [PaymentController::class, 'Payments_list']);
        Route::get('/payment-receipt-download/{id}', [PaymentController::class, 'payment_receipt_download']);

    });

    Route::prefix('role')->group(function () {
        Route::get('list', [RoleController::class, 'index']);
        Route::any('permissions/{role_id}', [RoleController::class, 'permissions']);
        Route::any('edit/{role_id}', [RoleController::class, 'edit']);
        Route::any('add', [RoleController::class, 'add']);
    });

    Route::prefix('user')->group(function () {
        Route::get('list', [UserController::class, 'index']);
        Route::any('add', [UserController::class, 'addUser']);
        Route::any('edit/{id}', [UserController::class, 'editUser']);
        Route::any('delete/{id}', [UserController::class, 'deleteUser']);
        Route::any('update-status/{id}/{status}', [UserController::class, 'updateStatus']);
    });

    Route::any('/settings', [SettingsController::class, 'index']);
    Route::any('/settings/list', [SettingsController::class, 'index']);

    Route::prefix('location')->group(function () {
        Route::get('list', [LocationController::class, 'index'])->name('location.list');
        Route::get('add', [LocationController::class, 'add'])->name('location.addForm');
        Route::post('store', [LocationController::class, 'store'])->name('location.store');
        Route::get('edit/{location}', [LocationController::class, 'edit'])->name('location.editForm');
        Route::put('update/{location}', [LocationController::class, 'update'])->name('location.update');
        Route::delete('destroy/{location}', [LocationController::class, 'destroy'])->name('location.destroy');
    });
     Route::prefix('refral_commision')->group(function () {
        Route::get('list', [commisionController::class, 'refral_commision']);
        Route::get('add', [commisionController::class, 'add'])->name('commmision.create');
        Route::post('store', [commisionController::class, 'store'])->name('commision.store');
       
        
    });

       Route::prefix('app_logo')->group(function () {
        Route::get('list', [LogoController::class, 'index'])->name('app_logo.list');
        Route::get('add', [LogoController::class, 'create'])->name('app_logo.create');
        Route::post('store', [LogoController::class, 'store'])->name('app_logo.store');
       
        
    });


});

// Payment Gateway
Route::any('final-payments/{booking_id}', [BookingController::class, 'final_payments']);

// Ajax Request

Route::any('locality-check/{location_id}', [App\Http\Controllers\Admin\HotelController::class, 'locality_check']);
Route::get('/facilities-check/{group_id}', [App\Http\Controllers\Admin\FacilityController::class, 'facilities_check']);
