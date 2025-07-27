<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 */
class PaymentRequest extends FormRequest
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
        $paymentId = $this->route('payment')?->getKey();

        return [
            'image' => $paymentId ? 'nullable|image|mimes:jpg,jpeg,png|max:3072' : 'required|image|mimes:jpg,jpeg,png|max:3072',
            'name' => 'required|string|max:50',
            'type' => 'required|in:bank,ewallet,qris,cash',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string|max:100',
            'tutorial' => 'nullable|string',
            'status' => 'boolean'
        ];
    }
}
