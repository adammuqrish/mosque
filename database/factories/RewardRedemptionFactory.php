<?php

namespace Database\Factories;

use App\Models\RewardRedemption;
use App\Models\User;
use App\Models\Reward;
use Illuminate\Database\Eloquent\Factories\Factory;

class RewardRedemptionFactory extends Factory
{
    protected $model = RewardRedemption::class;

    private $statuses = ['pending', 'claimed', 'rejected', 'expired'];

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id,
            'reward_id' => Reward::inRandomOrder()->first()?->id,
            'points_spent' => $this->faker->numberBetween(50, 300),
            'status' => $this->faker->randomElement($this->statuses),
            'redeemed_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'claimed_at' => null,
            'expires_at' => $this->faker->dateTimeBetween('now', '+3 months'),
            'claim_code' => strtoupper(Str::random(8)),
            'fulfillment_notes' => null,
            'fulfilled_by' => null,
            'fulfilled_at' => null,
        ];
    }

    public function pending()
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'claimed_at' => null,
            'fulfillment_notes' => null,
            'fulfilled_by' => null,
            'fulfilled_at' => null,
        ]);
    }

    public function claimed()
    {
        return $this->state(fn () => [
            'status' => 'claimed',
            'claimed_at' => now(),
            'fulfillment_notes' => $this->faker->sentence(),
            'fulfilled_by' => $this->faker->numberBetween(1, 3),
            'fulfilled_at' => now(),
        ]);
    }
}