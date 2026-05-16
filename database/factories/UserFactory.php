<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $firstNames = ['Ahmad', 'Muhammad', 'Ali', 'Hassan', 'Omar', 'Yusuf', 'Ibrahim', 'Ismail', 'Abdullah', 'Khalid', 'Faris', 'Danial', 'Aiman', 'Hazim', 'Irwan', 'Syamil', 'Naim', 'Azri', 'Fitri', 'Rafiq', 'Haiqal', 'Syarif', 'Zul', 'Amir', 'Aisyah', 'Nurul', 'Fatimah', 'Siti', 'Mariam', 'Hidayah', 'Nadia', 'Intan', 'Sofia', 'Amira', 'Nur', 'Izzah', 'Dhia', 'Ain', 'Syifa', 'Lina', 'Atiqah', 'Dayana', 'Nadilla', 'Syasya', 'Wani', 'Illa', 'Rina', 'Liza'];
        $lastNames = ['Bin Ahmad', 'Bin Mohammad', 'Bin Yusof', 'Bin Khalid', 'Bin Ibrahim', 'Bin Ali', 'Bin Omar', 'Binti Ahmad', 'Binti Mohammad', 'Binti Yusof', 'Binti Khalid', 'Binti Ibrahim'];

        return [
            'name' => $this->faker->randomElement($firstNames) . ' ' . $this->faker->randomElement($lastNames),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'role' => 'member',
            'phone' => $this->faker->phoneNumber(),
            'age' => $this->faker->numberBetween(18, 70),
            'address' => $this->faker->address(),
            'referred_code' => null,
            'referred_by' => null,
            'hide_from_leaderboard' => false,
        ];
    }

    public function admin()
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'email' => $this->faker->unique()->safeEmail('admin'),
        ]);
    }

    public function treasurer()
    {
        return $this->state(fn () => [
            'role' => 'treasurer',
            'email' => $this->faker->unique()->safeEmail('treasurer'),
        ]);
    }

    public function member()
    {
        return $this->state(fn () => [
            'role' => 'member',
        ]);
    }

    public function unverified()
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $user->referred_code = 'REF' . strtoupper(Str::random(6));
            $user->save();
        });
    }
}