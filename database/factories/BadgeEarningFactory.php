<?php

namespace Database\Factories;

use App\Models\BadgeEarning;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeEarningFactory extends Factory
{
    protected $model = BadgeEarning::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id,
            'badge_id' => Badge::inRandomOrder()->first()?->id,
            'earned_at' => $this->faker->dateTimeBetween('-24 months', 'now'),
            'source_event_id' => $this->faker->numberBetween(1, 120),
        ];
    }

    public function eventBased()
    {
        return $this->state(fn () => [
            'source_event_id' => $this->faker->numberBetween(1, 120),
        ]);
    }

    public function referralBased()
    {
        return $this->state(fn () => [
            'source_event_id' => null,
        ]);
    }
}