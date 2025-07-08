<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LogResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'action' => $this->action,
            'description' => $this->description,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'read' => $this->isReadBy(Auth::id()),
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
