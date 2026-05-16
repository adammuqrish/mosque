<?php

namespace Database\Factories;

use App\Models\VolunteerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerProfileFactory extends Factory
{
    protected $model = VolunteerProfile::class;

    private $skillOptions = ['teaching', 'cooking', 'first_aid', 'driving', 'medical', 'carpentry', 'electrical', 'plumbing', 'it', 'photography', 'music', 'sports'];
    private $hobbyOptions = ['reading', 'sports', 'music', 'gardening', 'cooking', 'crafts', 'gaming', 'travel', 'volunteering'];
    private $languageOptions = ['english', 'malay', 'arabic', 'chinese', 'tamil', 'indonesian'];
    private $availabilityOptions = ['weekday_morning', 'weekday_afternoon', 'weekday_evening', 'weekend_morning', 'weekend_afternoon', 'weekend_evening', 'friday_night', 'saturday_night'];
    private $locationOptions = ['Kuala Lumpur', 'Petaling Jaya', 'Shah Alam', 'Klang', 'Ampang', 'Puchong', 'Subang', 'Cheras', ' Kajang', 'Bangi'];

    public function definition()
    {
        return [
            'user_id' => null,
            'skills' => json_encode($this->faker->randomElements($this->skillOptions, $this->faker->numberBetween(1, 5))),
            'hobbies' => json_encode($this->faker->randomElements($this->hobbyOptions, $this->faker->numberBetween(1, 4))),
            'interests' => json_encode($this->faker->randomElements(['religious', 'education', 'community', 'youth', 'elderly', 'health', 'environment'], $this->faker->numberBetween(1, 4))),
            'languages' => json_encode($this->faker->randomElements($this->languageOptions, $this->faker->numberBetween(1, 3))),
            'availability' => json_encode($this->faker->randomElements($this->availabilityOptions, $this->faker->numberBetween(1, 4))),
            'location' => $this->faker->randomElement($this->locationOptions),
            'experience' => $this->faker->numberBetween(0, 20),
            'health_status' => $this->faker->randomElement(['good', 'excellent', 'fair']),
            'long_term_availability' => $this->faker->boolean(80),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (VolunteerProfile $profile) {
            if (!$profile->user_id) {
                $user = User::whereDoesntHave('volunteerProfile')->inRandomOrder()->first();
                if ($user) {
                    $profile->user_id = $user->id;
                    $profile->save();
                }
            }
        });
    }
}