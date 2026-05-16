<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    private $categories = ['zakat', 'sedekah', 'wakaf', 'yasin', 'korban', 'bersuci', 'd娲捐赠', 'maintenance', 'charity'];
    private $sources = ['cash', 'online', 'transfer', 'qr'];

    public function definition()
    {
        return [
            'user_id' => null,
            'amount' => $this->faker->numberBetween(5, 500),
            'category' => $this->faker->randomElement($this->categories),
            'source' => $this->faker->randomElement($this->sources),
            'description' => $this->faker->sentence(),
            'donation_date' => $this->faker->dateTimeBetween('-24 months', 'now'),
        ];
    }

    public function small()
    {
        return $this->state(fn () => [
            'amount' => $this->faker->numberBetween(5, 50),
        ]);
    }

    public function medium()
    {
        return $this->state(fn () => [
            'amount' => $this->faker->numberBetween(51, 200),
        ]);
    }

    public function large()
    {
        return $this->state(fn () => [
            'amount' => $this->faker->numberBetween(201, 2000),
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Donation $donation) {
            if (!$donation->user_id && $this->faker->boolean(70)) {
                $user = User::inRandomOrder()->first();
                if ($user) {
                    $donation->user_id = $user->id;
                    $donation->save();
                }
            }
        });
    }
}