<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'room_name'     => $this->room_name,
            'description'    => $this->description,
            'max_guests' => $this->max_guests,
            'room_size' => $this->room_size,
            'bed_type' => $this->bed_type,
            'photo_url' => $this->photo_url,
        ];
    }
}
