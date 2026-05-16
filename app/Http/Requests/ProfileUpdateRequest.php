<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // STEP 1: Validate personal info fields
            'name' => 'required|string|max:255',
            'phone' => 'required|digits_between:9,15',
            'age' => 'nullable|integer|min:1|max:150',
            'address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'phone.digits_between' => 'Phone number must be between 9 to 15 digits.',
            'age.min' => 'Age must be at least 1.',
            'age.max' => 'Age cannot exceed 150.',
            'address.max' => 'Address cannot exceed 500 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // STEP 2: Sanitize inputs
        $this->merge([
            'name' => strip_tags(trim($this->name)),
            'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            'address' => $this->address ? strip_tags(trim($this->address)) : null,
        ]);
    }
}
