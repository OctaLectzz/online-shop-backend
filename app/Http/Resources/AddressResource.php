<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'recipient_name' => $this->recipient_name,
            'phone_number' => $this->phone_number,
            'province_id' => $this->province_id,
            'province_name' => $this->province_name,
            'city_id' => $this->city_id,
            'city_name' => $this->city_name,
            'district_id' => $this->district_id,
            'district_name' => $this->district_name,
            'village_id' => $this->village_id,
            'village_name' => $this->village_name,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'label' => $this->label,
            'notes' => $this->notes,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
