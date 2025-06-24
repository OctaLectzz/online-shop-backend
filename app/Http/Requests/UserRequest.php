<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : $user;

        return [
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'name' => 'required|string|max:50',
            'username' => 'required|string|max:20|unique:users,username,' . $userId,
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => $userId ? 'nullable|min:8' : 'required|min:8',
            'phone_number' => 'nullable|string|max:15',
            'status' => 'boolean'
        ];
    }
}
