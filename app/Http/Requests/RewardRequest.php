<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reward = $this->route('reward');
        $rewardId = $reward ? $reward->getKey() : null;

        $rules = [
            'code' => 'required|string|max:50|unique:rewards,code,' . $rewardId . ',id',
            'name' => 'required|string|max:255',
            'name_my' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'description_my' => 'required|string|max:1000',
            'category' => 'required|in:priority,recognition,privilege,seasonal',
            'points_cost' => 'required|integer|min:1|max:999999',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'stock_quantity' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This reward code is already in use.',
            'category.in' => 'Category must be one of: priority, recognition, privilege, seasonal.',
            'points_cost.max' => 'Points cost cannot exceed 999,999.',
            'valid_until.after_or_equal' => 'Valid until date must be after or equal to valid from date.',
        ];
    }
}