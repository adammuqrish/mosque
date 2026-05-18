<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Rules\UniqueEventLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('id') ?? $this->input('event_id');

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10|max:2000',
            'event_date' => ['required', 'date', new UniqueEventLocation($eventId)],
            'end_time' => 'required|date|after:event_date',
            'location' => 'required|string|max:255',
            'event_location' => 'required|string|max:255',
            'max_volunteers' => 'required|integer|min:1|max:10000',
            'required_skills' => 'required|string|max:500',
            'required_hobbies' => 'nullable|string|max:500',
            'required_languages' => 'nullable|string|max:500',
            'health_requirement' => 'nullable|string|max:500',
            'gamification_category' => 'required|in:religious,charity,education,community,youth,elderly,maintenance',
        ];
    }

    public function messages(): array
    {
        return [
            'event_date.after_or_equal' => 'Event date cannot be in the past.',
            'end_time.required' => 'Event end time is required.',
            'end_time.after' => 'End time must be after the start time.',
            'max_volunteers.min' => 'Maximum volunteers must be at least 1.',
            'max_volunteers.max' => 'Maximum volunteers cannot exceed 10,000.',
            'description.min' => 'Description must be at least 10 characters.',
            'gamification_category.required' => 'Please select an event category.',
            'gamification_category.in' => 'Invalid event category selected.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => strip_tags(trim($this->title)),
            'description' => strip_tags(trim($this->description)),
            'location' => strtolower(preg_replace('/\s+/', '', strip_tags(trim($this->location)))),
            'event_location' => strtolower(preg_replace('/\s+/', '', strip_tags(trim($this->event_location)))),
            'required_skills' => strip_tags(trim($this->required_skills)),
            'required_hobbies' => $this->required_hobbies ? strip_tags(trim($this->required_hobbies)) : null,
            'required_languages' => $this->required_languages ? strip_tags(trim($this->required_languages)) : null,
            'health_requirement' => $this->health_requirement ? strip_tags(trim($this->health_requirement)) : null,
            'gamification_category' => $this->gamification_category,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $eventId = $this->route('id') ?? $this->input('event_id');
            $event = Event::find($eventId);
            
            if ($event) {
                if ($event->isPast()) {
                    $validator->errors()->add('event_date', 'Cannot edit events that have already passed.');
                    return;
                }
                
                $newMax = (int) $this->input('max_volunteers');
                $currentCount = (int) $event->volunteerCount;

                if ($newMax !== null && $newMax !== 0 && $newMax < $currentCount) {
                    $validator->errors()->add(
                        'max_volunteers',
                        "Cannot reduce maximum volunteers below current count ({$currentCount})."
                    );
                }
                
                $eventDate = $this->input('event_date');
                if ($eventDate && \Carbon\Carbon::parse($eventDate)->isPast()) {
                    $validator->errors()->add('event_date', 'Event date cannot be in the past.');
                }
            }
        });
    }
}
