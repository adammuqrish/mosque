<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::create([
            'title' => 'Gotong Royong Masjid',
            'description' => 'Membersihkan kawasan masjid untuk sambutan Aidilfitri.',
            'event_date' => now()->addDays(7), // 7 hari dari sekarang
            'location' => 'Perkarangan Masjid',
            'required_skills' => json_encode(['Cleaning', 'Gardening']), // JSON format
            'max_volunteers' => 20,
        ]);

        Event::create([
            'title' => 'Kenduri Arwah',
            'description' => 'Memerlukan sukarelawan untuk membantu menyediakan makanan.',
            'event_date' => now()->addDays(14), // 14 hari dari sekarang
            'location' => 'Dewan Serbaguna',
            'required_skills' => json_encode(['Cooking', 'Serving']), // JSON format
            'max_volunteers' => 10,
        ]);
    }
}