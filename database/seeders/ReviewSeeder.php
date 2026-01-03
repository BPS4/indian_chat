<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $hotels = Hotel::all();

        if ($users->isEmpty() || $hotels->isEmpty()) {
            $this->command->warn('⚠️ Please seed users and hotels first!');
            return;
        }

        foreach ($hotels as $hotel) {
            // Randomly select 3–10 users to review this hotel
            $selectedUsers = $users->random(min(10, max(3, $users->count())));

            foreach ($selectedUsers as $user) {
                Review::create([
                    'user_id'     => $user->id,
                    'hotel_id'    => $hotel->id,
                    'booking_id'  => null, // Optional: you could attach to a real booking if needed
                    'rating'      => rand(3, 5),
                    'review'      => fake()->sentence(15),
                    'is_approved' => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        $this->command->info('✅ Reviews seeded successfully for hotels!');
    }
}
