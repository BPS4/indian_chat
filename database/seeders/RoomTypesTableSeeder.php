<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RoomTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roomTypes = [
            // Hotel 1 - The Taj Mahal Palace
            [
                'hotel_id' => 1,
                'room_name' => 'Deluxe Room',
                'description' => 'Spacious room with city view, modern amenities, and queen-sized bed.',
                'max_guests' => 2,
                'room_size' => 350,
                'bed_type' => 'Queen',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hotel_id' => 1,
                'room_name' => 'Presidential Suite',
                'description' => 'Luxurious suite with private lounge, sea view, and king-sized bed.',
                'max_guests' => 4,
                'room_size' => 1200,
                'bed_type' => 'King',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 2 - The Oberoi Mumbai
            [
                'hotel_id' => 2,
                'room_name' => 'Executive Room',
                'description' => 'Comfortable room with workspace and city views.',
                'max_guests' => 2,
                'room_size' => 400,
                'bed_type' => 'Queen',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 3 - The Imperial, New Delhi
            [
                'hotel_id' => 3,
                'room_name' => 'Standard Room',
                'description' => 'Cozy room suitable for business travelers.',
                'max_guests' => 2,
                'room_size' => 320,
                'bed_type' => 'Double',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 4 - ITC Gardenia
            [
                'hotel_id' => 4,
                'room_name' => 'Luxury Room',
                'description' => 'Elegant room with garden view and modern facilities.',
                'max_guests' => 2,
                'room_size' => 450,
                'bed_type' => 'King',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 5 - The Leela Palace Chennai
            [
                'hotel_id' => 5,
                'room_name' => 'Sea View Suite',
                'description' => 'Suite with beach view and private balcony.',
                'max_guests' => 3,
                'room_size' => 600,
                'bed_type' => 'King',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 6 - The Oberoi Grand, Kolkata
            [
                'hotel_id' => 6,
                'room_name' => 'Heritage Room',
                'description' => 'Colonial-style room with classic decor.',
                'max_guests' => 2,
                'room_size' => 400,
                'bed_type' => 'Queen',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 7 - JW Marriott Pune
            [
                'hotel_id' => 7,
                'room_name' => 'Club Room',
                'description' => 'Modern room with access to executive lounge.',
                'max_guests' => 2,
                'room_size' => 420,
                'bed_type' => 'Queen',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 8 - Taj Falaknuma Palace, Hyderabad
            [
                'hotel_id' => 8,
                'room_name' => 'Palace Room',
                'description' => 'Lavish room with heritage interiors and palace views.',
                'max_guests' => 2,
                'room_size' => 550,
                'bed_type' => 'King',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 9 - Le Meridien Jaipur
            [
                'hotel_id' => 9,
                'room_name' => 'Aravalli View Room',
                'description' => 'Room with scenic mountain view and balcony.',
                'max_guests' => 3,
                'room_size' => 460,
                'bed_type' => 'Double',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 10 - Vivanta Dal View, Srinagar
            [
                'hotel_id' => 10,
                'room_name' => 'Lake View Room',
                'description' => 'Elegant room overlooking Dal Lake.',
                'max_guests' => 2,
                'room_size' => 500,
                'bed_type' => 'King',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 11 - Alila Diwa Goa
            [
                'hotel_id' => 11,
                'room_name' => 'Terrace Room',
                'description' => 'Spacious room with private terrace and tropical views.',
                'max_guests' => 2,
                'room_size' => 470,
                'bed_type' => 'Queen',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hotel 12 - The Tamara Coorg
            [
                'hotel_id' => 12,
                'room_name' => 'Luxury Cottage',
                'description' => 'Private cottage in the hills with scenic views.',
                'max_guests' => 2,
                'room_size' => 600,
                'bed_type' => 'King',
                'photo_url' => "images/rooms/rooms_image.png",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roomTypes as $roomType) {
            DB::table('room_types')->insert($roomType);
        }
    }
}
