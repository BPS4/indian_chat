<?php

namespace App\Services\Hotel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HotelRoomSearchService
{
    public static function hasBreakfastIncluded($roomType): bool
    {
        $addon = collect($roomType->addons ?? [])->first(function ($a) {
            $name = strtolower($a->name ?? '');
            $price = (float) (($a->pivot->price ?? 0));
            return strpos($name, 'breakfast') !== false;
            // return strpos($name, 'breakfast') !== false && $price == 0.0;
        });
        if ($addon) return true;
        $fac = collect($roomType->facilities ?? [])->first(function ($f) {
            return stripos($f->facility_name ?? '', 'breakfast') !== false;
        });

        return (bool) $fac;
    }

    public static function matchesMealPlanAddon($roomType, ?string $mealPlan): bool
    {

        if (!$mealPlan) return true;
        $addons = collect($roomType->addons ?? [])->map(function ($a) {
            return [
                'name' => strtolower($a->name ?? ''),
                'price' => (float) ($a->pivot->price ?? 0),
            ];
        });
        $hasIncluded = function ($keyword) use ($addons) {
            $keyword = strtolower($keyword);
            Log::info("meal plan " . $keyword);

            return $addons->contains(function ($a) use ($keyword) {
                Log::info("meal plan addons " . $a['name']);

                return strpos($a['name'], $keyword) !== false;
                // return strpos($a['name'], $keyword) !== false && $a['price'] == 0.0;
            });
        };
        switch ($mealPlan) {
            case 'RO':
                return !self::hasBreakfastIncluded($roomType) && !$hasIncluded('half') && !$hasIncluded('full');
            case 'BB':
                return self::hasBreakfastIncluded($roomType);
            case 'HB':
                return $hasIncluded('half') || ($hasIncluded('breakfast') && ($hasIncluded('dinner') || $hasIncluded('lunch')));
            case 'FB':
                return $hasIncluded('full') || ($hasIncluded('breakfast') && $hasIncluded('lunch') && $hasIncluded('dinner'));
        }
        return true;
    }

    // public static function matchFacilitiesAll($roomType, ?array $facilityIds): bool
    // {
    //     if (!$facilityIds || count($facilityIds) === 0) return true;
    //     $ids = collect($roomType->facilities ?? [])->pluck('id')->all();
    //     return collect($facilityIds)->every(fn($id) => in_array($id, $ids));
    // }

    // public static function matchesPreferences($roomType, ?array $preferences): bool
    // {
    //     if (!$preferences || count($preferences) === 0) return true;
    //     $names = collect($roomType->facilities ?? [])->pluck('facility_name')->map(fn($n) => strtolower($n ?? ''))->all();
    //     foreach ($preferences as $pref) {
    //         $pref = strtolower($pref);
    //         $hit = false;
    //         foreach ($names as $fname) {
    //             if (strpos($fname, $pref) !== false) {
    //                 $hit = true;
    //                 break;
    //             }
    //         }
    //         if (!$hit) return false;
    //     }
    //     return true;
    // }

    // public static function passesFreeCancellation($hotel, Request $request): bool
    // {
    //     if (!$request->boolean('free_cancellation')) return true;
    //     $policy = optional($hotel->hotelPolicies->first())?->cancellation_policy;
    //     if (!$policy) return false;
    //     return stripos($policy, 'free') !== false && stripos($policy, 'cancel') !== false;
    // }
}
