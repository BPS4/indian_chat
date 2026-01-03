<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelFacilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Assuming facility ids from 1 to 10 and hotel ids from 1 to 6
        $hotelFacilities = [
            ['hotel_id' => 1, 'facility_id' => 1], // Taj - Free Wi-Fi
            ['hotel_id' => 1, 'facility_id' => 2], // Swimming Pool
            ['hotel_id' => 1, 'facility_id' => 5], // Restaurant
            ['hotel_id' => 1, 'facility_id' => 7], // Parking

            ['hotel_id' => 2, 'facility_id' => 1],
            ['hotel_id' => 2, 'facility_id' => 3],
            ['hotel_id' => 2, 'facility_id' => 6], // 24/7 Room Service

            ['hotel_id' => 3, 'facility_id' => 1],
            ['hotel_id' => 3, 'facility_id' => 5],
            ['hotel_id' => 3, 'facility_id' => 8], // Airport Shuttle

            ['hotel_id' => 4, 'facility_id' => 2],
            ['hotel_id' => 4, 'facility_id' => 3],
            ['hotel_id' => 4, 'facility_id' => 4], // Spa

            ['hotel_id' => 5, 'facility_id' => 1],
            ['hotel_id' => 5, 'facility_id' => 5],
            ['hotel_id' => 5, 'facility_id' => 9], // Pet Friendly

            ['hotel_id' => 6, 'facility_id' => 1],
            ['hotel_id' => 6, 'facility_id' => 5],
            ['hotel_id' => 6, 'facility_id' => 10], // Conference Room
            ['hotel_id' => 7, 'facility_id' => 1],
            ['hotel_id' => 7, 'facility_id' => 2],
            ['hotel_id' => 7, 'facility_id' => 5],

            ['hotel_id' => 8, 'facility_id' => 1],
            ['hotel_id' => 8, 'facility_id' => 4],
            ['hotel_id' => 8, 'facility_id' => 5],

            ['hotel_id' => 9, 'facility_id' => 1],
            ['hotel_id' => 9, 'facility_id' => 5],
            ['hotel_id' => 9, 'facility_id' => 8],

            ['hotel_id' => 10, 'facility_id' => 1],
            ['hotel_id' => 10, 'facility_id' => 2],
            ['hotel_id' => 10, 'facility_id' => 6],

            ['hotel_id' => 11, 'facility_id' => 1],
            ['hotel_id' => 11, 'facility_id' => 2],
            ['hotel_id' => 11, 'facility_id' => 5],

            ['hotel_id' => 12, 'facility_id' => 1],
            ['hotel_id' => 12, 'facility_id' => 4],
            ['hotel_id' => 12, 'facility_id' => 9],
        ];

        foreach ($hotelFacilities as $hf) {
            DB::table('hotel_facilities')->insert($hf);
        }
    }
}
