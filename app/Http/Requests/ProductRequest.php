<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 */
class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->getKey();

        return [
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:255|unique:products,sku,' . $productId,
            'slug' => 'required|string|max:255|unique:products,slug,' . $productId,
            'name' => 'required|string|max:255',
            'description' => 'required',
            'weight' => 'required|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'width'  => 'nullable|integer|min:0',
            'length' => 'nullable|integer|min:0',
            'status' => 'boolean',
            'use_variant' => 'boolean',

            // Images
            'images' => 'nullable|array',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp,heic|max:5120',
            'keep_images' => 'sometimes|array',
            'keep_images.*' => 'string',

            // Variants
            'variants' => 'sometimes|array',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'required_with:variants|integer|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic|max:5120',
            'variants.*._delete' => 'nullable',

            // Attributes
            'attributes' => 'sometimes|array',
            'attributes.*.id' => 'nullable|integer|exists:product_attributes,id',
            'attributes.*.name' => 'required_with:attributes|string|max:255',
            'attributes.*.lists' => 'required_with:attributes|array|min:1',
            'attributes.*.lists.*' => 'string|max:255',
            'attributes.*._delete' => 'nullable',

            // Informations
            'informations' => 'sometimes|array',
            'informations.*.id' => 'nullable|integer|exists:product_informations,id',
            'informations.*.name' => 'required_with:informations|string|max:255',
            'informations.*.description' => 'required_with:informations|string|max:1000',
            'informations.*._delete' => 'nullable',

            // Tags
            'tags' => 'sometimes|array',
            'tags.*' => 'required|string|max:255'
        ];
    }
}
