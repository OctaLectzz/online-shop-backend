<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 */
class PayRequest extends FormRequest
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
        $payId = $this->route('pay')?->getKey();

        return [
            'order_id' => 'required|exists:orders,id',
            'payment_id' => 'required|exists:payments,id',
            'transfer_date' => 'required|date',
            'transfer_amount' => 'required|numeric|min:0',
            'transfer_proof' => $payId ? 'nullable|image|mimes:jpg,jpeg,png|max:3072' : 'required|image|mimes:jpg,jpeg,png|max:3072',
            'validation_status' => 'nullable|in:pending,accepted,rejected',
            'admin_notes' => 'nullable|string',
            'validated_by' => 'nullable|exists:users,id'
        ];
    }
}
