<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 */
class SettingRequest extends FormRequest
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
        $settingKey = $this->route('setting')?->key;

        return [
            'key' => $settingKey ? 'nullable|string|unique:settings,key,' . $settingKey . ',key' : 'required|string|unique:settings,key,' . $settingKey . ',key',
            'value' => 'nullable'
        ];
    }
}
