<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sliders')->insert([
            [
                'title' => 'Flat 25% Off on Your First Stay',
                'subtitle' => 'Apply code WELCOMEMINT to avail this discount on your\nfirst stay booking',
                'image_path' => "images/slider/slider.png",
                'link' => '/about',
                'button_text' => 'Learn More',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Flat 25% Off on Your First Stay',
                'subtitle' => 'Apply code WELCOMEMINT to avail this discount on your\nfirst stay booking',
                'image_path' => "images/slider/slider.png",
                'link' => '/sale',
                'button_text' => 'Book Now',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Flat 25% Off on Your First Stay',
                'subtitle' => 'Apply code WELCOMEMINT to avail this discount on your\nfirst stay booking',
                'image_path' => "images/slider/slider.png",
                'link' => '/subscribe',
                'button_text' => 'Subscribe',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
