<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // STEP 1: Validate required fields
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => 'required|string|min:8|max:50|confirmed',
            'phone' => 'required|digits_between:9,15',
            'special_code' => 'nullable|string|max:50',
            // STEP 2: Referral code validation - must be uppercase letters/numbers, max 8 chars
            'referral_code' => ['nullable', 'string', 'max:8', 'regex:/^[A-Z0-9]{0,8}$/'],

            // STEP 3: Sanitize inputs (applied via prepareForValidation)
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'phone.digits_between' => 'Phone number must be between 9 to 15 digits.',
            'password.min' => 'Password must be at least 8 characters.',
            // STEP 4: Custom message for invalid referral code format
            'referral_code.regex' => 'Referral code must be uppercase letters and numbers only (e.g., A3F9B2C1).',
        ];
    }

    protected function prepareForValidation(): void
    {
        // STEP 3: Sanitize input before validation
        $this->merge([
            'name' => strip_tags(trim($this->name)),
            'email' => strtolower(trim($this->email)),
            'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            'special_code' => strip_tags(trim($this->special_code ?? '')),
            'referral_code' => strtoupper(trim($this->referral_code ?? '')), // Normalize to uppercase
        ]);
    }
}
