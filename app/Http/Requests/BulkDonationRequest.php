<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:9999999999.99',
            'donation_date' => 'required|date|before_or_equal:today',
            'fund_purpose' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'witnesses' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Total amount from the box is required.',
            'amount.min' => 'Amount must be at least 0.01.',
            'amount.max' => 'Amount exceeds maximum allowed value.',
            'donation_date.required' => 'Collection date is required.',
            'donation_date.before_or_equal' => 'Collection date cannot be in the future.',
            'fund_purpose.required' => 'Fund purpose is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => round((float) $this->amount, 2),
            'fund_purpose' => $this->fund_purpose ? strip_tags(trim($this->fund_purpose)) : null,
            'description' => $this->description ? strip_tags(trim($this->description)) : null,
            'witnesses' => $this->witnesses ? strip_tags(trim($this->witnesses)) : null,
        ]);
    }
}
