<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'promo_id' => $this->promo_id,
            'invoice' => $this->invoice,
            'user' => new UserResource($this->user),
            'promo' => new PromoResource($this->promo),
            'total_price' => $this->total_price,
            'discount_value' => $this->discount_value,
            'subtotal_price' => $this->subtotal_price,
            'note' => $this->note,
            'order_date' => $this->order_date,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product' => $item->product ? new ProductResource($item->product) : null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price
                ];
            }),
            'created_at' => $this->created_at
        ];
    }
}
