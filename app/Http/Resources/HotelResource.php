<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'location_id'  => $this->location_id,
            'locality_id'  => $this->locality_id,
            'location' => optional($this->location)->city,
            'locality' => optional($this->localities)->name,
            'image' => optional($this->hotelPhotos->firstWhere('is_cover', true))->photo_url,
            'description'  => $this->description,
            'rating_avg'   => $this->getAverageRating(),
            'review_count' => $this->getReviewCount(),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'is_featured'  => $this->is_featured,
            'old_price' => $this->getLowestRoomPrice(),
            'new_price' => $this->getDiscountedLowestPrice(),
            'room_types'    => RoomTypeResource::collection($this->whenLoaded('roomTypes')),
            'hotels_offer' => new OfferResource($this->whenLoaded('hotelsOffer')),
            'hotel_photos' => $this->when($this->isDetail(), $this->whenLoaded('hotelPhotos')),
            'hotel_guests_photo' => $this->when($this->isDetail(), $this->whenLoaded('hotelGuestPhotos')),
            'hotel_policy' => $this->when($this->isDetail(), $this->whenLoaded('hotelPolicies')),
            // 'facilities' => $this->when($this->isDetail() && $this->relationLoaded('facilities'), function () {
            //     // Group facilities by group name
            //     $grouped = $this->facilities
            //         ->groupBy(fn($f) => $f->group->group_name ?? 'Ungrouped');

            //     // Pick only the first group
            //     $groupName = $grouped->keys()->first();
            //     $firstGroup = $grouped->first();

            //     // Map each facility to include name and icon
            //     $facilities = collect($firstGroup)->map(fn($f) => [
            //         'facility_name' => $f->facility_name,
            //         'icon' => $f->icon
            //     ])->values();

            //     return [
            //         'group_name' => $groupName,
            //         'items' => $facilities
            //     ];
            // }),
            'facilities' => $this->when(
                $this->isDetail() && $this->relationLoaded('facilities'),
                function () {
                    return $this->facilities->map(fn($f) => [
                        'facility_name' => $f->facility_name,
                        'icon'          => $f->icon,
                        'group_name'    => $f->group->group_name ?? null, // optional
                    ])->values();
                }
            ),



            'hotel_review' => $this->when($this->isDetail(),  ReviewResource::collection($this->whenLoaded('hotelReview'))),


        ];
    }


    protected function isDetail(): bool
    {
        return request()->routeIs('hotels.details');
    }

    public function getLowestRoomPrice()
    {
        // if (!$this->relationLoaded('roomTypes')) {
        //     return null;
        // }

        $today = now()->toDateString(); // Or use Carbon::today()->toDateString();

        $prices = $this->roomTypes
            ->flatMap(function ($roomType) use ($today) {
                return $roomType->roomPrices
                    ? $roomType->roomPrices->filter(function ($price) use ($today) {
                        return $price->start_date <= $today && $price->end_date >= $today;
                    })
                    : collect();
            })
            ->pluck('base_price');
        // dd(1);

        return $prices->min() ? round($prices->min(), 2) : null;
    }


    public function getDiscountedLowestPrice()
    {
        $original = $this->getLowestRoomPrice();

        // if (is_null($original)) {
        //     return null;
        // }

        $offer = $this->hotelsOffer;

        if (!$offer || !$offer->discount_type || !$offer->discount_value) {
            return $original;
        }

        if ($offer->discount_type === 'percent') {
            $discount = ($offer->discount_value / 100) * $original;
        } else {
            $discount = $offer->discount_value;
        }

        return max(round($original - $discount, 2), 0);
    }


    protected function getAverageRating(): ?float
    {
        // Check if the 'hotelReview' relation is loaded
        if (!$this->relationLoaded('hotelReview')) {
            return null;
        }

        $average = $this->hotelReview->avg('rating');

        // Return the average rounded to two decimal places, or null if no reviews
        return $average !== null ? round($average, 1) : null;
    }

    /**
     * Get the count of hotel reviews.
     *
     * @return int
     */
    protected function getReviewCount(): int
    {
        // Check if the 'hotelReview' relation is loaded
        if (!$this->relationLoaded('hotelReview')) {
            return 0;
        }

        return $this->hotelReview->count();
    }
}
