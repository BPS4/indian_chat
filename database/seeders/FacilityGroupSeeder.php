<?php

namespace Database\Seeders;

use App\Models\FacilityGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilityGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['group_name' => 'Basic', 'description' => 'Essential hotel amenities'],
            ['group_name' => 'Highlighted', 'description' => 'Key selling features'],
            ['group_name' => 'General Services', 'description' => 'Common facilities for guests'],
        ];

        foreach ($groups as $group) {
            FacilityGroup::create($group);
        }
    }
}
