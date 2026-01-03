<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddonsTableSeeder extends Seeder
{
    public function run()
    {
        $addons = [
            ['name' => 'Breakfast', 'description' => 'Daily continental breakfast buffet.'],
            ['name' => 'Airport Pickup', 'description' => 'Private car pickup from the airport.'],
            ['name' => 'Spa Access', 'description' => 'Unlimited spa access during stay.'],
            ['name' => 'Late Checkout', 'description' => 'Check out up to 4 hours late.'],
        ];

        foreach ($addons as $addon) {
            DB::table('addons')->insert([
                'name' => $addon['name'],
                'description' => $addon['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
