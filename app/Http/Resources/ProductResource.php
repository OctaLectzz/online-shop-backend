<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'slug' => $this->slug,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category->name ?? null,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'dimensions' => [
                'weight' => $this->weight,
                'height' => $this->height,
                'width' => $this->width,
                'length' => $this->length,
            ],
            'status' => $this->status,
            'sold' => $this->sold,
            'created_by' => $this->creator->name ?? null,
            'images' => $this->images->pluck('image'),
            'tags' => $this->tags->pluck('name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
