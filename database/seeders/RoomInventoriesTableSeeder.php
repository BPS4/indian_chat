<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomInventoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Start from today and go for 90 days
        $startDate = Carbon::today();
        $days = 90;

        // Room type pricing and availability per ID
        $roomInventoryDetails = [
            1 => ['price' => 8000.00, 'rooms' => 5],   // Deluxe Room
            2 => ['price' => 25000.00, 'rooms' => 2],  // Presidential Suite
            3 => ['price' => 7000.00, 'rooms' => 8],   // Executive Room
            4 => ['price' => 4500.00, 'rooms' => 10],  // Standard Room
            5 => ['price' => 8500.00, 'rooms' => 6],   // Luxury Room
            6 => ['price' => 9000.00, 'rooms' => 4],   // Sea View Suite
            7 => ['price' => 7500.00, 'rooms' => 7],   // Heritage Room
            8 => ['price' => 9500.00, 'rooms' => 6],   // Club Room
            9 => ['price' => 20000.00, 'rooms' => 3],  // Palace Room
            10 => ['price' => 7800.00, 'rooms' => 5],  // Aravalli View Room
            11 => ['price' => 8800.00, 'rooms' => 4],  // Lake View Room
            12 => ['price' => 9200.00, 'rooms' => 4],  // Terrace Room
        ];

        for ($day = 0; $day < $days; $day++) {
            $date = $startDate->copy()->addDays($day)->toDateString();
            $data = [];

            foreach ($roomInventoryDetails as $roomTypeId => $details) {
                $data[] = [
                    'room_type_id'     => $roomTypeId,
                    'date'             => $date,
                    'available_rooms'  => $details['rooms'],
                    'price_per_night'  => $details['price'],
                    'is_active'        => true,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            DB::table('room_inventories')->insert($data);
        }
    }
}
