<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $fillable = [
        'location_id',
        'locality_id',
        'name',
        'description',
        'rating_avg',
        'review_count',
        'count',
        'is_featured',
        'latitude',
        'longitude',
        'property_rules',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class, 'hotel_id');
    }

    public function hotelsOffer()
    {
        return $this->hasOne(Offer::class, 'hotel_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function localities()
    {
        return $this->belongsTo(Localty::class, 'locality_id', 'id');
    }

    public function hotelPolicies()
    {
        return $this->hasOne(HotelPolicy::class, 'hotel_id');
    }

    /**
     * Get the photos of the hotel.
     */
    public function hotelPhotos(): HasMany
    {
        return $this->hasMany(HotelPhoto::class, 'hotel_id');
    }

    /**
     * Get the facilities available in the hotel.
     */
    public function hotelFacilities(): HasMany
    {
        return $this->hasMany(HotelFacility::class, 'hotel_id');
    }

    public function facilitiesNames()
    {
        return $this->hasManyThrough(
            FacilityMaster::class,       // Final model
            HotelFacility::class,        // Intermediate model
            'hotel_id',                  // Foreign key on HotelFacility
            'id',                        // Foreign key on FacilityMaster (usually 'id')
            'id',                        // Local key on Hotel
            'facility_id'         // Local key on HotelFacility
        );
    }

    public function facilities()
    {
        return $this->belongsToMany(
            FacilityMaster::class,
            'hotel_facilities',
            'hotel_id',
            'facility_id'
        )->with('group'); // eager load facility group
    }

    public function hotelReview(): HasMany
    {
        return $this->hasMany(Review::class, 'hotel_id');
    }

    public function hotelGuestPhotos(): HasMany
    {
        return $this->hasMany(GuestPhoto::class, 'hotel_id');
    }


     public function bookings()
    {
        return $this->hasMany(Booking::class, 'hotel_id', 'id');
    }

        public function booking_payments()
    {
        return $this->hasManyThrough(
            BookingPayment::class, // final model
            Booking::class,        // intermediate model
            'hotel_id',             // foreign key on Booking table
            'booking_id',          // foreign key on BookingPayment table
            'id',                  // local key on User table
            'booking_id'                   // local key on Booking table
        );
    }



}
