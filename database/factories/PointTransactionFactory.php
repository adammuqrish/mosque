<?php

namespace Database\Factories;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointTransactionFactory extends Factory
{
    protected $model = PointTransaction::class;

    private $types = ['earned', 'redeemed', 'adjusted', 'revoked', 'refunded'];
    private $reasons = [
        'earned' => ['Event attendance', 'Referral bonus', 'Badge unlock', 'Streak bonus', 'Tier upgrade bonus'],
        'redeemed' => ['Reward redemption', 'Prize claim', 'Discount redemption'],
        'adjusted' => ['Admin adjustment - bonus', 'Correction'],
        'revoked' => ['Absence penalty', 'Invalid participation'],
        'refunded' => ['Cancelled reward', 'Error correction'],
    ];

    public function definition()
    {
        $type = $this->faker->randomElement($this->types);
        
        return [
            'user_id' => User::inRandomOrder()->first()?->id,
            'type' => $type,
            'points' => $type === 'redeemed' || $type === 'revoked' 
                ? -$this->faker->numberBetween(10, 200)
                : $this->faker->numberBetween(10, 150),
            'balance_after' => $this->faker->numberBetween(0, 2000),
            'reason' => $this->faker->randomElement($this->reasons[$type]),
            'source_type' => $this->faker->randomElement(['event', 'badge', 'referral', 'reward', 'admin', null]),
            'source_id' => $this->faker->numberBetween(1, 500),
            'admin_id' => null,
            'admin_notes' => null,
        ];
    }

    public function earned()
    {
        return $this->state(fn () => [
            'type' => 'earned',
            'points' => $this->faker->numberBetween(10, 150),
            'reason' => $this->faker->randomElement($this->reasons['earned']),
        ]);
    }

    public function redeemed()
    {
        return $this->state(fn () => [
            'type' => 'redeemed',
            'points' => -$this->faker->numberBetween(10, 200),
            'reason' => $this->faker->randomElement($this->reasons['redeemed']),
        ]);
    }
}