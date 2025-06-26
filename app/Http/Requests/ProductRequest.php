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
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|integer',
            'height' => 'nullable|integer',
            'width' => 'nullable|integer',
            'length' => 'nullable|integer',
            'status' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:255'
        ];
    }
}
