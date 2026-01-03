<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class HotelPhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Sample photos for hotels (assuming hotel ids 1 to 6 as seeded before)
        $photos = [
            ['hotel_id' => 1, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Front view of The Taj Mahal Palace'],
            ['hotel_id' => 1, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => false, 'caption' => 'Luxury suite room'],
            ['hotel_id' => 2, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Lobby of The Oberoi Mumbai'],
            ['hotel_id' => 3, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Exterior of The Imperial, New Delhi'],
            ['hotel_id' => 4, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'ITC Gardenia Swimming Pool'],
            ['hotel_id' => 5, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'The Leela Palace Beach View'],
            ['hotel_id' => 6, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Lobby of The Oberoi Grand, Kolkata'],
            ['hotel_id' => 7, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'JW Marriott Pune Entrance'],
            ['hotel_id' => 8, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Taj Falaknuma Palace Grounds'],
            ['hotel_id' => 9, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Le Meridien Jaipur Lobby'],
            ['hotel_id' => 10, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Vivanta Dal View, Dal Lake View'],
            ['hotel_id' => 11, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'Alila Diwa Goa Resort'],
            ['hotel_id' => 12, 'photo_url' => "images/hotels/hotel.png", 'is_cover' => true, 'caption' => 'The Tamara Coorg Cottages'],
        ];

        foreach ($photos as $photo) {
            DB::table('hotel_photos')->insert($photo);
        }
    }
}
