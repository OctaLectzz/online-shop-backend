<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayResource extends JsonResource
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
            'payment_id' => $this->payment_id,
            'payment' => new PaymentResource($this->payment),
            'transfer_date' => $this->transfer_date,
            'transfer_amount' => $this->transfer_amount,
            'transfer_proof' => $this->transfer_proof ? asset('storage/pays/' . $this->transfer_proof) : null,
            'validation_status' => $this->validation_status,
            'admin_notes' => $this->admin_notes,
            'validated_by' => $this->validator ? $this->validator->name : null,
            'created_at' => $this->created_at
        ];
    }
}
