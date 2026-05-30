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
            'phone' => ['required', 'regex:/^01[0-9]{8,9}$/'],
            'age' => 'nullable|integer|min:1|max:150',
            'address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'phone.regex' => 'Phone must be a valid Malaysian number starting with 01 (e.g. 012-3456789).',
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
