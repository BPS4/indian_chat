<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RoomTypeAddonPricesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $roomTypeIds = [ 2, 3, 4, 5]; // adjust based on what's in DB
        $addonIds = [1, 2, 3, 4]; // adjust based on what's in DB

        foreach ($roomTypeIds as $roomTypeId) {
            foreach ($addonIds as $addonId) {
                DB::table('room_type_addon_prices')->insert([
                    'room_type_id' => $roomTypeId,
                    'addon_id' => $addonId,
                    'price' => $faker->randomFloat(2, 500, 3000),
                    'per_person' => $faker->boolean(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
