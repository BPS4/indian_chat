<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Assuming locations IDs are as inserted above from 1 to 5
        $localities = [
            ['location_id' => 1, 'name' => 'Andheri'],
            ['location_id' => 1, 'name' => 'Bandra'],
            ['location_id' => 2, 'name' => 'Connaught Place'],
            ['location_id' => 2, 'name' => 'Saket'],
            ['location_id' => 3, 'name' => 'Indiranagar'],
            ['location_id' => 3, 'name' => 'MG Road'],
            ['location_id' => 4, 'name' => 'T. Nagar'],
            ['location_id' => 4, 'name' => 'Velachery'],
            ['location_id' => 5, 'name' => 'Salt Lake'],
            ['location_id' => 5, 'name' => 'Park Street'],
        ];

        foreach ($localities as $locality) {
            DB::table('localties')->insert($locality);
        }
    }
}
