<?php

namespace App\Http\Controllers\Api\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Http\Resources\LocaltyResource;
use App\Http\Resources\SliderResource;
use App\Models\Hotel;
use App\Models\Localty;
use App\Models\Location;
use App\Models\Slider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RoomType;
use Illuminate\Support\Facades\Validator;


class HotelController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            // Fetch top 3 most viewed hotels
            $mostViewedHotels = Hotel::with(['location', 'localities', 'hotelPhotos'])
                ->orderByDesc('count')
                ->limit(15)
                ->get();


            $sliders = Slider::where('is_active', 1)->limit(6)->get();

            $latestHotelsWithOffers = Hotel::with('hotelsOffer')
                ->whereHas('hotelsOffer', function ($query) {
                    $now = Carbon::now();
                    $query->where('status', 1)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                })
                ->latest()
                ->limit(15)
                ->get();

            // dd($latestHotelsWithOffers);


            return response()->json([
                'message' => 'Hotels fetched successfully!',
                'most_view_hotels' => HotelResource::collection($mostViewedHotels),
                'hotels_offers' => HotelResource::collection($latestHotelsWithOffers),
                'sliders' => SliderResource::collection($sliders),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching hotels: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function hotel_location($location = null)
    {
        try {
            // If no location is provided, fetch all with pagination
            if (is_null($location) || $location === '') {
                $hotels_location = Location::paginate(15);
            } else {
                // If location is provided, filter by city (case-insensitive search)
                $hotels_location = Location::where('city', 'LIKE', '%' . $location . '%')->paginate(15);
            }

            // Return response based on result
            if ($hotels_location->count() > 0) {
                return response()->json([
                    'message' => 'Location(s) fetched successfully!',
                    'hotels_location' => $hotels_location
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No locations found.',
                    'hotels_location' => []
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching locations: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    public function locality(Request $request)
    {

        try {
            $localities = Localty::with(['location'])->when(
                filled($request->location_id),
                function ($query) use ($request) {
                    $query->where('location_id', $request->location_id);
                }
            )->get();
            return response()->json([
                'message' => 'Locality fetched successfully!',
                'localities' => LocaltyResource::collection($localities),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching locality: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function hotelDetails(Request $request)
    {
        try {
            $hotelId = $request->hotel_id;

            //  Validate input
            if (!$hotelId) {
                return response()->json(['message' => 'Hotel ID is required.'], 422);
            }

            //  Fetch main hotel details

            $today = now();

            $hotelDetails = Hotel::with([
                'roomTypes',
                'hotelsOffer' => function ($query) use ($today) {
                    $query->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today);
                },
                'hotelPhotos',
                'hotelGuestPhotos',
                'hotelPolicies',
                'facilities.group',
                'hotelReview' => function ($query) {
                    $query->where('is_approved', 1)
                        ->orderBy('rating', 'desc')
                        ->limit(3);
                }
            ])->find($hotelId);

            if (!$hotelDetails) {
                return response()->json([
                    'message' => 'Hotel not found.'
                ], 404);
            }

            //  Optional search parameters for pricing logic
            $checkIn  = $request->filled('check_in') ? Carbon::parse($request->check_in) : now();
            $checkOut = $request->filled('check_out') ? Carbon::parse($request->check_out) : now()->addDay();
            $adults   = $request->adults ?? 2;
            $rooms    = $request->rooms ?? 1;
            $child    = $request->child ?? 0;
            $nights   = max(1, $checkIn->diffInDays($checkOut));
            $totalGuests = max(1, $adults + $child);

            //  Use the same search logic to compute correct pricing (reusable)
            $results = $this->performHotelSearch(
                $hotelDetails->location_id,
                $hotelDetails->locality_id,
                $checkIn,
                $checkOut,
                $adults,
                null,
                null,
                null,
                $rooms,
                $child,
            );
            // dd($results);
            //  Get the pricing for this specific hotel
            // $selectedHotel = $results->firstWhere('hotel_id', $hotelDetails->id);
            $selectedHotel = $results
                ->where('hotel_id', $hotelDetails->id)
                ->sortBy('new_price')
                ->first();

            //  Determine discount info
            // $discountType  = optional($hotelDetails->hotelsOffer)->discount_type;
            // $discountValue = optional($hotelDetails->hotelsOffer)->discount_value;
            $offer = optional($hotelDetails->hotelsOffer);

            $discountType = null;
            $discountValue = 0;
            $isOfferValid = false;

            // Validate offer date range
            if ($offer && $offer->start_date && $offer->end_date) {
                $today = date('Y-m-d');
                if ($today >= $offer->start_date && $today <= $offer->end_date) {
                    $isOfferValid = true;
                }
            }

            // If offer is valid, set type and value
            if ($isOfferValid) {
                $discountType = $offer->discount_type;
                $discountValue = (float) $offer->discount_value;
            } else {
                // No active offer
                $discountType = 'flat';
                $discountValue = 0;
            }



            //  If hotel pricing found — enrich response with it
            if ($selectedHotel) {
                $hotelPricing = [
                    'old_price'     => (int)  $selectedHotel['old_price'],
                    'new_price'     => (int) $selectedHotel['new_price'],
                    // 'lowest_price'  => $selectedHotel['lowest_price'],
                    // 'price_per_night' => $selectedHotel['price_per_night'],
                    // 'price_per_guest_per_night' => $selectedHotel['price_per_guest_per_night'],
                    'Tax'           => (int)  $selectedHotel['Tax'],
                    'nights'        => $selectedHotel['nights'],
                    'guests'        => $selectedHotel['guests'],
                    'combo_summary' => $selectedHotel['combo_summary'],
                    'discount_type'   => $discountType,
                    'discount_value'  => $discountValue,
                ];
            } else {
                $hotelPricing = [
                    'old_price'     => null,
                    'new_price'     => null,
                    // 'lowest_price'  => null,
                    // 'price_per_night' => null,
                    // 'price_per_guest_per_night' => null,
                    'Tax'           => null,
                    'nights'        => $nights,
                    'guests'        => [
                        'adults' => $adults,
                        'children' => $child,
                        'total' => $totalGuests,
                    ],
                    'combo_summary' => null,
                    'discount_type'   => null,
                    'discount_value'  => null,
                ];
            }

            //  Get nearby hotels (with offers)

            $today = now()->toDateString();

            $hotels = Hotel::where('location_id', $hotelDetails->location_id)
                ->where('id', '!=', $hotelDetails->id)
                ->with(['hotelsOffer' => function ($q) use ($today) {
                    $q->where('status', true)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                }])
                ->get();



            //  Format nearby hotels with the same pricing logic (optional)
            $nearbyFormatted = $results->filter(fn($h) => $h['hotel_id'] != $hotelDetails->id)
                ->values()
                ->take(5);

            //  Final response
            return response()->json([
                'message'        => 'Hotel details fetched successfully!',
                'hotel'          => new HotelResource($hotelDetails),
                'pricing'        => $hotelPricing,

                'near_by_hotels' =>  HotelResource::collection($hotels),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching hotel details: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function hotelFacility($id)
    {
        try {
            $hotel = Hotel::with(['facilities.group'])->find($id);

            if (!$hotel) {
                return response()->json([
                    'message' => 'Hotel not found.',
                ], 404);
            }

            // Group facilities by group name
            $groupedFacilities = $hotel->facilities
                ->groupBy(fn($f) => $f->group->group_name ?? 'Ungrouped')
                ->map(function ($group, $groupName) {
                    return [
                        'group_name' => $groupName,
                        'items' => $group->map(fn($f) => [
                            'facility_name' => $f->facility_name,
                            'icon' => $f->icon,
                        ])->values()
                    ];
                })->values();

            return response()->json([
                'message' => 'Hotel facilities fetched successfully!',
                'facilities' => $groupedFacilities
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching facility: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function hotelSearch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'location_id' => 'nullable|integer',
                'locality_id' => 'nullable|integer',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'adults' => 'required|integer|min:1',
                'min_price' => 'nullable|numeric',
                'max_price' => 'nullable|numeric',
                'sort_by' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 422);
            }

            //  Extract parameters
            $locationId = $request->location_id;
            $localityId = $request->locality_id;
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            $adults = $request->adults;
            $minPrice = $request->min_price;
            $maxPrice = $request->max_price;
            $sortBy = $request->sort_by;
            $rooms = $request->rooms ?? 1;
            $child = $request->child ?? 0;

            //  Call reusable search logic
            $results = $this->performHotelSearch($locationId, $localityId, $checkIn, $checkOut, $adults, $minPrice, $maxPrice, $sortBy, $rooms, $child);
            //  Handle empty results
            if ($results->isEmpty()) {
                return response()->json([
                    'check_in'   => $checkIn->toDateString(),
                    'check_out'  => $checkOut->toDateString(),
                    'adults'     => $adults,
                    'data'       => [],
                    'message'    => 'No hotels found.',
                ]);
            }

            return response()->json([
                'check_in'   => $checkIn->toDateString(),
                'check_out'  => $checkOut->toDateString(),
                'adults'     => $adults,
                'data'       => $results,
                'message'    => 'Hotels found successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching hotels: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    private function performHotelSearch(
        $locationId,
        $localityId,
        $checkIn,
        $checkOut,
        $adults,
        $minPrice = null,
        $maxPrice = null,
        $sortBy = null,
        $rooms = 1, //  user can specify how many rooms they want
        $child = 0  //  include child parameter
    ) {
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $totalGuests = max(1, $adults + $child);
        $dates = collect();
        for ($date = $checkIn->copy(); $date->lt($checkOut); $date->addDay()) {
            $dates->push($date->toDateString());
        }
        //  Step 1: Fetch hotels with all relations and inventory coverage
        $hotels = Hotel::with(['location', 'localities', 'hotelsOffer', 'hotelPhotos'])
            ->where('status', 'active')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->when($localityId, fn($q) => $q->where('locality_id', $localityId))
            ->with([
                'roomTypes' => function ($q) use ($dates) {
                    $q->with([
                        'inventories' => fn($inv) => $inv->whereIn('date', $dates)
                            ->where('available_rooms', '>', 0)
                            ->where('is_active', true),
                        'roomPrices' => fn($price) => $price->where(function ($query) use ($dates) {
                            foreach ($dates as $date) {
                                $query->orWhere(function ($q) use ($date) {
                                    $q->where('start_date', '<=', $date)
                                        ->where('end_date', '>=', $date);
                                });
                            }
                        })
                    ]);
                }
            ])
            ->get()->map(function ($hotel) use ($dates, $adults, $child, $rooms) {
                //  Filter only rooms available for the entire stay and with prices for all dates
                $hotel->roomTypes = $hotel->roomTypes->filter(function ($roomType) use ($dates) {
                    // Check inventory for all dates
                    $inventoryDates = $roomType->inventories->pluck('date')->toArray();
                    $allDatesPresent = count(array_intersect($dates->toArray(), $inventoryDates)) === $dates->count();

                    if (!$allDatesPresent) return false;

                    // Check prices for all dates
                    foreach ($dates as $date) {
                        $hasPrice = $roomType->roomPrices->contains(function ($price) use ($date) {
                            return $date >= $price->start_date && $date <= $price->end_date;
                        });
                        if (!$hasPrice) return false;
                    }

                    return true;
                })->values();

                if ($hotel->roomTypes->isEmpty()) return null;

                $combinations = [];
                $remainingGuests = $adults + $child;
                $remainingRooms = max(1, $rooms); // Ensure at least 1 room
                $totalPrice = 0;

                $requiredAdultsPerRoom = $remainingRooms > 0 ? ceil($adults / $remainingRooms) : $adults;
                $requiredChildrenPerRoom = $remainingRooms > 0 ? ceil($child / $remainingRooms) : $child;


                $sortedRoomTypes = $hotel->roomTypes->sortBy(function ($roomType)
                use ($requiredAdultsPerRoom, $requiredChildrenPerRoom) {

                    $isAdultEnough = $roomType->max_guests >= $requiredAdultsPerRoom ? 0 : 1;
                    $isChildEnough = $roomType->max_child >= $requiredChildrenPerRoom ? 0 : 1;

                    return [
                        $isAdultEnough,  // priority 1: must satisfy adult requirement
                        $isChildEnough,  // priority 2: must satisfy child requirement
                        $roomType->roomPrices->avg('base_price') ?? INF // priority 3: cheaper first
                    ];
                });


                // Track remaining adults and children separately for accurate allocation
                $remainingAdults = $adults;
                $remainingChildren = $child;

                // Now assign rooms based on sorted prices
                foreach ($sortedRoomTypes as $roomType) {
                    if (($remainingAdults <= 0 && $remainingChildren <= 0) && $remainingRooms <= 0) break;

                    $maxAdults = $roomType->max_guests;
                    $maxChildren = $roomType->max_child;
                    $capacity = $maxAdults + $maxChildren;
                    if ($capacity <= 0) continue; // Skip rooms with no capacity

                    $availableRooms = $roomType->inventories->pluck('available_rooms')->min();
                    if ($availableRooms <= 0) continue; // Skip rooms with no availability

                    // Calculate how many rooms we can use (up to remaining requested rooms)
                    $maxRoomsToUse = min($availableRooms, $remainingRooms);
                    if ($maxRoomsToUse <= 0 && $remainingRooms > 0) {
                        // If we still have requested rooms but this room type is not available, skip
                        continue;
                    }

                    // Try to fit guests in available rooms
                    $roomsToUse = 0;
                    $adultsFitted = 0;
                    $childrenFitted = 0;

                    for ($r = 1; $r <= $maxRoomsToUse; $r++) {
                        $roomAdults = min($remainingAdults, $maxAdults);
                        $roomChildren = min($remainingChildren, $maxChildren);

                        // Check if we can fit remaining guests in this room
                        if ($roomAdults + $roomChildren > 0) {
                            $roomsToUse = $r;
                            $adultsFitted += $roomAdults;
                            $childrenFitted += $roomChildren;

                            // Update remaining for next iteration
                            $remainingAdults -= $roomAdults;
                            $remainingChildren -= $roomChildren;

                            // If all guests are fitted, we can stop
                            if ($remainingAdults <= 0 && $remainingChildren <= 0) {
                                break;
                            }
                        }
                    }

                    if ($roomsToUse <= 0) continue;

                    //  Calculate price across all nights
                    $comboPrice = 0;
                    $hasPriceForAllDates = true;
                    foreach ($dates as $date) {
                        $priceForDate = $roomType->roomPrices
                            ->first(fn($p) => $date >= $p->start_date && $date <= $p->end_date);
                        if (!$priceForDate) {
                            $hasPriceForAllDates = false;
                            break;
                        }
                        $comboPrice += $priceForDate->base_price * $roomsToUse;
                    }

                    if (!$hasPriceForAllDates) {
                        // Revert the guest allocation if price check fails
                        $remainingAdults += $adultsFitted;
                        $remainingChildren += $childrenFitted;
                        continue;
                    }

                    //  Add combo
                    $combinations[] = [
                        'room_type_id'   => $roomType->id,
                        'room_name'      => $roomType->room_name,
                        'max_guests'     => $roomType->max_guests,
                        'max_child'      => $roomType->max_child,
                        'rooms_booked'   => $roomsToUse,
                        'total_capacity' => ($maxAdults + $maxChildren) * $roomsToUse,
                        'price_per_room' => $roomType->roomPrices->avg('base_price'),
                        'total_price'    => $comboPrice,
                        'inventories'    => $roomType->inventories
                    ];

                    $remainingRooms -= $roomsToUse;
                    $totalPrice += $comboPrice;
                }

                // Update remaining guests for next steps
                $remainingGuests = $remainingAdults + $remainingChildren;

                //  If not all guests covered, try adding more rooms automatically
                // --- Step: Ensure guest coverage ---
                if ($remainingAdults > 0 || $remainingChildren > 0) {
                    foreach ($sortedRoomTypes as $roomType) {
                        if ($remainingAdults <= 0 && $remainingChildren <= 0) break;

                        $maxAdults = $roomType->max_guests;
                        $maxChildren = $roomType->max_child;
                        $capacity = $maxAdults + $maxChildren;
                        if ($capacity <= 0) continue;

                        $availableRooms = $roomType->inventories->pluck('available_rooms')->min();

                        // Check how many rooms of this type are already booked
                        $alreadyBooked = collect($combinations)
                            ->where('room_type_id', $roomType->id)
                            ->sum('rooms_booked');

                        $remainingAvailable = max(0, $availableRooms - $alreadyBooked);
                        if ($remainingAvailable <= 0) continue;

                        // Try to fit remaining guests
                        $extraRoomsNeeded = 0;
                        $adultsFitted = 0;
                        $childrenFitted = 0;
                        $tempRemainingAdults = $remainingAdults;
                        $tempRemainingChildren = $remainingChildren;

                        for ($r = 1; $r <= $remainingAvailable; $r++) {
                            $roomAdults = min($tempRemainingAdults, $maxAdults);
                            $roomChildren = min($tempRemainingChildren, $maxChildren);

                            if ($roomAdults + $roomChildren > 0) {
                                $extraRoomsNeeded = $r;
                                $adultsFitted += $roomAdults;
                                $childrenFitted += $roomChildren;

                                $tempRemainingAdults -= $roomAdults;
                                $tempRemainingChildren -= $roomChildren;

                                if ($tempRemainingAdults <= 0 && $tempRemainingChildren <= 0) {
                                    break;
                                }
                            }
                        }

                        if ($extraRoomsNeeded <= 0) continue;

                        $comboPrice = 0;
                        $hasPriceForAllDates = true;
                        foreach ($dates as $date) {
                            $priceForDate = $roomType->roomPrices
                                ->first(fn($p) => $date >= $p->start_date && $date <= $p->end_date);
                            if (!$priceForDate) {
                                $hasPriceForAllDates = false;
                                break;
                            }
                            $comboPrice += $priceForDate->base_price * $extraRoomsNeeded;
                        }

                        if (!$hasPriceForAllDates) continue;

                        $combinations[] = [
                            'room_type_id'   => $roomType->id,
                            'room_name'      => $roomType->room_name,
                            'max_guests'     => $roomType->max_guests,
                            'max_child'      => $roomType->max_child,
                            'rooms_booked'   => $extraRoomsNeeded,
                            'total_capacity' => $capacity * $extraRoomsNeeded,
                            'price_per_room' => $roomType->roomPrices->avg('base_price'),
                            'total_price'    => $comboPrice,
                            'inventories'    => $roomType->inventories
                        ];

                        $remainingAdults -= $adultsFitted;
                        $remainingChildren -= $childrenFitted;
                        $remainingRooms -= $extraRoomsNeeded;
                        $totalPrice += $comboPrice;
                    }
                }

                // Update remaining guests for final check
                $remainingGuests = $remainingAdults + $remainingChildren;

                // --- Step: Ensure total requested rooms are used ---
                if ($remainingRooms > 0) {
                    // Sort room types by cheapest average price
                    // $sortedRoomTypes = $hotel->roomTypes->sortBy(fn($r) => $r->roomPrices->avg('base_price'));
                    foreach ($sortedRoomTypes as $roomType) {
                        if ($remainingRooms <= 0) break;

                        $availableRooms = $roomType->inventories->pluck('available_rooms')->min();

                        // Check how many rooms of this type are already booked
                        $alreadyBooked = collect($combinations)
                            ->where('room_type_id', $roomType->id)
                            ->sum('rooms_booked');

                        $remainingAvailable = max(0, $availableRooms - $alreadyBooked);
                        if ($remainingAvailable <= 0) continue;

                        $extraRooms = min($remainingAvailable, $remainingRooms);
                        if ($extraRooms <= 0) continue;

                        $comboPrice = 0;
                        $hasPriceForAllDates = true;
                        foreach ($dates as $date) {
                            $priceForDate = $roomType->roomPrices
                                ->first(fn($p) => $date >= $p->start_date && $date <= $p->end_date);
                            if (!$priceForDate) {
                                $hasPriceForAllDates = false;
                                break;
                            }
                            $comboPrice += $priceForDate->base_price * $extraRooms;
                        }

                        if (!$hasPriceForAllDates) continue;

                        $combinations[] = [
                            'room_type_id'   => $roomType->id,
                            'room_name'      => $roomType->room_name,
                            'max_guests'     => $roomType->max_guests,
                            'max_child'      => $roomType->max_child,
                            'rooms_booked'   => $extraRooms,
                            'total_capacity' => ($roomType->max_guests + $roomType->max_child) * $extraRooms,
                            'price_per_room' => $roomType->roomPrices->avg('base_price'),
                            'total_price'    => $comboPrice,
                            'inventories'    => $roomType->inventories
                        ];

                        $remainingRooms -= $extraRooms;
                        $totalPrice += $comboPrice;
                    }
                }
                // Merge same room types
                $combinations = collect($combinations)
                    ->groupBy('room_type_id')
                    ->map(function ($group) {
                        // $first = $group->first();
                        $first = $group->sortBy('price_per_room')->first();
                        $totalRooms = $group->sum('rooms_booked');
                        $totalPrice = $group->sum('total_price');
                        $totalCapacity = $group->sum('total_capacity');
                        return [
                            'room_type_id'   => $first['room_type_id'],
                            'room_name'      => $first['room_name'],
                            'rooms_booked'   => $totalRooms,
                            'total_price'    => $totalPrice,
                            'total_capacity' => $totalCapacity,
                            'price_per_room' => $first['price_per_room'],
                            'inventories'    => $first['inventories'],
                        ];
                    })
                    ->values()
                    ->toArray();


                //  If hotel can host all guests
                if ($remainingGuests <= 0) {
                    $hotel->room_combinations = [
                        'combo_summary'   => collect($combinations)
                            ->map(fn($c) => "{$c['rooms_booked']} × {$c['room_name']}")
                            ->join(' + '),
                        'total_capacity'  => collect($combinations)->sum('total_capacity'),
                        'total_price'     => $totalPrice,
                        'total_rooms'     => collect($combinations)->sum('rooms_booked'),
                        'rooms'           => $combinations,
                    ];
                    return $hotel;
                }

                return null;
            })
            ->filter()
            ->values();



        //  Step 2: Apply filters and sorting
        if ($minPrice || $maxPrice) {
            $hotels = $hotels->filter(function ($hotel) use ($minPrice, $maxPrice) {
                $price = $hotel->room_combinations['total_price'] ?? 0;
                if ($minPrice && $price < $minPrice) return false;
                if ($maxPrice && $price > $maxPrice) return false;
                return true;
            })->values();
        }

        if ($sortBy) {
            if ($sortBy === 'rating_high') {
                $hotels = $hotels->sortByDesc('rating_avg')->values();
            } elseif ($sortBy === '1') {
                $hotels = $hotels->sortBy(fn($h) => $h->room_combinations['total_price'] ?? 0)->values();
            } elseif ($sortBy === '2') {
                $hotels = $hotels->sortByDesc(fn($h) => $h->room_combinations['total_price'] ?? 0)->values();
            }
        }
        //  Step 3: Apply offer and GST
        return $hotels->map(function ($hotel) use ($nights, $totalGuests, $adults, $child) {
            $totalPrice = $hotel->room_combinations['total_price'] ?? 0;
            $isOfferValid = false;
            $offer = $hotel->hotelsOffer;
            // Check if offer is active and dates are valid
            if ($offer && $offer->start_date && $offer->end_date) {
                $today = date('Y-m-d');
                if ($today >= $offer->start_date && $today <= $offer->end_date) {
                    $isOfferValid = true;
                }
            }
            if ($isOfferValid) {
                if ($offer->discount_type === 'percent') {
                    $discountPercent = (float) optional($hotel->hotelsOffer)->discount_value;
                    $discountAmount = ($discountPercent / 100) * $totalPrice;
                    $newPrice = round($totalPrice - $discountAmount, 2);
                    $offerType = "percent";
                    $offerValue = $discountPercent;
                } elseif ($offer->discount_type === 'flat') {
                    $discountAmount = (float) optional($hotel->hotelsOffer)->discount_value;
                    $newPrice = round($totalPrice - $discountAmount, 2);
                    $offerType = "flat";
                    $offerValue = $discountAmount;
                }
            } else {
                $discountAmount = 0;
                $newPrice = $totalPrice;
                $offerType = "flat";
                $offerValue = 0;
            }
            $newPrice = max($newPrice, 0);
            $gst = round($newPrice * 0.12, 2);
            $pricePerNight = (int) round($newPrice / max(1, $nights));
            $gstPerNight = (int)  round(($newPrice * 0.12) / max(1, $nights));
            // $pricePerGuestPerNight = round($newPrice / max(1, $nights * $totalGuests), 2);
            $oldPrice =  round($hotel->room_combinations['total_price'] ?? 0, 2);
            $oldPricePerNight = round($oldPrice / max(1, $nights), 2);

            $guestBreakdown = [
                'adults' => $adults,
                'children' => $child,
                'total' => $totalGuests,
            ];
            $hotelRating = $hotel->hotelReview->where('is_approved', 1)->avg('rating') ?? 0;
            $reviewCount = $hotel->hotelReview()->where('is_approved', 1)->count();
            return [
                'hotel_id'      => $hotel->id,
                'name'          => $hotel->name,
                'address'       => $hotel->address,
                'description'   => $hotel->description,
                'rating'        => round($hotelRating, 1),
                'review_count'  =>  $reviewCount,
                'offer_type'    => $offerType,
                'offer'         => (int) $offerValue,
                'hotel_image'   => optional($hotel->hotelPhotos->first())->photo_url,
                'old_price'     => (int) $oldPricePerNight,
                'new_price'     => (int) $pricePerNight,
                // 'lowest_price'  => $newPrice,
                // 'price_per_night' => $pricePerNight,
                // 'price_per_guest_per_night' => $pricePerGuestPerNight,
                'Tax'           => (int) $gstPerNight,
                'nights'        => $nights,
                'guests'        => $guestBreakdown,
                'locality'      => new LocaltyResource($hotel->localities),
                'combo_summary' => $hotel->room_combinations['combo_summary']
                    ? $hotel->room_combinations['combo_summary'] . " + Tax {$gstPerNight} per night"
                    : null,
            ];
        });
    }




    public function hotel_Offer()
    {

        $latestHotelsWithOffers = Hotel::with('hotelsOffer')
            ->whereHas('hotelsOffer', function ($query) {
                $now = Carbon::now();
                $query->where('status', 1)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            })
            ->latest()
            ->limit(15)
            ->get();

        return response()->json([
            'message' => 'Hotels fetched successfully!',
            'hotels_offers' => HotelResource::collection($latestHotelsWithOffers),
        ]);
    }

    public function hotelPolicy($id)
    {
        $hotel = Hotel::findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Hotel policy details fetched successfully.',
            'data' => [
                'cancellation_policy' => $hotel->hotelPolicies?->cancellation_policy,
                'extra_bed_policy'    => $hotel->hotelPolicies?->extra_bed_policy,
                'child_policy'        => $hotel->hotelPolicies?->child_policy,
            ]
        ], 200);
    }
}
