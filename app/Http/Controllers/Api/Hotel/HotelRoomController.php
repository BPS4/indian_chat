<?php

namespace App\Http\Controllers\Api\Hotel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\Hotel\HotelRoomSearchService as RoomSearch;
use Illuminate\Support\Facades\Log;

class HotelRoomController extends Controller
{
    public function RoomDetail($hotelId, $roomTypeId)
    {
        try {

            // Fetch hotel with that room type and related data
            $hotel = Hotel::with([
                'roomTypes' => function ($query) use ($roomTypeId) {
                    $query->where('id', $roomTypeId)
                        ->with(['facilities', 'addons', 'roomPrices']);
                },
                'hotelPolicies'
            ])->findOrFail($hotelId);

            $room = $hotel->roomTypes->first();
            //  Room not found under this hotel
            if (!$room) {
                return response()->json([
                    'status' => false,
                    'message' => 'Room type not found for this hotel.'
                ], 404);
            }

            // Prepare rate plans from room prices
            // $ratePlans = $room->roomPrices ? $room->roomPrices->map(function ($price) {
            //     return [
            //         'id' => $price->id,
            //         'start_date' => $price->start_date,
            //         'end_date' => $price->end_date,
            //         'base_price' => (float) $price->base_price,
            //         'extra_person_price' => (float) $price->extra_person_price,
            //         'currency' => $price->currency ?? 'INR',
            //     ];
            // })->values() : [];

            // Prepare addons/meal plans as rate plan options
            $addonPlans = $room->addons ? $room->addons->map(function ($addon) {
                return [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'description' => $addon->description ?? null,
                    'price' => (float) ($addon->pivot->price ?? 0),
                    'per_person' => (bool) ($addon->pivot->per_person ?? false),
                ];
            })->values() : [];

            // Prepare detailed room response
            $roomData = [
                'id' => $room->id,
                'name' => $room->room_name,
                'room_size' => $room->room_size,
                'max_guests' => $room->max_guests,
                'max_child' => $room->max_child ?? null,
                'bed_type' => $room->bed_type,
                'description' => $room->description,
                // 'features' => $room->features ? $room->features->pluck('name') : [],
                'facilities' => $room->facilities ? $room->facilities : [],
                'free_addons' => $room->addons ? $room->addons
                    ->where('price' < 0)
                    ->pluck('name') : [],
                'images' => $room->photo_url,
                // 'rate_plans' => $ratePlans,
            ];

            // Prepare policies with check-in/check-out times
            $policies = [
                'check_in_time' => $hotel->hotelPolicies?->check_in_time ?? null,
                'check_out_time' => $hotel->hotelPolicies?->check_out_time ?? null,
                'cancellation_policy' => $hotel->hotelPolicies?->cancellation_policy ?? null,
                'extra_bed_policy' => $hotel->hotelPolicies?->extra_bed_policy ?? null,
                'child_policy' => $hotel->hotelPolicies?->child_policy ?? null,
                'addon_plans' => $addonPlans,

            ];

            // Return final JSON
            return response()->json([
                'status' => true,
                'hotel' => [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'location' => $hotel->location,
                    // 'city' => $hotel->city ?? null,
                    'rating' => $hotel->rating_avg ?? null,
                ],
                'extra_features' => $policies,
                'room' => $roomData
            ]);
        } catch (\Exception $e) {
            Log::error('RoomDetail Error', [
                'hotel_id' => $hotelId,
                'room_type_id' => $roomTypeId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching room details.'
            ], 500);
        }
    }


