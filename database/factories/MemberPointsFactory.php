<?php

namespace Database\Factories;

use App\Models\MemberPoints;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberPointsFactory extends Factory
{
    protected $model = MemberPoints::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'total_points' => $this->faker->numberBetween(0, 2500),
            'available_points' => $this->faker->numberBetween(0, 500),
            'redeemed_points' => $this->faker->numberBetween(0, 200),
            'current_streak' => $this->faker->numberBetween(0, 15),
            'longest_streak' => $this->faker->numberBetween(0, 30),
            'last_activity_date' => $this->faker->dateTimeBetween('-60 days', 'now'),
        ];
    }

    public function newUser()
    {
        return $this->state(fn () => [
            'total_points' => $this->faker->numberBetween(0, 50),
            'available_points' => $this->faker->numberBetween(0, 50),
            'redeemed_points' => 0,
            'current_streak' => $this->faker->numberBetween(0, 3),
            'longest_streak' => $this->faker->numberBetween(0, 5),
        ]);
    }

    public function powerUser()
    {
        return $this->state(fn () => [
            'total_points' => $this->faker->numberBetween(500, 2500),
            'available_points' => $this->faker->numberBetween(100, 500),
            'redeemed_points' => $this->faker->numberBetween(50, 300),
            'current_streak' => $this->faker->numberBetween(5, 15),
            'longest_streak' => $this->faker->numberBetween(10, 30),
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (MemberPoints $points) {
            if (!$points->user_id) {
                $user = User::whereDoesntHave('memberPoints')->inRandomOrder()->first();
                if ($user) {
                    $points->user_id = $user->id;
                    $points->save();
                }
            }
        });
    }
}