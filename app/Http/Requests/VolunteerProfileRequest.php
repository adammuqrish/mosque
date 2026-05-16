<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VolunteerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // STEP 1: Validate volunteer profile fields
            'skills' => 'required|array|min:1',
            'skills.*' => 'string|distinct|max:50',
            'availability' => 'nullable|array',
            'availability.*' => 'string|max:50',
            'hobbies' => 'nullable|array',
            'hobbies.*' => 'string|distinct|max:50',
            'interests' => 'nullable|array',
            'interests.*' => 'string|distinct|max:50',
            'languages' => 'nullable|array',
            'languages.*' => 'string|distinct|max:50',
            'location' => 'nullable|string|max:255',
            'health_status' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:2000',
            'long_term_availability' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'skills.required' => 'Please list at least one skill.',
            'skills.min' => 'Please list at least one skill.',
            'experience.max' => 'Experience description cannot exceed 2000 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize arrays by trimming and removing duplicates (case-insensitive duplicate check)
        $sanitizeArray = function ($items) {
            if (!is_array($items))
                return [];
            $clean = [];
            $lowerSeen = [];
            foreach ($items as $item) {
                if (is_string($item)) {
                    $trimmed = strip_tags(trim($item));
                    $lower = strtolower($trimmed);
                    if ($trimmed !== '' && !in_array($lower, $lowerSeen)) {
                        $clean[] = $trimmed;
                        $lowerSeen[] = $lower;
                    }
                }
            }
            return $clean;
        };

        // STEP 2: Sanitize all inputs
        $this->merge([
            'skills' => $this->has('skills') ? $sanitizeArray($this->input('skills')) : [],
            'hobbies' => $this->has('hobbies') ? $sanitizeArray($this->input('hobbies')) : [],
            'interests' => $this->has('interests') ? $sanitizeArray($this->input('interests')) : [],
            'languages' => $this->has('languages') ? $sanitizeArray($this->input('languages')) : [],
            'location' => is_string($this->location) ? strip_tags(trim($this->location)) : null,
            'health_status' => is_string($this->health_status) ? strip_tags(trim($this->health_status)) : null,
            'experience' => is_string($this->experience) ? strip_tags(trim($this->experience)) : null,
            'long_term_availability' => is_string($this->long_term_availability) ? strip_tags(trim($this->long_term_availability)) : null,
        ]);
    }
}
