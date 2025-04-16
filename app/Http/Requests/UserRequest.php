<?php

namespace App\Http\Requests;

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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'initial_balance' => 'required|numeric|min:0',
        ];

        // For update operations
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $this->user?->id;
            $rules['password'] = 'sometimes|required|string|min:8';
            $rules['initial_balance'] = 'sometimes|required|numeric|min:0';
        }

        return $rules;
    }
}
