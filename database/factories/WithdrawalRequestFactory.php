<?php

namespace Database\Factories;

use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WithdrawalRequestFactory extends Factory
{
    protected $model = WithdrawalRequest::class;

    private $purposes = ['masjid_maintenance', 'charity', 'events', 'utilities', 'equipment', 'renovation', 'community_programs'];
    private $statuses = ['pending', 'approved', 'rejected'];

    public function definition()
    {
        $status = $this->faker->randomElement($this->statuses);
        
        return [
            'requested_by' => User::where('role', 'treasurer')->inRandomOrder()->first()?->id ?: User::inRandomOrder()->first()?->id,
            'amount' => $this->faker->numberBetween(100, 5000),
            'purpose' => $this->faker->randomElement($this->purposes),
            'status' => $status,
            'approved_by' => $status === 'approved' ? User::where('role', 'admin')->first()?->id : null,
            'approved_at' => $status === 'approved' ? $this->faker->dateTimeBetween('-12 months', 'now') : null,
            'rejection_reason' => $status === 'rejected' ? $this->faker->randomElement(['insufficient_funds', 'lack_of_documentation', 'not_approved_by_committee', 'duplicate_request']) : null,
            'created_at' => $this->faker->dateTimeBetween('-24 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
        ];
    }

    public function pending()
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function approved()
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_by' => $this->faker->numberBetween(1, 3),
            'approved_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'rejection_reason' => null,
        ]);
    }

    public function rejected()
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => $this->faker->randomElement(['insufficient_funds', 'lack_of_documentation', 'not_approved_by_committee', 'duplicate_request']),
        ]);
    }
}