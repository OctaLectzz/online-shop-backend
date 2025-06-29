<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 */
class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')?->getKey();

        return [
            'slug' => 'required|string|max:255|unique:categories,slug,' . $categoryId,
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'name' => 'required|string|max:50',
            'description' => 'nullable'
        ];
    }
}
