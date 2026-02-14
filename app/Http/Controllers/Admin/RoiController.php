<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Investment_wallet;
use App\Models\RoiGenerator;
use App\Models\Commision;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoiController extends Controller
{
    public function runDailyROI()
{
    $today = Carbon::today();

    DB::beginTransaction();

    try {

        // Get ROI percentage from commissions table
        $commission = Commision::first(); 
        $roiPercentage = $commission->roi ?? 0;

        if ($roiPercentage <= 0) {
            return "ROI percentage not set.";
        }

        // Get all users with investment balance
        $wallets = Investment_wallet::where('investment_balance', '>', 0)->get();

        foreach ($wallets as $wallet) {

            // Prevent duplicate for same day
            $exists = RoiGenerator::where('user_id', $wallet->user_id)
                ->whereDate('roi_date', $today)
                ->exists();

            if ($exists) {
                continue;
            }

            $investmentAmount = $wallet->investment_balance;

            $roiAmount = ($investmentAmount * $roiPercentage) / 100;

            // Save ROI record
            RoiGenerator::create([
                'user_id' => $wallet->user_id,
                'investment_amount' => $investmentAmount,
                'roi_percentage' => $roiPercentage,
                'roi_amount' => $roiAmount,
                'roi_date' => $today,
            ]);

            // Add ROI to wallet (assuming roi_balance column exists)
            $wallet->increment('roi_balance', $roiAmount);
        }

        DB::commit();

        return "Daily ROI generated successfully.";

    } catch (\Exception $e) {

        DB::rollBack();
        return $e->getMessage();
    }
}
}
