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
            'category_id' => $this->category_id,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => new CategoryResource($this->category),
            'description' => $this->description,
            'weight' => $this->weight,
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
            'status' => $this->status === 1 ? true : false,
            'use_variant' => $this->use_variant === 1 ? true : false,
            'created_by' => $this->creator->name ?? null,

            // Images
            'images' => $this->images->isNotEmpty()
                ? $this->images->map(function ($image) {
                    return asset('storage/products/' . $image->image);
                })->all()
                : [],

            // Variants
            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'name' => $variant->name,
                    'price' => (int) $variant->price,
                    'stock' => (int) $variant->stock,
                    'sold' => (int) $variant->sold,
                    'image' => $variant->image ? asset('storage/products/variants/' . $variant->image) : null
                ];
            })->all(),

            // Attributes
            'attributes' => $this->attributes->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'product_id' => $attribute->product_id,
                    'name' => $attribute->name,
                    'lists' => $attribute->lists ?? []
                ];
            })->all(),

            // Informations
            'informations' => $this->informations->map(function ($information) {
                return [
                    'id' => $information->id,
                    'product_id' => $information->product_id,
                    'name' => $information->name,
                    'description' => $information->description
                ];
            })->all(),

            // Tags
            'tags' => $this->tags->pluck('name')->all(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
