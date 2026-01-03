<?php

// database/seeders/GiftCardSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GiftCard;
use Carbon\Carbon;

class GiftCardSeeder extends Seeder
{
    public function run(): void
    {
        GiftCard::create([
            'code' => 'GIFT1000',
            'balance_amount' => 1000.00,
            'expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
            'is_active' => true,
        ]);

        GiftCard::create([
            'code' => 'GIFT500',
            'balance_amount' => 500.00,
            'expiry_date' => Carbon::now()->addMonths(3)->toDateString(),
            'is_active' => true,
        ]);
    }
}
