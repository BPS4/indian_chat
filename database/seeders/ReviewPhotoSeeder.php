<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ReviewPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('review_photos')->insert([
            [
               'review_id' => 1,
               'photo_url' => 'uploads/reviews/photo1.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
               'review_id' => 2,
               'photo_url' => 'uploads/reviews/photo1.jpg',

                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
    }
}
