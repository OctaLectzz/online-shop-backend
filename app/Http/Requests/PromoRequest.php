<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 */
class PromoRequest extends FormRequest
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
        $promoId = $this->route('promo')?->getKey();

        return [
            'promo_code' => 'required|string|unique:promos,promo_code,' . $promoId,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0|required_if:discount_type,percent',
            'quota' => 'nullable|integer|min:0',
            'usage_count' => 'nullable|integer|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'status' => 'boolean'
        ];
    }
}
