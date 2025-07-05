<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 */
class ShipmentRequest extends FormRequest
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
        $shipmentId = $this->route('shipment')?->getKey();

        return [
            'order_id' => 'required|exists:orders,id',
            'shipping_service' => 'required|string|max:255',
            'courier_name' => 'nullable|string|max:255',
            'shipping_estimation' => 'nullable|string|max:255',
            'shipping_description' => 'nullable|string|max:255',
            'tracking_number' => 'required|string|unique:shipments,tracking_number,' . $shipmentId,
        ];
    }
}
