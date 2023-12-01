<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistryRequest extends FormRequest
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
            'username' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'country_code' => ['required', 'string', 'min:2', 'max:5', 'regex:/^\+?\d{1,4}$/'],
            'phone' => ['required', 'numeric', 'digits_between:6,15'],
            'password' => ['required', 'min:8', 'confirmed']
        ];
    }
}
