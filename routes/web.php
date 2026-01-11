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

    // profile

    Route::prefix('profile')->group(function () {
        Route::any('user/profile', [App\Http\Controllers\Admin\ProfileController::class, 'profile'])->name('profile');
        Route::any('user/profile/upload', [App\Http\Controllers\Admin\ProfileController::class, 'upload'])->name('profile.upload');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('admin.password.change');

    });

    // Hotel
    Route::prefix('message')->group(function () {
        Route::get('list', [App\Http\Controllers\Admin\HotelController::class, 'hotel_list'])->name('hotels.list');
        Route::any('add', [App\Http\Controllers\Admin\HotelController::class, 'store'])->name('message.store');
        Route::get('edit/{id}', [App\Http\Controllers\Admin\HotelController::class, 'edit'])->name('hotels.edit');
        Route::put('update/{id}', [App\Http\Controllers\Admin\HotelController::class, 'update'])->name('hotels.update');
        Route::post('status/{hotel}/toggle-status', [App\Http\Controllers\Admin\HotelController::class, 'toggleStatus']);

        Route::get('/hotel-room/{hotel_id}', [HotelRoomController::class, 'index'])->name('hotel-room.index');
        Route::get('/hotel-room/{hotel_id}/create', [HotelRoomController::class, 'create'])->name('hotel-room.create');
        Route::post('/hotel-room/{hotel_id}/store', [HotelRoomController::class, 'store'])->name('hotel-room.store');
        Route::get('/hotel/{hotelId}/room/{id}/edit', [HotelRoomController::class, 'edit'])->name('hotel-room.edit');
        Route::put('/hotel/{hotelId}/room/{id}', [HotelRoomController::class, 'update'])->name('hotel-room.update');
        Route::delete('/hotel/{hotelId}/room/{id}', [HotelRoomController::class, 'destroy'])->name('hotel-room.destroy');
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
    Route::prefix('locality')->group(function () {
        Route::get('list', [LocalityController::class, 'index'])->name('locality.list');
        Route::get('add', [LocalityController::class, 'add'])->name('locality.addForm');
        Route::post('store', [LocalityController::class, 'store'])->name('locality.store');
        Route::get('edit/{localty}', [LocalityController::class, 'edit'])->name('locality.editForm');
        Route::put('update/{localty}', [LocalityController::class, 'update'])->name('locality.update');
        Route::delete('destroy/{localty}', [LocalityController::class, 'destroy'])->name('locality.destroy');
    });
    Route::resource('addons', AddOnsController::class);
    Route::resource('facility', FacilityController::class);
    Route::post('/facility-group/add', [FacilityGroupController::class, 'store'])->name('facility-group.store');
    Route::get('/facility-group/list', [FacilityGroupController::class, 'index'])->name('facility-group.list');
    Route::delete('/facility-group/{id}', [FacilityGroupController::class, 'destroy'])->name('facility-group.destroy');
    Route::resource('coupons', CouponController::class);
    Route::resource('gift-card', GiftCardController::class);
    Route::prefix('reviews')->group(function () {
        Route::get('list', [ReviewController::class, 'index'])->name('review.list');
        Route::get('view/{id}', [ReviewController::class, 'show'])->name('review.view');
        Route::post('reply/{id}', [ReviewController::class, 'reply'])->name('review.reply');
        Route::post('status/{id}', [ReviewController::class, 'updateStatus'])->name('review.updateStatus');
    });
    Route::resource('slider', SliderController::class);
    Route::resource('term', TermController::class);
    Route::resource('guest-photo', GuestPhotoController::class);
});

// Payment Gateway
Route::any('final-payments/{booking_id}', [BookingController::class, 'final_payments']);

// Ajax Request

Route::any('locality-check/{location_id}', [App\Http\Controllers\Admin\HotelController::class, 'locality_check']);
Route::get('/facilities-check/{group_id}', [App\Http\Controllers\Admin\FacilityController::class, 'facilities_check']);
