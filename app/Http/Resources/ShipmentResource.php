<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'order' => new OrderResource($this->order),
            'shipping_date' => $this->shipping_date,
            'shipping_service' => $this->shipping_service,
            'courier_name' => $this->courier_name,
            'shipping_estimation' => $this->shipping_estimation,
            'shipping_description' => $this->shipping_description,
            'tracking_number' => $this->tracking_number,
            'processed_by' => $this->processor ? $this->processor->name : null,
            'created_at' => $this->created_at
        ];
    }
}
