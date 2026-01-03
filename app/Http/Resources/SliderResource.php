<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'title'     => $this->title,
            'subtitle'    => $this->subtitle,
            'image_path' => $this->image_path,
            'link' => $this->link,
            'button_text' => $this->button_text,
        ];
    }
}
