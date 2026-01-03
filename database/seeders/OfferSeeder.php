<?php

// database/seeders/OfferSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Offer;
use Carbon\Carbon;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'hotel_id' => 1,
                'title' => 'Flat ₹500 Off on Luxury Rooms',
                'description' => 'Only at Hotel Paradise.',
                'discount_type' => 'flat',
                'discount_value' => 500,
                'min_booking_amount' => 2000,
                'max_discount_amount' => null,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(15)->toDateString(),
                'status' => 'active',
            ],
            [
                'hotel_id' => 2,
                'title' => 'Festival Discount',
                'description' => 'Get 10% off on bookings above ₹3000.',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_booking_amount' => 3000,
                'max_discount_amount' => 1000,
                'start_date' => Carbon::now()->subDays(2)->toDateString(),
                'end_date' => Carbon::now()->addDays(10)->toDateString(),
                'status' => 'active',
            ],
            [
                'hotel_id' => 3,
                'title' => 'Winter Sale',
                'description' => 'Flat ₹1000 off for all December bookings.',
                'discount_type' => 'flat',
                'discount_value' => 1000,
                'min_booking_amount' => 2500,
                'max_discount_amount' => null,
                'start_date' => '2025-12-01',
                'end_date' => '2025-12-31',
                'status' => 'inactive',
            ],
            [
                'hotel_id' => 4,
                'title' => 'Early Bird Deal',
                'description' => 'Book early and save 15%.',
                'discount_type' => 'percent',
                'discount_value' => 15,
                'min_booking_amount' => 4000,
                'max_discount_amount' => 1200,
                'start_date' => Carbon::now()->addDays(3)->toDateString(),
                'end_date' => Carbon::now()->addDays(20)->toDateString(),
                'status' => 'active',
            ],
            [
                'hotel_id' => 5,
                'title' => 'Weekend Bonanza',
                'description' => '₹750 off for weekend stays.',
                'discount_type' => 'flat',
                'discount_value' => 750,
                'min_booking_amount' => 3000,
                'max_discount_amount' => null,
                'start_date' => Carbon::now()->toDateString(),
                'end_date' => Carbon::now()->addDays(7)->toDateString(),
                'status' => 'active',
            ],
            [
                'hotel_id' => 6,
                'title' => 'Festive Flash Sale',
                'description' => 'Enjoy 12% off for festive bookings!',
                'discount_type' => 'percent',
                'discount_value' => 12,
                'min_booking_amount' => 3500,
                'max_discount_amount' => 1500,
                'start_date' => Carbon::now()->addDays(1)->toDateString(),
                'end_date' => Carbon::now()->addDays(12)->toDateString(),
                'status' => 'active',
            ],
        ];

        foreach ($offers as $offer) {
            Offer::create([
                ...$offer,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
