<?php

// database/seeders/CouponSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::create([
            'code' => 'WELCOME500',
            'discount_type' => 'flat',
            'discount_value' => 500,
            'valid_from' => Carbon::now()->subDays(5)->toDateString(),
            'valid_to' => Carbon::now()->addDays(30)->toDateString(),
            'min_booking_amount' => 2500,
            'max_discount' => null,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SAVE10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'valid_from' => Carbon::now()->toDateString(),
            'valid_to' => Carbon::now()->addDays(15)->toDateString(),
            'min_booking_amount' => 3000,
            'max_discount' => 1000,
            'is_active' => true,
        ]);
    }
}
