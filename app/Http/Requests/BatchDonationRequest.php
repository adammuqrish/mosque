<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donations' => 'required|array|min:1|max:50',
            'donations.*.amount' => 'required|numeric|min:0.01|max:9999999999.99',
            'donations.*.source' => 'required|in:cash,online',
            'donations.*.donation_date' => 'required|date|before_or_equal:today',
            'donations.*.donor_name' => 'nullable|string|max:255',
            'donations.*.fund_purpose' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'donations.required' => 'At least one donation is required.',
            'donations.min' => 'At least one donation is required.',
            'donations.max' => 'Maximum 50 donations per batch.',
            'donations.*.amount.required' => 'Row :index: Amount is required.',
            'donations.*.amount.min' => 'Row :index: Amount must be at least 0.01.',
            'donations.*.source.required' => 'Row :index: Source is required.',
            'donations.*.donation_date.required' => 'Row :index: Date is required.',
            'donations.*.donation_date.before_or_equal' => 'Row :index: Date cannot be in the future.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $donations = $this->input('donations', []);
        foreach ($donations as $i => $d) {
            $donations[$i]['amount'] = round((float) ($d['amount'] ?? 0), 2);
            $donations[$i]['category'] = 'sadaqah';
            $donations[$i]['donor_name'] = isset($d['donor_name']) ? strip_tags(trim($d['donor_name'])) : null;
            $donations[$i]['fund_purpose'] = isset($d['fund_purpose']) ? strip_tags(trim($d['fund_purpose'])) : null;
        }
        $this->merge(['donations' => $donations]);
    }
}
