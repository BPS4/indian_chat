<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomFacilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Assuming facility ids from 1 to 10 and hotel ids from 1 to 6
        $roomFacilities = [
            ['room_type_id' => 1, 'facility_id' => 1], // Taj - Free Wi-Fi
            ['room_type_id' => 1, 'facility_id' => 2], // Swimming Pool
            ['room_type_id' => 1, 'facility_id' => 5], // Restaurant
            ['room_type_id' => 1, 'facility_id' => 7], // Parking

            ['room_type_id' => 2, 'facility_id' => 1],
            ['room_type_id' => 2, 'facility_id' => 3],
            ['room_type_id' => 2, 'facility_id' => 6], // 24/7 Room Service

            ['room_type_id' => 3, 'facility_id' => 1],
            ['room_type_id' => 3, 'facility_id' => 5],
            ['room_type_id' => 3, 'facility_id' => 8], // Airport Shuttle

            ['room_type_id' => 4, 'facility_id' => 2],
            ['room_type_id' => 4, 'facility_id' => 3],
            ['room_type_id' => 4, 'facility_id' => 4], // Spa

            ['room_type_id' => 5, 'facility_id' => 1],
            ['room_type_id' => 5, 'facility_id' => 5],
            ['room_type_id' => 5, 'facility_id' => 9], // Pet Friendly

            ['room_type_id' => 6, 'facility_id' => 1],
            ['room_type_id' => 6, 'facility_id' => 5],
            ['room_type_id' => 6, 'facility_id' => 10], // Conference Room
            ['room_type_id' => 7, 'facility_id' => 1],
            ['room_type_id' => 7, 'facility_id' => 2],
            ['room_type_id' => 7, 'facility_id' => 5],

            ['room_type_id' => 8, 'facility_id' => 1],
            ['room_type_id' => 8, 'facility_id' => 4],
            ['room_type_id' => 8, 'facility_id' => 5],

            ['room_type_id' => 9, 'facility_id' => 1],
            ['room_type_id' => 9, 'facility_id' => 5],
            ['room_type_id' => 9, 'facility_id' => 8],

            ['room_type_id' => 10, 'facility_id' => 1],
            ['room_type_id' => 10, 'facility_id' => 2],
            ['room_type_id' => 10, 'facility_id' => 6],

            ['room_type_id' => 11, 'facility_id' => 1],
            ['room_type_id' => 11, 'facility_id' => 2],
            ['room_type_id' => 11, 'facility_id' => 5],

            ['room_type_id' => 12, 'facility_id' => 1],
            ['room_type_id' => 12, 'facility_id' => 4],
            ['room_type_id' => 12, 'facility_id' => 9],
        ];

        foreach ($roomFacilities as $hf) {
            DB::table('room_facilities')->insert($hf);
        }
    }
}
