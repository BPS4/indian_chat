<?php

namespace App\Http\Controllers\Api\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\BookingCancellation;
use App\Models\BookingDiscount;
use App\Models\BookingGuest;
use App\Models\BookingPayment;
use App\Models\BookingRoom;
use App\Models\BookingRoomFacility;
use App\Models\Coupon;
use App\Models\GstDetail;
use App\Models\Hotel;
use App\Models\RoomTypeAddonPrice;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class BookingController extends Controller
{
    // reviewBooking uses the helper
    public function reviewBooking(Request $request)
    {

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|integer|min:1',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'combo' => 'required|array|min:1',
            'combo.*.room_type_id' => 'required|integer|min:1',
            'combo.*.rooms_booked' => 'required|integer|min:1',
            'combo.*.addons_ids' => 'nullable|array',
            'combo.*.addons_ids.*' => 'integer',
            'adults' => 'nullable|integer|min:1',
            'child' => 'nullable|integer|min:0',
            'coupon_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();

            return response()->json([
                'status' => false,
                'message' => $firstError,
            ], 422);
        }

        $hotelId = $request->hotel_id;
        $combo = $request->combo; // [{room_type_id, rooms_booked}]
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $couponCode = $request->coupon_code ?? null;

        $dates = collect();
        $ci = Carbon::parse($checkIn);
        $co = Carbon::parse($checkOut);
        for ($d = $ci->copy(); $d->lt($co); $d->addDay()) {
            $dates->push($d->toDateString());
        }
        // Load hotel with needed relations
        $hotel = Hotel::with([
            'roomTypes.inventories' => function ($q) use ($dates) {
                $q->whereIn('date', $dates);
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
            'roomTypes.addons',
            'hotelPolicies',
            'hotelsOffer',
            'location',
            'localities',
        ])->findOrFail($hotelId);

        //  Calculate base price using server-side tariffs
        $calc = $this->calculateBookingPrice(
            $hotel,
            $combo,
            $checkIn,
            $checkOut,
            (int) ($request->adults ?? 0),
            (int) ($request->child ?? 0)
        );

        if (! $calc['ok']) {
            return response()->json([
                'message' => $calc['error_message'],
            ], 400);
        }

        $priceBreakdown = $calc['price_breakdown'];
        // Calculate hotel discount (offer) if any
        $offer = optional($hotel->hotelsOffer);
        $hotelDiscount = 0.0;
        $isOfferValid = false;

        // Check if offer is active and dates are valid
        if ($offer && $offer->start_date && $offer->end_date) {
            $today = date('Y-m-d');
            if ($today >= $offer->start_date && $today <= $offer->end_date) {
                $isOfferValid = true;
            }
        }

        $baseForDiscount = $priceBreakdown['subtotal'];
        // dd($baseForDiscount);
        if ($isOfferValid) {
            // dd($baseForDiscount ,$offer->discount_type, $offer->discount_value);

            if ($offer->discount_type === 'flat') {
                $hotelDiscount = min((float) $offer->discount_value, $baseForDiscount);
            } elseif ($offer->discount_type === 'percent') {
                $hotelDiscount = round($baseForDiscount * ((float) $offer->discount_value / 100), 2);
            }
        } else {
            $hotelDiscount = 0;
        }
        $taxAfterDiscount = $baseForDiscount - $hotelDiscount;
        $tax = round($taxAfterDiscount * 0.12, 2);
        $priceBreakdown['tax'] = $tax;
        // Optionally apply coupon
        $appliedCoupon = null;
        $couponDiscount = 0.0;
        $couponMessage = null;
        if ($couponCode) {
            $total = $taxAfterDiscount;
            $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->whereDate('valid_from', '<=', now())
                ->whereDate('valid_to', '>=', now())
                ->first();

            if (! $coupon) {
                // If coupon is invalid, ignore and continue with normal review data
                $couponCode = null;
                $couponMessage = 'Invalid or expired coupon code.';
            } else {
                // Check minimum booking amount requirement
                $minAmount = (float) ($coupon->min_booking_amount ?? 0);
                if ($minAmount > 0 && $total < $minAmount) {
                    $couponMessage = 'Minimum booking amount for this coupon is ' . number_format($minAmount, 2);
                } else {
                    $couponDiscount = 0;
                    if ($coupon->discount_type === 'flat') {
                        $couponDiscount = (float) $coupon->discount_value;
                    } elseif ($coupon->discount_type === 'percent') {
                        $couponDiscount = round($total * ((float) $coupon->discount_value / 100), 2);
                    }
                    // Apply max discount cap if configured
                    $maxDiscount = (float) ($coupon->max_discount ?? 0);
                    if ($maxDiscount > 0) {
                        $couponDiscount = min($couponDiscount, $maxDiscount);
                    }
                    // Do not exceed current total
                    $couponDiscount = min($couponDiscount, $total);
                    $couponMessage = 'Coupon applied successfully.';
                    $appliedCoupon = [
                        'code' => $couponCode,
                        'type' => $coupon->discount_type,
                        'value' => (float) $coupon->discount_value,
                        'min_booking_amount' => $minAmount ?: null,
                        'max_discount' => $maxDiscount ?: null,
                    ];
                }
            }
        }

        // Consolidate discounts and payable
        $priceBreakdown['discount'] = round($hotelDiscount + $couponDiscount, 2);
        $priceBreakdown['total'] = round(max(0, $priceBreakdown['subtotal'] - $priceBreakdown['discount']), 2);
        $tax = round($priceBreakdown['total'] * 0.12, 2);
        $priceBreakdown['tax'] = $tax;

        $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        $cancellationPolicy = optional($hotel->hotelPolicies)?->cancellation_policy;
        $adults = (int) ($request->adults ?? 0);
        $children = (int) ($request->child ?? 0);

        // Hotel offer (display only label)
        $offerLabel = $offer ? [
            'discount_type' => $offer->discount_type,
            'discount_value' => (float) $offer->discount_value,
            'label' => $offer->title ?? null,
        ] : null;
        $totalRoomsBooked = collect($calc['rooms_detailed'])->sum('rooms_booked');

        return response()->json([
            'message' => 'Booking review details fetched successfully.',
            'hotel' => [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'image' => optional($hotel->hotelPhotos->first())?->photo_url,
                'cancellation_policy' => $hotel->cancellationPolicy,
                'address' => trim(
                    collect([
                        optional($hotel->localities)?->name,
                        optional($hotel->location)?->city,
                        optional($hotel->location)?->state,
                        optional($hotel->location)?->country,
                        optional($hotel->location)?->zipcode,
                    ])->filter()->implode(', ')
                ),
                // 'offer' => $offerLabel,
            ],
            'itinerary' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights,
                'adults' => $adults ?: null,
                'child' => $children ?: null,
                'total_room' => $totalRoomsBooked,
            ],
            'addons_selected' => collect($calc['rooms_detailed'])
                ->flatMap(function ($room) {
                    return collect($room['addons_selected'])->map(function ($a) {
                        return [
                            'id'          => $a['id'],
                            'name'        => $a['name'],
                            'description' => $a['description'] ?? null,
                        ];
                    });
                })
                ->unique('id')   // ✅ keep only unique addons by ID
                ->values(),



            'price_summary' => [
                // 'base_room_charges' => (int) $priceBreakdown['rooms_subtotal'] ?? (int) $priceBreakdown['subtotal'],
                'base_room_charges' => (int) $priceBreakdown['rooms_subtotal'] + (int) $priceBreakdown['addons_subtotal'],
                'addon_charges' => (int) $priceBreakdown['addons_subtotal'] ?? 0,
                'hotel_discount' => (int) $hotelDiscount,
                'coupon_discount' => (int) $couponDiscount,
                'total_discount' => (int) $priceBreakdown['discount'],
                'sub_total' => (int) round($priceBreakdown['subtotal'] - $priceBreakdown['discount']),
                'taxes' => (int) $priceBreakdown['tax'],
                'total_payable' => (int) round($priceBreakdown['total'] + $priceBreakdown['tax']),
                'currency' => 'INR',
            ],
            'coupon' => $appliedCoupon,
            'coupon_message' => $couponMessage,
            // 'policies' => [
            //     'cancellation' => $cancellationPolicy,
            //     'free_cancellation' => ($cancellationPolicy && stripos($cancellationPolicy, 'free') !== false && stripos($cancellationPolicy, 'cancel') !== false)
            // ],
            // 'payment_options' => [
            //     'pay_now' => true,
            //     'pay_at_hotel' => false
            // ],
        ]);
    }

    private function calculateBookingPrice($hotel, array $combo, $checkIn, $checkOut, $adults = 0, $children = 0): array
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);
        $dates = collect();
        for ($date = $checkIn->copy(); $date->lt($checkOut); $date->addDay()) {
            $dates->push($date->toDateString());
        }

        $nights = $dates->count();
        $subtotal = 0;
        $roomSubtotal = 0;
        $addonsSubtotal = 0;
        $totalGuests = max(1, (int) $adults + (int) $children);
        $roomsDetailed = [];

        foreach ($combo as $room) {
            $roomType = $hotel->roomTypes->firstWhere('id', $room['room_type_id']);
            if (! $roomType) {
                return [
                    'ok' => false,
                    'error_message' => "Room type ID {$room['room_type_id']} not found in this hotel.",
                ];
            }
            // check inventories for requested dates
            $inv = $roomType->inventories
                ->whereIn('date', $dates)
                ->filter(function ($i) use ($room) {
                    // align with search: availability and optionally active
                    if (property_exists($i, 'is_active')) {
                        if (! $i->is_active) {
                            return false;
                        }
                    }

                    return $i->available_rooms >= $room['rooms_booked'];
                });

            $presentDates = $inv->pluck('date')->map(fn($d) => (string) $d)->unique();
            if ($presentDates->count() !== $nights) {
                $missingDates = $dates->diff($presentDates)->implode(', ');

                return [
                    'ok' => false,
                    'error_message' => "Room '{$roomType->room_name}' not available for all nights: {$missingDates}",
                ];
            }

            // calculate per-night price from tariffs
            $perNight = [];
            $totalPerRoom = 0;
            foreach ($dates as $date) {
                $priceForDate = $roomType->roomPrices->first(function ($p) use ($date) {
                    return $date >= $p->start_date && $date <= $p->end_date;
                });
                if (! $priceForDate) {
                    return [
                        'ok' => false,
                        'error_message' => "Price not configured for '{$roomType->room_name}' on {$date}",
                    ];
                }
                $perNight[] = [
                    
                    'date' => $date,
                    'base_price' => (float) $priceForDate->base_price,
                    'currency' => $priceForDate->currency,
                ];
                $totalPerRoom += (float) $priceForDate->base_price;
            }

            $lineRoomSubtotal = $room['rooms_booked'] * $totalPerRoom;

            $addonIds = $room['addons_ids'] ?? ($room['selected_addonsId'] ?? []);
            if (! is_array($addonIds)) {
                $addonIds = [$addonIds];
            }
            $addonIds = array_values(array_filter($addonIds, fn($id) => filled($id)));

            $addonTotal = 0;
            $addonDetails = [];

            if (! empty($addonIds)) {
                $addonPrices = RoomTypeAddonPrice::where('room_type_id', $room['room_type_id'])
                    ->whereIn('addon_id', $addonIds)
                    ->get()
                    ->keyBy('addon_id');

                $addonModels = collect($roomType->addons ?? [])->keyBy('id');

                foreach ($addonIds as $addonId) {
                    if (! $addonPrices->has($addonId)) {
                        continue;
                    }
                    $priceEntry = $addonPrices->get($addonId);
                    $unitPrice = (float) $priceEntry->price;
                    $perPerson = (bool) $priceEntry->per_person;

                    $computedPrice = $perPerson
                        ? $unitPrice * $totalGuests * $nights
                        : $unitPrice * (int) $room['rooms_booked'] * $nights;

                    $addonTotal += $computedPrice;
                    $addonDetails[] = [
                        'id' => $addonId,
                        'name' => optional($addonModels->get($addonId))->name,
                        'description' => optional($addonModels->get($addonId))->description,
                        'per_person' => $perPerson,
                        'unit_price' => $unitPrice,
                        'total_price' => round($computedPrice, 2),
                    ];
                }
            }

            $lineSubtotal = $lineRoomSubtotal + $addonTotal;
            $roomSubtotal += $lineRoomSubtotal;
            $addonsSubtotal += $addonTotal;
            $subtotal += $lineSubtotal;

            $roomsDetailed[] = [
                'room_type_id' => $roomType->id,
                'room_name' => $roomType->room_name,
                'rooms_booked' => (int) $room['rooms_booked'],
                'nights' => $nights,
                'per_night' => $perNight,
                'per_room_total' => round($totalPerRoom, 2),
                'line_room_subtotal' => round($lineRoomSubtotal, 2),
                'addon_total' => round($addonTotal, 2),
                'line_subtotal' => round($lineSubtotal, 2),
                'bed_type' => $roomType->bed_type,
                'addons_selected' => $addonDetails,
                'addons_included' => collect($roomType->addons)->filter(function ($a) {
                    return (float) ($a->pivot->price ?? 0) == 0.0;
                })->map(fn($a) => $a->name)->values(),
            ];
        }

        $tax = round(($subtotal) * 0.12, 2);
        $total = $subtotal + $tax;
        return [
            'ok' => true,
            'price_breakdown' => [
                'rooms_subtotal' => round($roomSubtotal, 2),
                'addons_subtotal' => round($addonsSubtotal, 2),
                'subtotal' => round(($subtotal), 2),
                'tax' => $tax,
                'discount' => 0,
                'total' => (int) round(($total)),
            ],
            'rooms_detailed' => $roomsDetailed,
        ];
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|integer|exists:hotels,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'child' => 'nullable|integer|min:0',

            'combo' => 'required|array|min:1',
            'combo.*.room_type_id' => 'required|integer|exists:room_types,id',
            'combo.*.rooms_booked' => 'required|integer|min:1',

            // 'name' => 'required|string|max:255',
            // 'email' => 'required|email',
            // 'phone' => 'required|max:15',

            // 'is_primary' => 'required|boolean',

            'guest' => 'nullable|array',
            'guest.*.name' => 'required_with:guest|string|max:255',
            'guest.*.email' => 'nullable',

            'is_gst' => 'nullable|boolean',
            'gst_no' => 'nullable|required_if:is_gst,true|string',
            'company_name' => 'nullable|required_if:is_gst,true|string',
            'address' => 'nullable|required_if:is_gst,true|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $hotelId = $request->hotel_id;
        $combo = $request->combo;
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $adults = $request->adults;
        $child = $request->child ?? 0;
        $couponCode = $request->coupon_code;
        $dates = collect();
        $ci = Carbon::parse($checkIn);
        $co = Carbon::parse($checkOut);
        for ($d = $ci->copy(); $d->lt($co); $d->addDay()) {
            $dates->push($d->toDateString());
        }
        $hotel = Hotel::with([
            'roomTypes.inventories' => function ($q) use ($dates) {
                $q->whereIn('date', $dates);
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
            'roomTypes.addons',
            // 'hotelPolicies',
            'hotelsOffer',
            // // 'location',
            // 'localities'
        ])->findOrFail($hotelId);
        $price = $this->calculateBookingPrice($hotel, $combo, $checkIn, $checkOut, $adults);
        $finalPrice = $this->calculateFinalPrice($price, $hotel, $couponCode);

        $base_room_charges = $price['price_breakdown']['subtotal'];
        $tax = $finalPrice['tax'];
        // $tax = $price['price_breakdown']['tax'];

        // dd($finalPrice['total'],$request->payable_amount);
        Log::info($finalPrice);
        Log::info($request->payable_amount);
        if ((int) round($finalPrice['total']) !== (int) round($request->input('payable_amount'))) {

            return response()->json(['message' => 'Amount did not match , Something went wrong'], 422);
        }

        // dd($request->guest);
        if ($request->created_by == 'Admin') {

            $user_id = $request->guest[0]['id'];
        } else {
            $user = Auth::user();
            if ($user) {
                $user_id = $user->id;
            }
        }

        try {
            DB::beginTransaction();

            $bookingId = $this->generateUniqueBookingId();

            // dd($bookingId);

            // Create booking
            $booking = Booking::create([
                'booking_id' => $bookingId,
                'user_id' => $user_id,
                'hotel_id' => $hotelId,
                'checkin_date' => $checkIn,          // Correct date format as string
                'checkout_date' => $checkOut,
                'total_nights' => count($dates),
                'total_guests' => $adults + $child,

                'base_room_charges' => $base_room_charges,
                'taxes' => $tax,
                'hotel_discount' => $finalPrice['hotel_discount'],
                'coupon_discount' => $finalPrice['coupon_discount'],
                'total_payable' => $finalPrice['total'],
                'status' => ($request->created_by == 'Admin') ? 'confirmed' : 'pending',

                'created_by' => ($request->created_by == 'Admin') ? 'Admin' : 'User',
            ]);
            // Rooms
            if (! empty($request->combo) && ! empty($price['rooms_detailed'])) {

                foreach ($request->combo as $comboRoom) {
                    $room_type_id = isset($comboRoom['room_type_id']) ? $comboRoom['room_type_id'] : null;
                    $quantity = isset($comboRoom['rooms_booked']) ? $comboRoom['rooms_booked'] : 0;

                    // Find matching room in $price['rooms_detailed']
                    $matchedRoom = collect($price['rooms_detailed'])
                        ->firstWhere('room_type_id', $room_type_id);

                    // Get per_room_total safely
                    $per_room_total = isset($matchedRoom['per_room_total']) ? $matchedRoom['per_room_total'] : 0;

                    // Save to BookingRoom
                    if ($room_type_id) {
                        $bookingRooms = BookingRoom::create([
                            'booking_id' => $booking->id,
                            'room_type_id' => $room_type_id,
                            'quantity' => $quantity,
                            'price_per_room' => $per_room_total,
                            'subtotal' => $quantity * $per_room_total,
                        ]);
                    }
                    if ($bookingRooms->roomType && $bookingRooms->roomType->facilities) {
                        foreach ($bookingRooms->roomType->facilities as $facility) {

                            BookingRoomFacility::create([
                                'booking_id' => $booking->id,
                                'booking_room_type_id' => $bookingRooms->id,
                                'icon' => $facility->icon,
                                'title' => $facility->facility_name,
                            ]);
                        }
                    }
                    $rawAddonIds = $comboRoom['addons_ids'] ?? [];
                    if (! is_array($rawAddonIds)) {
                        $rawAddonIds = $rawAddonIds === null ? [] : [$rawAddonIds];
                    }
                    // Remove empty values
                    $addonIds = array_values(array_filter($rawAddonIds, fn($id) => filled($id)));

                    if (! empty($addonIds)) {
                        // Load addon prices for this room_type and these addon ids
                        $addonPrices = RoomTypeAddonPrice::where('room_type_id', $room_type_id)
                            ->whereIn('addon_id', $addonIds)
                            ->get()
                            ->keyBy('addon_id');

                        // Load addon meta (name/description) directly from Addon model to avoid relying on $roomType->addons
                        $addonModels = \App\Models\Addon::whereIn('id', $addonIds)->get()->keyBy('id');

                        $addonDetails = [];
                        $nights = count($dates);
                        $guestsCount = max(count($request->guest ?? []), 1); // at least 1 guest
                        $roomsCount = max($quantity, 1);

                        foreach ($addonIds as $addonId) {
                            if (! $addonPrices->has($addonId)) {
                                continue; // price not available for this room type
                            }

                            $priceEntry = $addonPrices->get($addonId);
                            $unitPrice = (float) $priceEntry->price;
                            $perPerson = (bool) $priceEntry->per_person;

                            // Determine quantity and computed price
                            if ($perPerson) {
                                $computedQuantity = $guestsCount;
                                $computedPrice = $unitPrice * $computedQuantity * $nights;
                            } else {
                                $computedQuantity = $roomsCount;
                                $computedPrice = $unitPrice * $computedQuantity * $nights;
                            }

                            $addonDetails[] = [
                                'id' => $addonId,
                                'name' => optional($addonModels->get($addonId))->name ?? null,
                                'description' => optional($addonModels->get($addonId))->description ?? null,
                                'per_person' => $perPerson,
                                'unit_price' => $unitPrice,
                                'quantity' => $computedQuantity,
                                'total_price' => round($computedPrice, 2),
                            ];
                        }

                        // Persist addons to BookingAddon
                        foreach ($addonDetails as $addonDetail) {
                            BookingAddon::create([
                                'booking_id' => $booking->id,
                                // 'booking_room_type_id' => $bookingRooms ? $bookingRooms->id : null,
                                // 'addon_id' => $addonDetail['id'],
                                'addon_name' => $addonDetail['name'] ?? '',
                                // 'description' => $addonDetail['description'] ?? null,
                                // 'per_person' => $addonDetail['per_person'],
                                'addon_price' => $addonDetail['unit_price'],
                                'quantity' => $addonDetail['quantity'],
                                // 'total_price' => $addonDetail['total_price'],
                            ]);
                        }
                    }
                }
            }

            // discount
            if ($finalPrice['offer_id'] || $finalPrice['coupon_id']) {
                BookingDiscount::create([
                    'booking_id' => $booking->id,
                    'coupon_id' => $finalPrice['coupon_id'],
                    'offer_id' => $finalPrice['offer_id'],
                    'discount_amount' => $finalPrice['coupon_discount'] + $finalPrice['hotel_discount'],
                ]);
            }

            // Guests
            if (! empty($request->guest) && is_array($request->guest)) {
                foreach ($request->guest as $guest) {
                    $guest_name = isset($guest['name']) ? $guest['name'] : null;
                    $email = isset($guest['email']) ? $guest['email'] : null;
                    $mobile = isset($guest['phone']) ? $guest['phone'] : null;
                    $aadhar_no = isset($guest['aadhar_no']) ? $guest['aadhar_no'] : null;
                    $is_primary = isset($guest['is_primary']) ? (bool) $guest['is_primary'] : false;

                    BookingGuest::create([
                        'booking_id' => $booking->id,
                        'guest_name' => $guest_name,
                        'email' => $email,
                        'mobile' => $mobile,
                        'aadhar_no' => $aadhar_no,
                        'is_primary' => $is_primary,
                    ]);
                }
            }

            //  ✅ Check if booking was created
            if (! $booking || ! $booking->id) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Booking creation failed',
                    'error' => 'Booking object is null or invalid',
                ], 500);
            }

            // Payment
            BookingPayment::create([
                'booking_id' => $bookingId,
                'payment_method' => ($request->created_by == 'Admin') ? 'Cash' : 'Online',
                'transaction_id' => '',
                'amount' => $finalPrice['total'],
                'currency' => 'INR',
                'payment_status' => ($request->created_by == 'Admin') ? '1' : '0',
                'payment_date' => Carbon::now(),
            ]);

            // // GST
            if ($request->is_gst) {
                GstDetail::create([
                    'booking_id' => $booking->id,
                    'gst_no' => $request->gst_no ?? null,
                    'company_name' => $request->company_name ?? null,
                    'address' => $request->address ?? null,
                ]);
            }

            // // Addons
            // if (!empty($validated['addons'])) {
            //     foreach ($validated['addons'] as $addon) {
            //         BookingAddon::create([
            //             'booking_id' => $booking->booking_id,
            //             'addon_name' => $addon['addon_name'],
            //             'addon_price' => $addon['addon_price'],
            //             'quantity' => $addon['quantity'],
            //         ]);
            //     }
            //

            DB::commit();

            // Step 5: Create Razorpay Order
            $razorpayKey = env('RAZORPAY_KEY');
            $razorpaySecret = env('RAZORPAY_SECRET');

            $authHeader = 'Basic ' . base64_encode("$razorpayKey:$razorpaySecret");

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => (int) $finalPrice['total'] * 100, // Razorpay takes amount in paise
                    'currency' => 'INR',
                    'receipt' => $bookingId,
                    'notes' => [
                        'booking_id' => $bookingId,
                        'user_id' => $user_id,
                    ],
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    "Authorization: $authHeader",
                ],
            ]);

            $razorpayResponse = curl_exec($curl);
            curl_close($curl);

            // dd($razorpayResponse);

            $razorpayOrder = json_decode($razorpayResponse, true);

            if (! isset($razorpayOrder['id'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create Razorpay order',
                    'razorpay_response' => $razorpayOrder,
                ], 500);
            }

            // ✅ Save Booking Payment with razorpay_order_id
            BookingPayment::where('booking_id', $bookingId)->update([
                'razorpay_order_id' => $razorpayOrder['id'],
                'payment_date' => now()->toDateString(),
            ]);

            return response()->json([
                'message' => 'Booking created successfully',
                'booking_id' => $booking->booking_id,
                'amount' => (int) $finalPrice['total'],
                'razorpay_order_id' => $razorpayOrder['id'] ?? null,
                'razorpay_data' => $razorpayOrder,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Booking creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function calculateFinalPrice($calc, $hotel, $couponCode)
    {

        if (! $calc['ok']) {
            return response()->json([
                'message' => $calc['error_message'],
            ], 400);
        }
        $priceBreakdown = $calc['price_breakdown'];
        // Calculate hotel discount (offer) if any
        $offer = optional($hotel->hotelsOffer);
        $hotelDiscount = 0.0;

        $isOfferValid = false;

        // Check if offer is active and dates are valid
        if ($offer && $offer->start_date && $offer->end_date) {
            $today = date('Y-m-d');
            if ($today >= $offer->start_date && $today <= $offer->end_date) {
                $isOfferValid = true;
            }
        }
        $baseForDiscount = $priceBreakdown['subtotal'];
        if ($isOfferValid) {
            if ($offer->discount_type === 'flat') {
                $hotelDiscount = min((float) $offer->discount_value, $baseForDiscount);
            } elseif ($offer->discount_type === 'percent') {
                $hotelDiscount = round($baseForDiscount * ((float) $offer->discount_value / 100), 2);
            }
        } else {
            $hotelDiscount = 0;
        }

        $sellPrice = $baseForDiscount - $hotelDiscount;
        // Optionally apply coupon
        $appliedCoupon = null;
        $couponDiscount = 0.0;
        $couponMessage = null;
        if ($couponCode) {
            $total = $sellPrice;
            $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->whereDate('valid_from', '<=', now())
                ->whereDate('valid_to', '>=', now())
                ->first();

            if (! $coupon) {
                // If coupon is invalid, ignore and continue with normal review data
                $couponCode = null;
                $couponMessage = 'Invalid or expired coupon code.';
            } else {
                // Check minimum booking amount requirement
                $minAmount = (float) ($coupon->min_booking_amount ?? 0);
                if ($minAmount > 0 && $total < $minAmount) {
                    $couponMessage = 'Minimum booking amount for this coupon is ' . number_format($minAmount, 2);
                } else {
                    $couponDiscount = 0;
                    if ($coupon->discount_type === 'flat') {
                        $couponDiscount = (float) $coupon->discount_value;
                    } elseif ($coupon->discount_type === 'percent') {
                        $couponDiscount = round($total * ((float) $coupon->discount_value / 100), 2);
                    }
                    // Apply max discount cap if configured
                    $maxDiscount = (float) ($coupon->max_discount ?? 0);
                    if ($maxDiscount > 0) {
                        $couponDiscount = min($couponDiscount, $maxDiscount);
                    }
                    // Do not exceed current total
                    $couponDiscount = min($couponDiscount, $total);
                    $couponMessage = 'Coupon applied successfully.';
                    $appliedCoupon = [
                        'code' => $couponCode,
                        'type' => $coupon->discount_type,
                        'value' => (float) $coupon->discount_value,
                        'min_booking_amount' => $minAmount ?: null,
                        'max_discount' => $maxDiscount ?: null,
                    ];
                }
            }
        }

        // Consolidate discounts and payable
        $priceBreakdown['discount'] = round($hotelDiscount + $couponDiscount, 2);
        $priceBreakdown['total'] = round(max(0, $priceBreakdown['subtotal'] - $priceBreakdown['discount']), 2);
        $tax = round($priceBreakdown['total'] * 0.12, 2);
        $payableAmount = round($priceBreakdown['total'] + $tax, 2);

        return [
            'total' => (int) round($payableAmount),
            'offer_id' => $offer ? $offer->offer_id : null,
            'hotel_discount' => $hotelDiscount,
            'coupon_id' => $couponCode && $coupon ? $coupon->coupon_id : null,
            'coupon_discount' => $couponDiscount,
            'tax' => $tax,
        ];
    }

    private function generateUniqueBookingId(): string
    {
        do {
            // Generate a random 5-digit number
            $randomNumber = random_int(1000000, 9999999);

            // Prefix with 'MARKS'
            $bookingId = 'MARKS' . $randomNumber;

            // Check if the ID already exists in the database
            $exists = \App\Models\Booking::where('booking_id', $bookingId)->exists();
        } while ($exists); // Repeat if not unique

        return $bookingId;
    }

    public function final_payments(Request $request, $order_id)
    {
        try {
            // $order = Order::with('tourPackage', 'schedule')->where('order_id', $order_id)->orderBy('id', 'desc')->first();

            $order = $order_id;
            $payment = 125;

            return view('payment.finalpayment', compact('order', 'payment'));
        } catch (\Exception $e) {
            dd($e);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function booking_confirmation(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $razorpayKey = env('RAZORPAY_KEY');
        $razorpaySecret = env('RAZORPAY_SECRET');

        try {
            $api = new Api($razorpayKey, $razorpaySecret);

            // Fetch the payments for the given order_id
            $payments = $api->order->fetch($request->order_id)->payments();

            $items = $payments['items'];

            if (count($payments['items']) === 0) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'No payments found for this order ID.',
                ], 404);
            }

            // Get the latest payment
            $latestPayment = $items[count($items) - 1];

            // dd($latestPayment);

            // Update your local database
            $order = BookingPayment::where('razorpay_order_id', $request->order_id)->first();

            if (! $order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found in local database.',
                ], 404);
            }

            // If payment is successful, update order
            if ($latestPayment['status'] === 'captured') {
                $order->payment_status = '1';
                $order->transaction_id = $latestPayment['id'];
                $order->save();
            }

            return response()->json([
                'status' => 'success',
                'booking_id' => $order->booking_id,
                'payment_status' => $latestPayment['status'],
                'payment_id' => $latestPayment['id'],
                'amount' => $latestPayment['amount'],
                'method' => $latestPayment['method'],
                'created_at' => date('Y-m-d H:i:s', $latestPayment['created_at']),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        // 🔹 Log raw incoming request
        Log::info('Razorpay Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $payload,
            'webhookSecret' => $webhookSecret,
        ]);

        // Step 1: Verify webhook signature
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        if ($signature !== $expectedSignature) {
            Log::warning('Razorpay Webhook: Invalid signature', [
                'received_signature' => $signature,
                'expected_signature' => $expectedSignature,
            ]);

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);

        if (! $data || ! isset($data['event'])) {
            Log::error('Razorpay Webhook: Malformed payload', ['payload' => $payload]);

            return response()->json(['message' => 'Malformed payload'], 400);
        }

        $event = $data['event'];
        Log::info('Razorpay Webhook: Event detected', ['event' => $event]);

        try {
            if ($event === 'payment.captured') {
                $paymentId = $data['payload']['payment']['entity']['id'] ?? null;
                $orderId = $data['payload']['payment']['entity']['order_id'] ?? null;
                $amount = $data['payload']['payment']['entity']['amount'] ?? null;
                $paymentMethod = $data['payload']['payment']['entity']['method'] ?? null;
                $currency = $data['payload']['payment']['entity']['currency'] ?? null;
                $paymentStatus = $data['payload']['payment']['entity']['status'] ?? null;

                Log::info('Razorpay Payment Captured', [
                    'payment_id' => $paymentId,
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'currency' => $currency,
                    'payment_status' => $paymentStatus,
                ]);

                if (! $orderId) {
                    Log::warning('Razorpay Webhook: Missing order_id in payment.captured');

                    return response()->json(['message' => 'Missing order_id'], 400);
                }

                $bookingPayment = BookingPayment::where('razorpay_order_id', $orderId)->first();

                if ($bookingPayment) {
                    $transactionDetails = [
                        'payment_method' => $paymentMethod,
                        'currency' => $currency,
                        'amount' => $amount / 100, // Razorpay amount is in paise, convert to rupees or your currency
                        'payment_status' => $paymentStatus,
                        'payment_id' => $paymentId,
                    ];
                    $bookingPayment->update([
                        'payment_status' => '1',
                        'transaction_id' => $paymentId,
                        'transaction_detail' => json_encode($transactionDetails),
                        'payment_date' => now(),
                    ]);

                    Log::info('BookingPayment updated successfully', [
                        'booking_payment_id' => $bookingPayment->id,
                    ]);

                    // Optionally update booking status
                    if ($bookingPayment->booking) {
                        $bookingPayment->booking()->update(['status' => 'confirmed']);
                        Log::info('Booking status updated to confirmed', [
                            'booking_id' => $bookingPayment->booking->id,
                        ]);
                    }
                } else {
                    Log::warning('BookingPayment not found for Razorpay order_id', ['order_id' => $orderId]);
                }
            } elseif ($event === 'payment.failed') {
                $orderId = $data['payload']['payment']['entity']['order_id'] ?? null;
                $failureReason = $data['payload']['payment']['entity']['failure_reason'] ?? null;

                Log::info('Booking status failed', [
                    'order_id' => $orderId,
                    'failure_reason' => $failureReason,
                ]);
                if ($orderId) {
                    $transactionDetails = [
                        'failure_reason' => $failureReason,
                        'order_id' => $orderId,
                    ];
                    BookingPayment::where('razorpay_order_id', $orderId)->update([
                        'payment_status' => '0',
                        'transaction_detail' => json_encode($transactionDetails), // Store failure details as JSON
                    ]);
                    Log::info('Payment failed updated in DB', ['order_id' => $orderId]);
                } else {
                    Log::warning('Payment failed event missing order_id', ['data' => $data]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Razorpay Webhook Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Server error'], 500);
        }

        Log::info('Razorpay Webhook handled successfully', ['event' => $event]);

        return response()->json(['status' => 'success']);
    }

    public function all_bookings(Request $request)
    {
        $user = Auth::user();
        $filter = strtolower($request->query('filter', ''));
        $today = now()->toDateString();

        $query = Booking::with(['hotel.location', 'hotel.localities', 'hotel.hotelPhotos', 'addons'])
            ->where('user_id', $user->id)
            ->latest();
        // Apply filter
        switch ($filter) {
            case 'past':
                $query->whereDate('checkout_date', '<=', $today);
                break;
            case 'current':
                $query->whereDate('checkin_date', '<=', $today)
                    ->whereDate('checkout_date', '>', $today);
                break;
            case 'upcoming':
                $query->whereDate('checkin_date', '>', $today);
                break;
        }

        $perPage = $request->query('per_page');

        if ($perPage) {
            $bookings = $query->paginate($perPage);
            $bookings->getCollection()->transform(fn($booking) => $this->transformBooking($booking));
        } else {
            $allBookings = $query->get()->map(fn($booking) => $this->transformBooking($booking));

            // Wrap collection in a "pseudo-paginator" structure
            $bookings = [
                'current_page' => 1,
                'per_page' => $allBookings->count(),
                'total' => $allBookings->count(),
                'last_page' => 1,
                'data' => $allBookings,
            ];
        }

        return response()->json([
            'message' => 'All Bookings',
            'data' => $bookings,
        ], 200);
    }

    private function transformBooking($booking)
    {
        return [
            'id' => $booking->id,
            'user_id' => $booking->user_id,
            'hotel_id' => $booking->hotel_id,
            'checkin_date' => $booking->checkin_date,
            'checkin_time' => \Carbon\Carbon::parse($booking->checkin_time)->format('h:i A'),
            'checkout_date' => $booking->checkout_date,
            'checkout_time' => \Carbon\Carbon::parse($booking->checkout_time)->format('h:i A'),
            'total_nights' => $booking->total_nights,
            'total_guests' => $booking->total_guests,
            'status' => $booking->status,
            'hotel_name' => $booking->hotel?->name ?? null,
            'image' => $booking->hotel?->hotelPhotos?->first()?->photo_url ?? null,
            'address' => collect([
                $booking->hotel?->localities?->name,
                $booking->hotel?->location?->city,
                $booking->hotel?->location?->state,
                $booking->hotel?->location?->country,
                $booking->hotel?->location?->zipcode,
            ])->filter()->implode(', '),
            // 'addons' => $booking->addons->map(function ($addon) {
            //     return [
            //         'addon_id' => $addon->id,
            //         'addon_name' => $addon->addon_name,
            //         'addon_price' => $addon->addon_price,
            //         'quantity' => $addon->quantity,
            //     ];
            // }),
        ];
    }

    // public function booking_details($id)
    // {
    //     $booking = Booking::with('rooms', 'guests', 'payment', 'gst', 'addons')->where('id', $id)->get();

    //     return response()->json([
    //         'message' => 'Booking Details',
    //         'data' => $booking
    //     ], 200);
    // }

    public function booking_details($id)
    {
        $booking = Booking::with(
            'rooms.roomType',
            'guests',
            'payment',
            'gst',
            'addons',
            'reviews',
            'hotel.location',
            'hotel.localities'
        )
            ->where('id', $id)
            ->first();

        if (! $booking) {
            return response()->json([
                'message' => 'Booking not found',
                'data' => null,
            ], 404);
        }

        // --- Build Room Combo Details ---
        $roomsData = [];
        $comboSummaryParts = [];
        $comboKeyParts = [];
        $totalCapacity = 0;
        $totalPrice = 0;
        $totalBookedRoom = 0;

        foreach ($booking->rooms as $room) {
            $roomType = $room->roomType;
            $roomsData[] = [
                'room_type_id' => $room->room_type_id,

                'room_name' => $roomType->room_name ?? 'N/A',
                'rooms_booked' => $room->quantity,
                'price_per_room' => (float) $room->price_per_room,
                'bed_type' => $roomType->bed_type ?? 'Double',
                'photo_url' => $roomType->photo_url ?? 'images/rooms/rooms_image.png',
                'facilities' => $room->roomFacilities ?? null,
            ];

            $comboSummaryParts[] = "{$room->quantity} × {$roomType->room_name}";
            $comboKeyParts[] = "{$room->quantity} × {$roomType->room_name}";
            $totalCapacity += ($roomType->max_guests ?? 2) * $room->quantity;
            $totalPrice += $room->subtotal;
            $totalBookedRoom += $room->quantity;
        }

        $roomCombo = [
            'combo_key' => implode(' + ', $comboKeyParts),
            'combo_title' => implode(' + ', $comboSummaryParts),
            'total_capacity' => $totalCapacity,
            'total_price' => round($totalPrice, 2),
            'total_booked_room' => $totalBookedRoom,
            'rooms' => $roomsData,
        ];

        // --- Build Addon Summary ---
        $addonsList = [];

        foreach ($booking->addons as $addon) {
            $addonsList[] = [
                'addon_id' => $addon->id,
                'addon_name' => $addon->addon_name,
                'addon_price' => (float) $addon->addon_price,
                'quantity' => (int) $addon->quantity,
                'total_price' => round($addon->addon_price * $addon->quantity, 2),
            ];
        }

        $addonSummary = [
            'total_addons' => count($addonsList),
            'addons' => $addonsList,
        ];


        // --- Build Price Summary ---
        $priceSummary = [
            'base_room_charges' => (float) $booking->base_room_charges,
            'taxes' => (float) $booking->taxes,
            'hotel_discount' => (float) $booking->hotel_discount,
            'coupon_discount' => (float) $booking->coupon_discount,
            'total_payable' => (float) $booking->total_payable,
        ];

        $review_summary = null; // ✅ define it first, always

        if ($booking->reviews && count($booking->reviews) > 0) {
            $review_summary = []; // initialize as empty array if reviews exist
            $primaryGuest = $booking->guests?->where('is_primary', 1)->first();
            foreach ($booking->reviews as $review) {
                $review_summary = [
                    'hotel' => $review->hotel?->name,
                    'user' => $primaryGuest->guest_name ?? null,
                    // 'user' => $review->user?->name,
                    'rating' => $review->rating ?? 'N/A',
                    'review' => $review->review,
                    'image' => $review->image,
                    'created_at' => $review->created_at,
                    'reply' => $review->reply,
                    'is_approved' => $review->is_approved,
                ];
            }

            // If no approved reviews found, you can set it to null or keep empty
            if (empty($review_summary)) {
                $review_summary = null;
            }
        }

        // --- Convert Booking to Array ---
        $bookingData = $booking->toArray();
        // $bookingData = $this->transformBooking($booking);

        // Remove unwanted keys
        unset(
            $bookingData['reviews'],
            $bookingData['hotel'],
            $bookingData['rooms'],
            $bookingData['base_room_charges'],
            $bookingData['taxes'],
            $bookingData['hotel_discount'],
            $bookingData['coupon_discount'],
            $bookingData['total_payable']
        );

        $bookingData['image'] = $booking->hotel?->hotelPhotos()?->first()?->photo_url;

        $bookingData['hotel_name'] = $booking->hotel?->name;
        $hotelLocation = $booking->hotel?->location;
        $locality = $booking->hotel?->localities;

        $addressParts = array_filter([
            $locality?->name,
            $hotelLocation?->city,
            $hotelLocation?->state,
            $hotelLocation?->country,
            $hotelLocation?->zipcode,
        ]);

        $bookingData['hotel_address'] = implode(', ', $addressParts);

        // Add new structured keys
        $bookingData['review_summary'] = $review_summary; // always defined now
        $bookingData['room_combo'] = $roomCombo;
        $bookingData['price_summary'] = $priceSummary;
        $bookingData['checkin_time'] = \Carbon\Carbon::parse($bookingData['checkin_time'])->format('h:i A');
        $bookingData['checkout_time'] = \Carbon\Carbon::parse($bookingData['checkout_time'])->format('h:i A');
        // $bookingData['addon_summary'] = $addonSummary;


        return response()->json([
            'message' => 'Booking Details',
            'data' => $bookingData,
        ], 200);
    }

    public function cancel_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json([
                'message' => 'Booking not found',
            ], 404);
        }

        // Check if already cancelled
        if (BookingCancellation::where('booking_id', $booking->id)->exists()) {
            return response()->json([
                'message' => 'This booking has already been cancelled.',
            ], 400);
        }

        // Ensure booking has a check-in date
        if (! $booking->checkin_date) {
            return response()->json([
                'message' => 'Check-in date not found for this booking.',
            ], 400);
        }

        // Get current time and check-in time
        $now = Carbon::now();
        $checkin = Carbon::parse($booking->checkin_date);

        // Check if cancellation is before 12 hours of check-in
        if ($now->diffInHours($checkin, false) < 12) {
            return response()->json([
                'message' => 'Cancellation not allowed within 12 hours of check-in time.',
            ], 403);
        }

        $deduction_percentage = 10;
        // Example refund rule — full refund if cancelled on time
        $refundAmount = $booking->total_amount - ($booking->total_amount * $deduction_percentage / 100);

        // Create a cancellation record
        $cancellation = BookingCancellation::create([
            'booking_id' => $booking->id,
            'deduction percentage' => $deduction_percentage,
            'refund_amount' => $refundAmount,
            'cancel_reason' => $request->cancel_reason ?? null,
            'cancelled_by' => auth()->id() ?? null,
            'cancelled_at' => Carbon::now(),
            'status' => 'cancelled',
        ]);

        // Update booking status
        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'data' => $cancellation,
        ], 200);
    }

    public function cancel_bookings_details($id)
    {
        $cancel_booking_details = BookingCancellation::where('cancel_id', $id)->get();

        return response()->json([
            'message' => 'Cancel Booking Details',
            'data' => $cancel_booking_details,
        ], 200);
    }

    public function all_transactions(Request $request)
    {
        $perPage = $request->per_page ?? 15;

        $payments = BookingPayment::with(['booking.guests', 'booking.hotel'])
            ->whereHas('booking', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->orderBy('id', 'DESC')
            ->paginate($perPage);

        $data = $payments->map(function ($payment) {

            return [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'guest_name'     => $payment->booking?->guests->where('is_primary', 1)->first()?->guest_name ?? null,
                'hotel_name'     => $payment->booking?->hotel?->name,
                'booking_id'     => $payment->booking_id,
                'amount'         => $payment->amount,
                'currency'       => $payment->currency,
                'payment_method' => $payment->payment_method,
                'payment_date' => \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d'),
                'payment_status' => $payment->payment_status ? 'Success' : 'Failed',
            ];
        });

        return response()->json([
            'message' => 'All Transactions',
            'data' => $data,
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ], 200);
    }
}
