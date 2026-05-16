<?php

namespace Database\Factories;

use App\Models\EventVolunteer;
use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventVolunteerFactory extends Factory
{
    protected $model = EventVolunteer::class;

    private $attendanceStatuses = ['confirmed', 'pending_review', 'completed', 'absent'];
    private $statuses = ['active', 'withdrawn'];

    public function definition()
    {
        return [
            'event_id' => Event::inRandomOrder()->first()?->id,
            'user_id' => User::inRandomOrder()->first()?->id,
            'status' => $this->faker->randomElement($this->statuses),
            'attendance_status' => $this->faker->randomElement($this->attendanceStatuses),
            'absence_reason' => null,
            'points_awarded' => false,
            'points_earned' => 0,
            'joined_at' => $this->faker->dateTimeBetween('-24 months', 'now'),
        ];
    }

    public function completed()
    {
        return $this->state(fn () => [
            'attendance_status' => 'completed',
            'points_awarded' => true,
            'points_earned' => $this->faker->numberBetween(20, 100),
        ]);
    }

    public function absent()
    {
        return $this->state(fn () => [
            'attendance_status' => 'absent',
            'absence_reason' => $this->faker->randomElement(['sick', 'emergency', 'family_matter', 'work_commitment']),
            'points_awarded' => false,
            'points_earned' => 0,
        ]);
    }

    public function confirmed()
    {
        return $this->state(fn () => [
            'attendance_status' => 'confirmed',
            'points_awarded' => false,
            'points_earned' => 0,
        ]);
    }
}