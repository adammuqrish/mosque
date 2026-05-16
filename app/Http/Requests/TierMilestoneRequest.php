<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TierMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tier = $this->route('tier');
        $tierId = $tier ? $tier->getKey() : null;

        $rules = [
            'tier' => 'required|string|max:50|unique:tier_milestones,tier,' . $tierId . ',id',
            'min_points' => 'required|integer|min:0|max:999999',
            'name' => 'required|string|max:255',
            'name_my' => 'required|string|max:255',
            'benefits' => 'required|string|max:1000',
            'benefits_my' => 'required|string|max:1000',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tier.unique' => 'This tier name is already in use.',
            'min_points.max' => 'Minimum points cannot exceed 999,999.',
        ];
    }
}