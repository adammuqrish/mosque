<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequestForm extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:9999999999.99',
            'purpose' => 'required|string|min:5|max:500',
            'type' => 'required|in:zakat,zakat_fitr,sadaqah,waqf',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Withdrawal amount must be at least 0.01.',
            'amount.max' => 'Withdrawal amount exceeds maximum allowed value.',
            'purpose.required' => 'Please provide a purpose for this withdrawal request.',
            'purpose.min' => 'Purpose description must be at least 5 characters.',
            'purpose.max' => 'Purpose description cannot exceed 500 characters.',
            'type.required' => 'Please select a fund type.',
            'type.in' => 'Fund type must be Zakat, Zakat Fitr, Sadaqah, or Waqf.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => round((float) $this->amount, 2),
            'purpose' => strip_tags(trim($this->purpose)),
        ]);
    }
}
