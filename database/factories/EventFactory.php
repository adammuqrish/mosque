<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    private $titles = [
        'Quran Reading Session',
        'Tahfiz Class',
        'Islamic Studies',
        'Community Cleaning',
        'Food Bank Distribution',
        'Elderly Visit',
        'Orphan Visit',
        'Friday Prayer Setup',
        'Ramadan Iftar Prep',
        'Aidilfitri Cleanup',
        'Zakat Collection Drive',
        'Charity Sale',
        'Kids Islamic Camp',
        'Youth Leadership Camp',
        'Elderly Care Visit',
        'Mosque Maintenance',
        'Garden Cleanup',
        'Book Donation',
        'Blood Drive',
        'Health Checkup',
        'Marriage Clinic',
        'Parenting Workshop',
        'Financial Planning',
        'Haji Preparation',
        'Umrah Preparation',
    ];

    private $locations = [
        'Masjid Al-Hidayah',
        'Masjid Al-Mukarramah',
        'Masjid Nurul Yaqin',
        'Masjid Jamek',
        'Community Hall',
        'Youth Center',
        'Elderly Home',
        'Orphanage',
    ];

    private $gamificationCategories = ['education', 'community', 'religious', 'charity', 'maintenance', 'youth', 'elderly'];

    public function definition()
    {
        $eventDate = $this->faker->dateTimeBetween('-24 months', '+3 months');
        
        return [
            'title' => $this->faker->randomElement($this->titles) . ' ' . $this->faker->randomElement(['A', 'B', 'C', '1', '2']),
            'description' => $this->faker->paragraph(),
            'event_date' => $eventDate,
            'location' => $this->faker->randomElement($this->locations),
            'max_volunteers' => $this->faker->numberBetween(5, 50),
            'required_skills' => json_encode($this->faker->randomElements(['teaching', 'cooking', 'first_aid', 'driving', 'medical', 'carpentry', 'electrical'], $this->faker->numberBetween(0, 3))),
            'required_hobbies' => json_encode($this->faker->randomElements(['reading', 'sports', 'music', 'gardening', 'cooking', 'crafts'], $this->faker->numberBetween(0, 3))),
            'required_languages' => json_encode($this->faker->randomElements(['english', 'malay', 'arabic', 'chinese', 'tamil'], $this->faker->numberBetween(1, 3))),
            'status' => $eventDate->getTimestamp() > time() ? 'open' : 'closed',
            'gamification_category' => $this->faker->randomElement($this->gamificationCategories),
        ];
    }

    public function upcoming()
    {
        return $this->state(fn () => [
            'event_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'status' => 'open',
        ]);
    }

    public function past()
    {
        return $this->state(fn () => [
            'event_date' => $this->faker->dateTimeBetween('-24 months', 'now'),
            'status' => 'closed',
        ]);
    }

    public function cancelled()
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
        ]);
    }
}