<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RoomPricesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Seed pricing for room_type_id 1 to 12
        for ($i = 1; $i <= 12; $i++) {
            DB::table('room_prices')->insert([
                'room_type_id' => $i,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(3)->toDateString(),
                'base_price' => $faker->randomFloat(2, 4000, 20000), // Adjusted for luxury hotels
                'extra_person_price' => $faker->randomFloat(2, 800, 3000),
                'currency' => 'USD', // or 'INR' if preferred
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
