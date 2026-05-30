<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categories = implode(',', ['zakat', 'zakat_fitr', 'sadaqah', 'waqf']);
        $requiresDonor = ['zakat', 'zakat_fitr', 'waqf'];

        return [
            'amount' => 'required|numeric|min:0.01|max:9999999999.99',
            'category' => 'required|string|in:' . $categories,
            'source' => 'required|in:cash,online',
            'donation_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:1000',

            'reference' => 'nullable|string|max:100',

            'fund_purpose' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $category = $this->input('category');
                    if ($category === 'sadaqah' && empty($value)) {
                        $fail('Fund purpose is required for Sadaqah donations (e.g. General Fund, Kipas Gergasi).');
                    }
                    if (in_array($category, ['zakat', 'zakat_fitr', 'waqf']) && !empty($value) && $value !== 'General Fund') {
                        $fail('Fund purpose for ' . $category . ' donations must be "General Fund". The selected purpose will be ignored and set to General Fund.');
                    }
                },
            ],

            'amil_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $category = $this->input('category');
                    if (in_array($category, ['zakat', 'zakat_fitr']) && empty($value)) {
                        $fail('Amil name is required for Zakat donations.');
                    }
                },
            ],
            'amil_user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('is_amil', true);
                }),
            ],
            'akad_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) {
                    $category = $this->input('category');
                    if (in_array($category, ['zakat', 'zakat_fitr']) && empty($value)) {
                        $fail('Akad date is required for Zakat donations.');
                    }
                },
            ],
            'akad_notes' => 'nullable|string|max:500',

            'donor_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($requiresDonor) {
                    $category = $this->input('category');
                    if (in_array($category, $requiresDonor) && empty($value)) {
                        $fail('Donor name is required for Zakat and Waqf donations.');
                    }
                },
            ],
            'donor_ic' => [
                'nullable',
                'string',
                'regex:/^(?:\d{6}-\d{2}-\d{4}|\d{12})$/',
                function ($attribute, $value, $fail) use ($requiresDonor) {
                    $category = $this->input('category');
                    if (in_array($category, $requiresDonor) && empty($value)) {
                        $fail('Donor IC/MyKad is required for Zakat and Waqf donations.');
                    }
                },
            ],
            'donor_phone' => ['nullable', 'string', 'regex:/^(?:(?:\+?6?0)1\d{8,9}|\d{9,11})$/u', 'max:15'],
            'donor_email' => 'nullable|email|max:255',
            'donor_address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Donation amount must be at least 0.01.',
            'amount.max' => 'Donation amount exceeds maximum allowed value.',
            'donation_date.before_or_equal' => 'Donation date cannot be in the future.',
            'source.in' => 'Donation source must be either cash or online.',
            'donor_ic.regex' => 'Donor IC must be in 12-digit format (e.g. 010203-10-1234 or 010203101234).',
            'donor_phone.regex' => 'Donor phone must be a valid Malaysian number (e.g. 0123456789).',
            'donor_email.email' => 'Donor email must be a valid email address.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $ic = $this->donor_ic;
        if ($ic && preg_match('/^\d{12}$/', $ic)) {
            $ic = substr($ic, 0, 6) . '-' . substr($ic, 6, 2) . '-' . substr($ic, 8, 4);
        }

        $this->merge([
            'amount' => round((float) $this->amount, 2),
            'status' => 'pending',
            'reference' => $this->reference ? strip_tags(trim($this->reference)) : null,
            'fund_purpose' => $this->fund_purpose ? strip_tags(trim($this->fund_purpose)) : null,
            'description' => $this->description ? strip_tags(trim($this->description)) : null,
            'donor_name' => $this->donor_name ? strip_tags(trim($this->donor_name)) : null,
            'donor_ic' => $ic ?: null,
            'donor_phone' => $this->donor_phone ? strip_tags(trim($this->donor_phone)) : null,
            'donor_email' => $this->donor_email ? strip_tags(trim($this->donor_email)) : null,
            'donor_address' => $this->donor_address ? strip_tags(trim($this->donor_address)) : null,
        ]);
    }
}
