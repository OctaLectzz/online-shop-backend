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
            'sku' => 'required|string|max:255|unique:products,sku,' . $productId,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|integer',
            'height' => 'nullable|integer',
            'width' => 'nullable|integer',
            'length' => 'nullable|integer',
            'status' => 'boolean',
            'use_variant' => 'boolean',

            // Image
            'images' => 'nullable|array',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:3072',

            // Variant
            'variants' => 'sometimes|array',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'required_with:variants|integer|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.image' => 'nullable',

            // Attribute
            'attributes' => 'sometimes|array',
            'attributes.*.name' => 'required_with:attributes|string|max:255',
            'attributes.*.lists' => 'required_with:attributes|array',
            'attributes.*.lists.*' => 'string|max:255',

            // Information
            'informations' => 'sometimes|array',
            'informations.*.name' => 'required_with:informations|string|max:255',
            'informations.*.description' => 'required_with:informations|string|max:1000',

            // Tag
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:255'
        ];
    }
}
