<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
        return [
            'user_id' => 'required|exists:users,id',
            'recipient_name' => 'required|string|max:50',
            'phone_number' => 'required|string|max:15',
            'province_id' => 'required|integer',
            'province_name' => 'required|string|max:255',
            'city_id' => 'required|integer',
            'city_name' => 'required|string|max:255',
            'district_id' => 'nullable|integer',
            'district_name' => 'nullable|string|max:255',
            'village_id' => 'nullable|integer',
            'village_name' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'label' => 'required|in:house,office,etc',
            'notes' => 'nullable',
            'is_default' => 'boolean'
        ];
    }
}
