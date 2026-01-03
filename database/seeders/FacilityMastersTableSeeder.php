<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilityMastersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $facilities = [
            ['group_id' => 1,'facility_name' => 'Free Wi-Fi', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 1,'facility_name' => 'Swimming Pool', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 1,'facility_name' => 'Gym', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 2,'facility_name' => 'Spa', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 2,'facility_name' => 'Restaurant', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 2,'facility_name' => '24/7 Room Service', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 3,'facility_name' => 'Parking', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 3,'facility_name' => 'Airport Shuttle', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 3,'facility_name' => 'Pet Friendly', 'icon' => 'images/hotel_facility/icon.png'],
            ['group_id' => 3,'facility_name' => 'Conference Room', 'icon' => 'images/hotel_facility/icon.png'],
        ];

        foreach ($facilities as $facility) {
            DB::table('facility_masters')->insert($facility);
        }
    }
}
