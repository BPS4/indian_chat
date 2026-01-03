<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->coupon_id,
            'code' => $this->code,
            // 'description' => $this->description,
            'discount_type' => $this->discount_type,
            'amountOff' => (int) $this->discount_value,
            'min_amount' => (int)  $this->min_booking_amount,

        ];
    }
}