    public function hotelRooms(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|integer|min:1',
            'location_id' => 'nullable|integer',
            'locality_id' => 'nullable|integer',
            'checkIn' => 'required|date|after_or_equal:today',
            'checkOut' => 'required|date|after:checkIn',
            'adults' => 'required|integer|min:1',
            'child' => 'nullable|numeric',
            'rooms' => 'nullable|numeric',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'sort_by' => 'nullable|string|in:price_asc,price_desc,rating_asc,rating_desc',
            'breakfast_included' => 'nullable|boolean',
            'meal_plan' => 'nullable|string|in:RO,BB,HB,FB', // Room Only, Bed & Breakfast, Half Board, Full Board
            'facility_ids' => 'nullable|array',
            'facility_ids.*' => 'integer',
            'preferences' => 'nullable|array', // names e.g., non_smoking, high_floor, city_view
            'preferences.*' => 'string',
            'room_type_ids' => 'nullable|array',
            'room_type_ids.*' => 'integer',
            'bed_type' => 'nullable|string',
            'free_cancellation' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $hotelId = $request->hotel_id;
        $checkIn = Carbon::parse($request->checkIn);
        $checkOut = Carbon::parse($request->checkOut);
        $adults = (int) $request->adults;
        $children = (int) ($request->child ?? 0);
        $requestedRooms = max(1, (int) ($request->rooms ?? 1));
        $originalRequestedRooms = $requestedRooms;
        $totalGuests = $adults + $children;

        // Generate date range
        $dates = collect();
        for ($date = $checkIn->copy(); $date->lt($checkOut); $date->addDay()) {
            $dates->push($date->toDateString());
        }

        // Load hotel with relationships
        $hotel = Hotel::with([
            'roomTypes.inventories' => function ($q) use ($dates) {
                $q->whereIn('date', $dates)
                    ->where('available_rooms', '>', 0)
                    ->where('is_active', true);
            },
            'roomTypes.roomPrices' => function ($q) use ($dates) {
                $q->where(function ($query) use ($dates) {
                    foreach ($dates as $date) {
                        $query->orWhere(function ($q2) use ($date) {
                            $q2->where('start_date', '<=', $date)
                                ->where('end_date', '>=', $date);
                        });
                    }
                });
            },
            'roomTypes.facilities:id,facility_name,icon',
            'roomTypes.addons',
            'hotelPolicies',
            'hotelsOffer',
        ])->findOrFail($hotelId);

        $nights = max(1, $dates->count());
        // Prepare room data
        $rooms = $hotel->roomTypes->map(function ($room) use ($dates) {
            $available = $room->inventories->pluck('available_rooms')->min() ?? 0;

            $totalPrice = 0;
            foreach ($dates as $date) {
                $priceForDate = $room->roomPrices->first(
                    fn($p) => $date >= $p->start_date && $date <= $p->end_date
                );
                if (!$priceForDate) return null;
                $totalPrice += $priceForDate->base_price;
            }
            return [
                'room_type_id' => $room->id,
                'room_name' => $room->room_name,
                'max_adults' => $room->max_guests,
                'max_child' => $room->max_child,
                'available_rooms' => $available,
                'price_per_room' => round($totalPrice, 2),
                'bed_type' => $room->bed_type,
                'photo_url' => $room->photo_url,
                'total_price' => $totalPrice,
                'facilities' => $room->facilities->map(fn($f) => [
                    'id' => $f->id,
                    'icon' => $f->icon,
                    'title' => $f->facility_name
                ])->values(),
                'raw_facilities' => $room->facilities,
                'raw_addons' => $room->addons,
            ];
        })->filter(fn($r) => $r && $r['available_rooms'] > 0)->values();

        // Apply room-level filters (pre-combo)
        $filterRequest = $request;
        $rooms = $rooms->filter(function ($r) use ($filterRequest) {
            // Map back minimal mock object to reuse closures expecting relations
            $mock = new class($r) {
                public $facilities;
                public $addons;
                public function __construct($r)
                {
                    $this->facilities = $r['raw_facilities'];
                    $this->addons = $r['raw_addons'];
                }
            };
            // if ($filterRequest->boolean('breakfast_included') && !RoomSearch::hasBreakfastIncluded($mock)) return false;
            // Meal plan filter moved to combo level - check after addons are selected
            // if ($filterRequest->meal_plan && !RoomSearch::matchesMealPlanAddon($mock, $filterRequest->meal_plan)) return false;
            // if ($filterRequest->facility_ids && !RoomSearch::matchFacilitiesAll($mock, $filterRequest->facility_ids)) return false;
            // if ($filterRequest->preferences && !RoomSearch::matchesPreferences($mock, $filterRequest->preferences)) return false;
            // if ($filterRequest->room_type_ids && !in_array($r['room_type_id'], $filterRequest->room_type_ids)) return false;
            // if ($filterRequest->bed_type && strcasecmp($filterRequest->bed_type, $r['bed_type']) !== 0) return false;
            // if ($filterRequest->min_price && $r['price_per_room'] < (float) $filterRequest->min_price) return false;
            // if ($filterRequest->max_price && $r['price_per_room'] > (float) $filterRequest->max_price) return false;
            return true;
        })->values();

        if ($rooms->isEmpty()) {
            return response()->json(['message' => 'No rooms available for selected dates.'], 404);
        }

        $requiredAdultsPerRoom = (int) ceil($adults / max(1, $originalRequestedRooms));
        $requiredChildrenPerRoom = (int) ceil($children / max(1, $originalRequestedRooms));

        // Sort by price first (lowest first), then by capacity fit
        $rooms = $rooms->sortBy(function ($room) use ($requiredAdultsPerRoom, $requiredChildrenPerRoom) {
            $maxAdults = (int) ($room['max_adults'] ?? 0);
            $maxChildren = (int) ($room['max_child'] ?? 0);
            $capacity = $maxAdults + $maxChildren;

            $isAdultEnough = $maxAdults >= $requiredAdultsPerRoom ? 0 : 1;
            $isChildEnough = $maxChildren >= $requiredChildrenPerRoom ? 0 : 1;

            return [
                $room['price_per_room'] ?? INF,  // priority 1: lower price first
                $isAdultEnough,                   // priority 2: must satisfy adult requirement
                $isChildEnough,                   // priority 3: must satisfy child requirement
                -$capacity,                       // priority 4: higher capacity preferred (negative for desc)
            ];
        })->values();

        $roomRequirement = $this->assessRoomRequirement($rooms, $originalRequestedRooms, $totalGuests);

        if (!$roomRequirement['can_fit']) {
            return response()->json([
                'message' => 'Not enough available rooms to accommodate all guests for the selected dates.',
                'hotel_id' => $hotel->id,
                'hotel_name' => $hotel->name,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'adults' => $adults,
                'child' => $children,
            ], 409);
        }

        $fitsOriginalRequest = $roomRequirement['fits_requested'];
        $targetRoomCount = $roomRequirement['room_count'];

        $validCombos = [];

        // Generate single-room combos
        $validCombos = array_merge($validCombos, $this->generateSingleRoomCombos(
            $rooms,
            $adults,
            $children,
            $totalGuests,
            $nights,
            $hotel,
            $targetRoomCount
        ));

        // Generate two-room combos (different room types)
        $validCombos = array_merge($validCombos, $this->generateTwoRoomCombos(
            $rooms,
            $adults,
            $children,
            $totalGuests,
            $nights,
            $hotel,
            $targetRoomCount
        ));

        //  Sort and find best available combos
        $allCombos = collect($validCombos);
        // Filter combos by meal plan if requested (check at combo level based on selected addons)
        if ($request->meal_plan) {
            $allCombos = $allCombos->filter(function ($combo) use ($request) {
                return $this->comboMatchesMealPlan($combo, $request->meal_plan);
            })->values();
        }

        // Filter combos by free cancellation at hotel level if requested
        // if (!RoomSearch::passesFreeCancellation($hotel, $request)) {
        //     $allCombos = collect();
        // }

        // Primary sort: by rooms booked asc, then optional sort_by
        $allCombos = $allCombos->sortBy(fn($c) => array_sum(array_column($c['rooms'], 'rooms_booked')))->values();

        if ($request->sort_by) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $allCombos = $allCombos->sortBy(fn($c) => $c['new_price'])->values();
                    break;
                case 'price_desc':
                    $allCombos = $allCombos->sortByDesc(fn($c) => $c['new_price'])->values();
                    break;
                case 'rating_asc':
                    $allCombos = $allCombos->sortBy(fn() => $hotel->rating_avg ?? 0)->values();
                    break;
                case 'rating_desc':
                    $allCombos = $allCombos->sortByDesc(fn() => $hotel->rating_avg ?? 0)->values();
                    break;
            }
        } else {
            $allCombos = $allCombos->sortBy(fn($c) => $c['new_price'])->values();
        }

        //  Find exact room match first
        $exactCombos = $allCombos->filter(function ($combo) use ($targetRoomCount) {
            $totalRooms = array_sum(array_column($combo['rooms'], 'rooms_booked'));
            return $targetRoomCount && $totalRooms === $targetRoomCount;
        })->values();

        // If exact requestedRooms match found → return it
        if ($exactCombos->isNotEmpty()) {
            // Mark first combo as recommended if exact match exists
            $exactCombos = $this->markRecommendedCombos($exactCombos, $targetRoomCount, $totalGuests);

            $message = $fitsOriginalRequest
                ? "Exact match found for {$targetRoomCount} room(s)."
                : "Guests cannot fit in {$originalRequestedRooms} room(s). Showing best options for {$targetRoomCount} rooms.";

            return response()->json([
                'message' => $message,
                'hotel_id' => $hotel->id,
                'hotel_name' => $hotel->name,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'adults' => $adults,
                'child' => $children,
                'requested_rooms' => $originalRequestedRooms,
                'serving_rooms' => $targetRoomCount,
                'combos' => $exactCombos,
            ]);
        }

        //  Otherwise, show next best combo
        $nextCombos = collect();
        if ($targetRoomCount > 0) {
            $nextCombos = $allCombos->filter(function ($combo) use ($targetRoomCount) {
                $totalRooms = array_sum(array_column($combo['rooms'], 'rooms_booked'));
                return $totalRooms > $targetRoomCount;
            })->values();
        }

        if ($nextCombos->isNotEmpty()) {
            $minRooms = array_sum(array_column($nextCombos->first()['rooms'], 'rooms_booked'));
            $filteredCombos = $nextCombos->filter(
                fn($c) =>
                array_sum(array_column($c['rooms'], 'rooms_booked')) == $minRooms
            )->values();

            // Mark first combo as recommended
            $filteredCombos = $this->markRecommendedCombos($filteredCombos, $minRooms, $totalGuests);

            $message = $fitsOriginalRequest
                ? "Guests cannot fit in {$targetRoomCount} room(s). Showing available options for {$minRooms} rooms."
                : "Guests cannot fit in {$originalRequestedRooms} room(s). Showing available options for {$minRooms} rooms.";

            return response()->json([
                'message' => $message,
                'hotel_id' => $hotel->id,
                'hotel_name' => $hotel->name,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'adults' => $adults,
                'child' => $children,
                'requested_rooms' => $originalRequestedRooms,
                'serving_rooms' => $minRooms,
                'combos' => $filteredCombos,
            ]);
        }

        //  If no combos found at all
        return response()->json([
            'message' => 'No room combinations available to fit all guests.',
            'hotel_id' => $hotel->id,
            'hotel_name' => $hotel->name,
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'adults' => $adults,
            'child' => $children,
        ]);
    }


    private function buildAddonPackages($rawAddons,  $nights,  $totalGuests,  $roomCount = 1)
    {
        $packages = [[
            'label' => '(Room Only)',
            'addon_names' => [],
            'addon_ids' => [],
            'addon_price_per_room' => 0.0,
        ]];
        $addons = collect($rawAddons ?? [])->map(function ($addon) use ($nights, $totalGuests, $roomCount) {
            $basePrice = (float) ($addon->pivot->price ?? 0);
            $perPerson = (bool) ($addon->pivot->per_person ?? false);
            Log::info('Debug values', [
                'nights'      => $nights,
                'totalGuests' => $totalGuests,
                'roomCount'   => $roomCount,
                'addon'       => $addon,
                'basePrice'   => $basePrice,
                'perPerson'   => $perPerson,
            ]);

            // Calculate price based on per_person flag
            if ($perPerson) {
                // Price per person per night, multiplied by total guests and nights
                $price = $basePrice * $totalGuests * $nights;
            } else {
                // Price per room per night, multiplied by room count and nights
                $price = $basePrice * $roomCount * $nights;
            }

            return [
                'id' => $addon->id,
                'name' => $addon->name,
                'price' => $price,
                'per_person' => $perPerson,
            ];
        })->values();
        $count = $addons->count();
        $totalCombos = 1 << $count;
        for ($mask = 1; $mask < $totalCombos; $mask++) {
            $names = [];
            $ids = [];
            $price = 0.0;

            for ($index = 0; $index < $count; $index++) {
                if ($mask & (1 << $index)) {
                    $addon = $addons[$index];
                    $names[] = $addon['name'];
                    $ids[] = $addon['id'];
                    $price += $addon['price'];
                }
            }

            $packages[] = [
                'label' => '+ ' . implode(' + ', $names),
                'addon_names' => $names,
                'addon_ids' => $ids,
                'addon_price_per_room' => round($price, 2),
            ];
        }

        return $packages;
    }

    private function applyHotelOffer($hotel,  $totalPrice)
    {
        $offer = optional($hotel->hotelsOffer);
        $discountValue = 0.0;
        $discountType = $offer->discount_type ?? 'flat';
        $isOfferValid = false;

        if ($offer && $offer->start_date && $offer->end_date) {
            $today = date('Y-m-d');
            if ($today >= $offer->start_date && $today <= $offer->end_date) {
                $isOfferValid = true;
            }
        }

        if ($isOfferValid) {
            if ($discountType === 'percent') {
                $discountValue = ($totalPrice * ((float) $offer->discount_value)) / 100;
            } elseif ($discountType === 'flat') {
                $discountValue = (float) $offer->discount_value;
            }
        } else {
            $discountType = 'flat';
            $discountValue = 0.0;
        }

        $newPrice = max(0, $totalPrice - $discountValue);
        return [
            'discount_type' => $discountType,
            'discount_value' => (int) round($discountValue),
            'new_price' => (int) round($newPrice),
        ];
    }


    private function generateSingleRoomCombos($rooms,  $adults,  $children,  $totalGuests,  $nights, $hotel,  $requestedRooms)
    {
        $validCombos = [];

        foreach ($rooms as $room) {
            $availableRooms = (int) ($room['available_rooms'] ?? 0);
            if ($availableRooms <= 0) continue;

            $capacity = ($room['max_adults'] ?? 0) + ($room['max_child'] ?? 0);
            if ($capacity <= 0) continue;

            // Calculate minimum rooms needed to fit all guests
            $minRoomsNeeded = ceil($totalGuests / $capacity);

            // Determine room range to check:
            // 1. First try requested rooms (if specified and available)
            // 2. If requested rooms don't fit, increase up to available rooms
            $roomsToCheck = [];

            if ($requestedRooms > 0) {
                // Prioritize requested rooms first
                $requestedRoomsToCheck = min($requestedRooms, $availableRooms);
                if ($requestedRoomsToCheck >= $minRoomsNeeded) {
                    // Requested rooms can fit, prioritize them
                    $roomsToCheck[] = $requestedRoomsToCheck;
                }

                // Also check if we need more rooms (up to requested + 2 or available)
                $maxRoomsToCheck = min($requestedRooms + 2, $availableRooms);
                for ($r = max($minRoomsNeeded, $requestedRoomsToCheck + 1); $r <= $maxRoomsToCheck; $r++) {
                    if (!in_array($r, $roomsToCheck)) {
                        $roomsToCheck[] = $r;
                    }
                }
            } else {
                // No requested rooms, check from minimum needed to available
                for ($r = $minRoomsNeeded; $r <= min($availableRooms, $minRoomsNeeded + 2); $r++) {
                    $roomsToCheck[] = $r;
                }
            }

            // Try each room count
            foreach ($roomsToCheck as $roomCount) {
                $allocation = $this->allocateGuestsToRooms($room, $roomCount, $adults, $children);

                if (!$allocation) continue;

                $totalCapacity = $capacity * $roomCount;

                // Check if all guests fit
                if ($totalGuests <= $totalCapacity && $allocation['remaining_adults'] == 0 && $allocation['remaining_children'] == 0) {
                    $addonPackages = $this->buildAddonPackages($room['raw_addons'], $nights, $totalGuests, $roomCount);
                    $comboBaseLabel = "{$roomCount} × {$room['room_name']}";
                    Log::info($addonPackages);
                    foreach ($addonPackages as $package) {
                        $combo = $this->buildSingleRoomCombo(
                            $room,
                            $roomCount,
                            $comboBaseLabel,
                            $allocation['allocations'],
                            $package,
                            $totalCapacity,
                            $nights,
                            $hotel,
                            $totalGuests
                        );
                        $validCombos[] = $combo;
                    }
                }
            }
        }

        return $validCombos;
    }



    private function generateTwoRoomCombos($rooms,  $adults,  $children,  $totalGuests,  $nights, $hotel,  $requestedRooms)
    {
        $validCombos = [];

        // Only iterate through unique pairs to avoid duplicates
        for ($i = 0; $i < count($rooms); $i++) {
            for ($j = $i + 1; $j < count($rooms); $j++) {
                $room1 = $rooms[$i];
                $room2 = $rooms[$j];

                $capacity1 = ($room1['max_adults'] ?? 0) + ($room1['max_child'] ?? 0);
                $capacity2 = ($room2['max_adults'] ?? 0) + ($room2['max_child'] ?? 0);

                if ($capacity1 <= 0 || $capacity2 <= 0) continue;

                $available1 = (int) ($room1['available_rooms'] ?? 0);
                $available2 = (int) ($room2['available_rooms'] ?? 0);

                if ($available1 <= 0 || $available2 <= 0) continue;

                // Calculate minimum rooms needed
                $minRoomsNeeded = ceil($totalGuests / max($capacity1, $capacity2));

                // Determine room range: prioritize requested rooms, then increase if needed
                $maxRoomsToCheck = $requestedRooms > 0
                    ? min($requestedRooms + 2, $available1 + $available2)
                    : min($available1 + $available2, $minRoomsNeeded + 2);

                for ($count1 = 1; $count1 <= $available1; $count1++) {
                    for ($count2 = 1; $count2 <= $available2; $count2++) {
                        $totalRooms = $count1 + $count2;

                        // Prioritize requested rooms first, then allow up to maxRoomsToCheck
                        if ($totalRooms > $maxRoomsToCheck) continue;

                        // If requested rooms specified, prioritize exact match or slightly above
                        if ($requestedRooms > 0 && $totalRooms > $requestedRooms + 2) continue;

                        $allocation = $this->allocateGuestsToTwoRoomTypes(
                            $room1,
                            $count1,
                            $room2,
                            $count2,
                            $adults,
                            $children
                        );

                        if (!$allocation) continue;

                        $totalCapacity = $capacity1 * $count1 + $capacity2 * $count2;

                        if ($totalGuests <= $totalCapacity && $allocation['remaining_adults'] == 0 && $allocation['remaining_children'] == 0) {
                            // Get addon packages from one room type (prefer room1) and apply to all guests
                            $allAddonPackages = $this->getUnifiedAddonPackages(
                                $room1['raw_addons'],
                                $room2['raw_addons'],
                                $nights,
                                $totalGuests,
                                $totalRooms
                            );

                            // Apply the same addon package to all rooms
                            foreach ($allAddonPackages as $package) {
                                $combo = $this->buildTwoRoomCombo(
                                    $room1,
                                    $count1,
                                    $room2,
                                    $count2,
                                    $allocation['room1_allocations'],
                                    $allocation['room2_allocations'],
                                    $package,
                                    $totalCapacity,
                                    $nights,
                                    $hotel,
                                    $totalGuests,
                                    $totalRooms
                                );
                                $validCombos[] = $combo;
                            }
                        }
                    }
                }
            }
        }

        return $validCombos;
    }



    private function allocateGuestsToRooms($room,  $roomCount,  $adults,  $children)
    {
        $remainingAdults = $adults;
        $remainingChildren = $children;
        $allocations = [];

        for ($r = 1; $r <= $roomCount; $r++) {
            $roomAdults = 0;
            $roomChildren = 0;

            // Fill adults first
            if ($remainingAdults > 0) {
                $roomAdults = min($remainingAdults, $room['max_adults']);
                $remainingAdults -= $roomAdults;
            }

            // Then fill children with capacity logic
            $totalCapacity = $room['max_adults'] + $room['max_child'];
            $availableSlots = $totalCapacity - $roomAdults;
            $roomChildren = min($remainingChildren, $availableSlots);
            $remainingChildren -= $roomChildren;

            $allocations[] = [
                'room_no' => $r,
                'adult' => $roomAdults,
                'child' => $roomChildren,
            ];

            if ($remainingAdults <= 0 && $remainingChildren <= 0) break;
        }

        return [
            'allocations' => $allocations,
            'remaining_adults' => $remainingAdults,
            'remaining_children' => $remainingChildren,
        ];
    }


    private function allocateGuestsToTwoRoomTypes($room1,  $count1, $room2,  $count2,  $adults,  $children)
    {
        $remainingAdults = $adults;
        $remainingChildren = $children;
        $room1Allocations = [];
        $room2Allocations = [];

        // Fill first room type
        for ($r = 1; $r <= $count1; $r++) {
            $roomAdults = min($remainingAdults, $room1['max_adults']);
            $remainingAdults -= $roomAdults;

            $totalCapacity1 = $room1['max_adults'] + $room1['max_child'];
            $availableSlots1 = $totalCapacity1 - $roomAdults;
            $roomChildren = min($remainingChildren, $availableSlots1);
            $remainingChildren -= $roomChildren;

            $room1Allocations[] = [
                'room_no' => $r,
                'adult' => $roomAdults,
                'child' => $roomChildren,
            ];

            if ($remainingAdults <= 0 && $remainingChildren <= 0) break;
        }

        // Fill second room type
        for ($r = 1; $r <= $count2; $r++) {
            $roomAdults = min($remainingAdults, $room2['max_adults']);
            $remainingAdults -= $roomAdults;

            $totalCapacity2 = $room2['max_adults'] + $room2['max_child'];
            $availableSlots2 = $totalCapacity2 - $roomAdults;
            $roomChildren = min($remainingChildren, $availableSlots2);
            $remainingChildren -= $roomChildren;

            $room2Allocations[] = [
                'room_no' => $r,
                'adult' => $roomAdults,
                'child' => $roomChildren,
            ];

            if ($remainingAdults <= 0 && $remainingChildren <= 0) break;
        }

        // Try to fit remaining children if total capacity allows
        if ($remainingChildren > 0) {
            foreach ($room2Allocations as &$alloc) {
                if ($remainingChildren > 0 && $alloc['child'] < $room2['max_child']) {
                    $add = min($remainingChildren, $room2['max_child'] - $alloc['child']);
                    $alloc['child'] += $add;
                    $remainingChildren -= $add;
                }
            }
        }

        return [
            'room1_allocations' => $room1Allocations,
            'room2_allocations' => $room2Allocations,
            'remaining_adults' => $remainingAdults,
            'remaining_children' => $remainingChildren,
        ];
    }



    private function getUnifiedAddonPackages($rawAddons1, $rawAddons2,  $nights,  $totalGuests,  $totalRooms)
    {
        // Prefer addons from room1, fallback to room2 if room1 has none
        // $sourceAddons = !empty($rawAddons1) ? $rawAddons1 : ($rawAddons2 ?? []);
        $sourceAddons = collect($rawAddons1 ?? [])
            ->merge($rawAddons2 ?? [])
            ->unique('id')     // avoid duplicates by addon ID
            ->values()
            ->all();
        // dd($sourceAddons);


        // Get addons from the source room type
        $addons = collect($sourceAddons)->map(function ($addon) {
            return [
                'id' => $addon->id,
                'name' => $addon->name,
                'per_person' => (bool) ($addon->pivot->per_person ?? false),
            ];
        })->values();

        // Build packages starting with "Room Only"
        $packages = [[
            'label' => '(Room Only)',
            'addon_names' => [],
            'addon_ids' => [],
            'source_addons' => $sourceAddons, // Store source for price calculation
        ]];

        // Generate all combinations of addons
        $count = $addons->count();
        $totalCombos = 1 << $count;

        for ($mask = 1; $mask < $totalCombos; $mask++) {
            $names = [];
            $ids = [];

            for ($index = 0; $index < $count; $index++) {
                if ($mask & (1 << $index)) {
                    $addon = $addons[$index];
                    $names[] = $addon['name'];
                    $ids[] = $addon['id'];
                }
            }

            $packages[] = [
                'label' => '+ ' . implode(' + ', $names),
                'addon_names' => $names,
                'addon_ids' => $ids,
                'source_addons' => $sourceAddons, // Store source for price calculation
            ];
        }

        return $packages;
    }



    private function calculateAddonPriceForRoom($rawAddons,  $addonIds,  $nights,  $totalGuests,  $roomCount = 1)
    {
        if (empty($addonIds)) {
            return 0.0;
        }

        $totalPrice = 0.0;
        foreach ($rawAddons ?? [] as $addon) {
            if (in_array($addon->id, $addonIds)) {
                $basePrice = (float) ($addon->pivot->price ?? 0);
                $perPerson = (bool) ($addon->pivot->per_person ?? false);

                // Calculate price based on per_person flag
                if ($perPerson) {
                    // Price per person per night, multiplied by total guests and nights
                    $price = $basePrice * $totalGuests * $nights;
                } else {
                    // Price per room per night, multiplied by room count and nights
                    $price = $basePrice * $roomCount * $nights;
                }

                $totalPrice += $price;
            }
        }

        return round($totalPrice, 2);
    }


    private function buildSingleRoomCombo($room,  $roomCount,  $comboBaseLabel,  $allocations,  $package,  $totalCapacity,  $nights, $hotel,  $totalGuests)
    {
        Log::info($package);
        // Addon price is already calculated in buildAddonPackages based on per_person and total guests
        // For display, we show addon price per room (total addon price divided by room count)
        $addonPricePerRoom = $roomCount > 0 ? round($package['addon_price_per_room'] / $roomCount, 2) : 0;
        $roomPriceWithAddon = $room['price_per_room'] + $addonPricePerRoom;
        $totalPrice = ($room['price_per_room'] * $roomCount) + $package['addon_price_per_room'];
        $oldPricePerNight =  round($totalPrice / max(1, $nights), 2);

        $gst = round($totalPrice * 0.12, 2);
        $pricing = $this->applyHotelOffer($hotel, $totalPrice);
        $newPricePerNight = (int) round($pricing['new_price'] / max(1, $nights));
        $gstPerNight =  (int) round(($pricing['new_price'] * 0.12) / max(1, $nights));
        $comboTitle = trim("{$comboBaseLabel} {$package['label']}");
        return [
            'combo_title' => $comboTitle,
            'total_capacity' => $totalCapacity,
            'rooms' => [[
                'room_type_id' => $room['room_type_id'],
                'room_name' => $comboBaseLabel,
                'rooms_booked' => $roomCount,
                'rooms_capacity' => $allocations,
                'price_per_room' => round($roomPriceWithAddon, 2),
                'bed_type' => $room['bed_type'],
                'photo_url' => $room['photo_url'],
                // 'cancelation_policy' => 'Free Cancellation till 24 hours before Check-in',
                'facilities' => $room['facilities'],
                'selected_addons' => $package['addon_names'],
                'selected_addonsId' => $package['addon_ids'],
                'addon_price_per_room' => round($addonPricePerRoom, 2),
            ]],
            'combo_summary' => "{$comboTitle} + Tax {$gstPerNight} per night",
            'old_price' => round($oldPricePerNight, 2),
            'discount_type' => $pricing['discount_type'],
            'discount_value' => $pricing['discount_value'],
            'new_price' => $newPricePerNight,
        ];
    }



    private function buildTwoRoomCombo($room1,  $count1, $room2,  $count2,  $room1Allocations,  $room2Allocations,  $package,  $totalCapacity,  $nights, $hotel,  $totalGuests,  $totalRooms)
    {
        // Calculate addon price using source addons (from one room type) and apply to all guests
        $sourceAddons = $package['source_addons'] ?? [];
        $totalAddonPrice = $this->calculateAddonPriceForRoom($sourceAddons, $package['addon_ids'] ?? [], $nights, $totalGuests, $totalRooms);

        // Calculate base room prices (without addons)
        $room1BaseTotal = $room1['price_per_room'] * $count1;
        $room2BaseTotal = $room2['price_per_room'] * $count2;

        // For display purposes, distribute addon price proportionally based on room prices
        // This is just for showing per-room breakdown, but the total is calculated correctly
        $totalBasePrice = $room1BaseTotal + $room2BaseTotal;
        if ($totalBasePrice > 0) {
            $room1AddonShare = ($room1BaseTotal / $totalBasePrice) * $totalAddonPrice;
            $room2AddonShare = ($room2BaseTotal / $totalBasePrice) * $totalAddonPrice;
        } else {
            // If both room prices are 0, split addon price evenly
            $room1AddonShare = $totalAddonPrice / 2;
            $room2AddonShare = $totalAddonPrice / 2;
        }

        // Calculate price per room for each room type (for display)
        $room1PricePerRoom = $room1['price_per_room'] + ($count1 > 0 ? round($room1AddonShare / $count1, 2) : 0);
        $room2PricePerRoom = $room2['price_per_room'] + ($count2 > 0 ? round($room2AddonShare / $count2, 2) : 0);

        // Total price = base room prices + addon price (calculated for all guests)
        $totalPrice = $room1BaseTotal + $room2BaseTotal + $totalAddonPrice;
        $gst = round($totalPrice * 0.12, 2);
        $pricing = $this->applyHotelOffer($hotel, $totalPrice);

        $newPricePerNight =  (int) round($pricing['new_price'] / max(1, $nights));
        $gstPerNight = (int) round(($pricing['new_price'] * 0.12) / max(1, $nights));
        $oldPricePerNight =  round(($room1BaseTotal + $room2BaseTotal) / max(1, $nights), 2);
        // Format: (1 × Deluxe Room + 1 × Executive Room) + Lunch
        $room1Title = "{$count1} × {$room1['room_name']}";
        $room2Title = "{$count2} × {$room2['room_name']}";
        $roomsGroup = "({$room1Title} + {$room2Title})";

        // Add addon label once at the end if there are addons
        if (!empty($package['addon_names'])) {
            $comboName = trim("{$roomsGroup} {$package['label']}");
        } else {
            $comboName = $roomsGroup;
        }

        return [
            'combo_title' => $comboName,
            'total_capacity' => $totalCapacity,
            'rooms' => [
                [
                    'room_type_id' => $room1['room_type_id'],
                    'room_name' => "{$count1} × {$room1['room_name']}",
                    'rooms_booked' => $count1,
                    'rooms_capacity' => $room1Allocations,
                    'price_per_room' => round($room1PricePerRoom, 2),
                    'bed_type' => $room1['bed_type'],
                    'photo_url' => $room1['photo_url'],
                    // 'cancelation_policy' => 'Free Cancellation till 24 hours before Check-in',
                    'facilities' => $room1['facilities'],
                    'selected_addons' => $package['addon_names'],
                    'selected_addonsId' => $package['addon_ids'],

                    'addon_price_per_room' => round($room1AddonShare / max($count1, 1), 2),
                ],
                [
                    'room_type_id' => $room2['room_type_id'],
                    'room_name' => "{$count2} × {$room2['room_name']}",
                    'rooms_booked' => $count2,
                    'rooms_capacity' => $room2Allocations,
                    'price_per_room' => round($room2PricePerRoom, 2),
                    'bed_type' => $room2['bed_type'],
                    'photo_url' => $room2['photo_url'],
                    // 'cancelation_policy' => 'Free Cancellation till 24 hours before Check-in',
                    'facilities' => $room2['facilities'],
                    'selected_addons' => $package['addon_names'],
                    'selected_addonsId' => $package['addon_ids'],

                    'addon_price_per_room' => round($room2AddonShare / max($count2, 1), 2),
                ],
            ],
            'combo_summary' => "{$comboName} + Tax {$gstPerNight} per night",
            'old_price' => round($oldPricePerNight, 2),
            'discount_type' => $pricing['discount_type'],
            'discount_value' => $pricing['discount_value'],
            'new_price' => $newPricePerNight,
        ];
    }




    private function comboMatchesMealPlan($combo,  $mealPlan)
    {
        if (!$mealPlan) return true;

        // Get all selected addons from all rooms in the combo
        $allSelectedAddons = [];
        foreach ($combo['rooms'] ?? [] as $room) {
            $selectedAddons = $room['selected_addons'] ?? [];
            foreach ($selectedAddons as $addonName) {
                $allSelectedAddons[] = strtolower($addonName);
            }
        }

        // Remove duplicates
        $allSelectedAddons = array_unique($allSelectedAddons);

        // Helper function to check if addon exists
        $hasAddon = function ($keyword) use ($allSelectedAddons) {
            $keyword = strtolower($keyword);
            foreach ($allSelectedAddons as $addon) {
                if (strpos($addon, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        };

        // Check meal plan requirements
        switch ($mealPlan) {
            case 'RO':
                // Room Only: No meals at all - no breakfast, lunch, dinner, half board, or full board
                return !$hasAddon('breakfast') &&
                    !$hasAddon('lunch') &&
                    !$hasAddon('dinner') &&
                    !$hasAddon('half') &&
                    !$hasAddon('full');

            case 'BB':
                // Bed & Breakfast: Must have breakfast
                return $hasAddon('breakfast');

            case 'HB':
                // Half Board: Must have half board OR (breakfast + lunch/dinner)
                return $hasAddon('half') ||
                    ($hasAddon('breakfast') && ($hasAddon('lunch') || $hasAddon('dinner')));

            case 'FB':
                // Full Board: Must have full board OR (breakfast + lunch + dinner)
                return $hasAddon('full') ||
                    ($hasAddon('breakfast') && $hasAddon('lunch') && $hasAddon('dinner'));
        }

        return true;
    }

    private function assessRoomRequirement($rooms, $requestedRooms, $totalGuests)
    {
        $roomCapacities = [];

        foreach ($rooms as $room) {
            $available = (int) ($room['available_rooms'] ?? 0);
            $capacityPerRoom = (int) ($room['max_adults'] ?? 0) + (int) ($room['max_child'] ?? 0);

            if ($available <= 0 || $capacityPerRoom <= 0) {
                continue;
            }

            for ($i = 0; $i < $available; $i++) {
                $roomCapacities[] = $capacityPerRoom;
            }
        }

        if (empty($roomCapacities)) {
            return [
                'can_fit' => false,
                'fits_requested' => false,
                'room_count' => 0,
            ];
        }

        rsort($roomCapacities);

        $prefixSums = [];
        $running = 0;
        foreach ($roomCapacities as $index => $capacity) {
            $running += $capacity;
            $prefixSums[$index] = $running;
        }

        $totalInventory = count($roomCapacities);
        $cappedRequested = max(1, min($requestedRooms, $totalInventory));
        $fitsRequested = $cappedRequested > 0 && $prefixSums[$cappedRequested - 1] >= $totalGuests;

        if ($fitsRequested) {
            return [
                'can_fit' => true,
                'fits_requested' => true,
                'room_count' => $cappedRequested,
            ];
        }

        $roomCount = $cappedRequested;
        while ($roomCount <= $totalInventory) {
            if ($prefixSums[$roomCount - 1] >= $totalGuests) {
                return [
                    'can_fit' => true,
                    'fits_requested' => false,
                    'room_count' => $roomCount,
                ];
            }
            $roomCount++;
        }

        return [
            'can_fit' => false,
            'fits_requested' => false,
            'room_count' => $totalInventory,
        ];
    }

    private function markRecommendedCombos($combos, $requestedRooms, $totalGuests)
    {
        if ($combos->isEmpty()) {
            return $combos;
        }

        // Find the best combo to recommend
        // Priority: exact room match with best price
        $bestComboIndex = 0;
        $bestScore = null;

        foreach ($combos as $index => $combo) {
            $totalRooms = array_sum(array_column($combo['rooms'], 'rooms_booked'));
            $pricePerPerson = $totalGuests > 0 ? ($combo['new_price'] / $totalGuests) : $combo['new_price'];

            // Calculate score: lower is better
            $score = 0;

            // Exact room match gets highest priority
            if ($requestedRooms > 0 && $totalRooms == $requestedRooms) {
                $score -= 1000000; // High priority for exact match
            }

            // Lower price per person is better
            $score += $pricePerPerson * 1000;

            // Fewer rooms is better (if same price)
            $score += $totalRooms * 10;

            if ($bestScore === null || $score < $bestScore) {
                $bestScore = $score;
                $bestComboIndex = $index;
            }
        }

        // Mark all combos with recommended flag using map
        $markedCombos = $combos->map(function ($combo, $index) use ($bestComboIndex) {
            $combo['recommended'] = ($index === $bestComboIndex);
            return $combo;
        })->values();

        return $markedCombos;
    }
}
