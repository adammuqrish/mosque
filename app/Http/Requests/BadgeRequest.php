<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $badge = $this->route('badge');
        $badgeId = $badge ? $badge->getKey() : null;

        $rules = [
            'code' => 'required|string|max:50|unique:badges,code,' . $badgeId . ',id',
            'name' => 'required|string|max:255',
            'name_my' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'description_my' => 'required|string|max:1000',
            'tier' => 'required|string|max:50',
            'points_awarded' => 'required|integer|min:0|max:999999',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This badge code is already in use.',
            'icon.image' => 'The icon must be an image file.',
            'icon.mimes' => 'The icon must be a JPG, PNG, GIF, or WebP file.',
            'icon.max' => 'The icon must not exceed 2MB.',
            'points_awarded.max' => 'Points awarded cannot exceed 999,999.',
        ];
    }
}
