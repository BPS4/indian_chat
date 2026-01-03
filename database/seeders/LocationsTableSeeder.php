<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $locations = [
            ['city' => 'Mumbai', 'state' => 'Maharashtra', 'country' => 'India', 'zipcode' => '400001', 'latitude' => 19.0760, 'longitude' => 72.8777, 'image' => 'images/locations/goa.png'],
            ['city' => 'Delhi', 'state' => 'Delhi', 'country' => 'India', 'zipcode' => '110001', 'latitude' => 28.6139, 'longitude' => 77.2090, 'image' => 'images/locations/goa.png'],
            ['city' => 'Bangalore', 'state' => 'Karnataka', 'country' => 'India', 'zipcode' => '560001', 'latitude' => 12.9716, 'longitude' => 77.5946, 'image' => 'images/locations/goa.png'],
            ['city' => 'Chennai', 'state' => 'Tamil Nadu', 'country' => 'India', 'zipcode' => '600001', 'latitude' => 13.0827, 'longitude' => 80.2707, 'image' => 'images/locations/goa.png'],
            ['city' => 'Kolkata', 'state' => 'West Bengal', 'country' => 'India', 'zipcode' => '700001', 'latitude' => 22.5726, 'longitude' => 88.3639, 'image' => 'images/locations/goa.png'],
        ];

        foreach ($locations as $location) {
            DB::table('locations')->insert($location);
        }
    }
}
