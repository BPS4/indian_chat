<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;
use App\Models\message;
use Illuminate\Support\Facades\DB;


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
        'user_id',
        'name',
        'email',
        'country',
        'state',
        'city',
        'country_code',
        'mobile',
        'role_id',
        'password',
        'wallet_amount',
        'profile_pic',
        'referral_code',
        'referred_by',
        'status',
    ];

     const CUSTOMER = 2;

    protected static function booted()
    {
        static::creating(function ($user) {

            if (!empty($user->user_id)) {
                return;
            }

            $lastNumber = DB::table('users')
                ->selectRaw("CAST(SUBSTRING(user_id, 5) AS UNSIGNED) as num")
                ->where('user_id', 'like', 'ind_%')
                ->orderByDesc('num')
                ->value('num');

            $nextNumber = ($lastNumber ?? 0) + 1;

            $user->user_id = 'ind_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        });
    }

public function sponsor()
{
    return $this->belongsTo(User::class, 'referred_by', 'id');
}

protected $appends = ['sponser_id'];

public function getSponserIdAttribute()
{
    return $this->sponsor?->referral_code;
}


    

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

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }


    
}
