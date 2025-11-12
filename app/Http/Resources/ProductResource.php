<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'category_id' => $this->category_id,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category->name ?? null,
            'description' => $this->description,
            'dimensions' => [
                'weight' => $this->weight,
                'height' => $this->height,
                'width' => $this->width,
                'length' => $this->length
            ],
            'status' => $this->status,
            'created_by' => $this->creator->name ?? null,

            // Images
            'images' => $this->images->isNotEmpty()
                ? $this->images->map(function ($image) {
                    return Storage::url('products/' . $image->image);
                })->all()
                : [],

            // Variants
            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'price' => (int) $variant->price,
                    'stock' => (int) $variant->stock,
                    'sold' => (int) $variant->sold,
                    'image' => $variant->image ? Storage::url('products/variants/' . $variant->image) : null,
                ];
            })->all(),

            // Attributes
            'attributes' => $this->attributes->map(function ($attr) {
                return [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'lists' => $attr->lists ?? [],
                ];
            })->all(),

            // Informations
            'informations' => $this->informations->map(function ($info) {
                return [
                    'id' => $info->id,
                    'name' => $info->name,
                    'description' => $info->description,
                ];
            })->all(),

            // Tags
            'tags' => $this->tags->pluck('name')->all(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
