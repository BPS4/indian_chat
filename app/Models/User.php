<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

// use App\modal\Booking;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'role_id',
        'password',
        'wallet_amount',
        'status',
    ];

    const CUSTOMER = 2;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'at_whatsapp' => 'boolean',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'id');
    }

        public function booking_payments()
    {
        return $this->hasManyThrough(
            BookingPayment::class, // final model
            Booking::class,        // intermediate model
            'user_id',             // foreign key on Booking table
            'booking_id',          // foreign key on BookingPayment table
            'id',                  // local key on User table
            'booking_id'                   // local key on Booking table
        );
    }

     public function payments()
    {
        return $this->hasMany(BookingPayment::class, 'booking_id', 'booking_id');
    }

    public function rooms()
    {
        return $this->hasMany(BookingRoom::class, 'booking_id', 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function conversations()
{
    return $this->belongsToMany(Conversation::class)
                ->withPivot('is_admin')
                ->withTimestamps();
}











}
