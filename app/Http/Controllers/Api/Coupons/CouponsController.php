<?php

namespace App\Http\Controllers\Api\Coupons;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponsController extends Controller
{
    public function all_coupons()
    {
        $today = Carbon::today();

        $coupons = Coupon::where('is_active', 1)
            ->whereDate('valid_from', '<=', $today)
            ->whereDate('valid_to', '>=', $today)
            ->get();

        return response()->json([
            'message' => 'All Active & Valid Coupons',
            'data' => CouponResource::collection($coupons),
        ], 200);
    }
}
