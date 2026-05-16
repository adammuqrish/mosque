<?php

namespace App\Console\Commands;

use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Console\Command;

class SeedMosqueRewards extends Command
{
    protected $signature = 'mosque:seed-rewards {--force}';

    protected $description = 'Reset and seed mosque-appropriate rewards catalog';

    private const REWARDS = [
        // Facilities
        [
            'code' => 'PRIORITY_PARKING_FRI',
            'name' => 'Priority Parking (Friday)',
            'name_my' => 'Tempat Letak Kereta Keutamaan (Jumaat)',
            'description' => 'Reserved parking spot near the mosque entrance during Friday prayers.',
            'description_my' => 'Tempat letak kenderaan berhampiran pintu masjid semasa solat Jumaat.',
            'category' => 'facilities',
            'points_cost' => 50,
            'is_active' => true,
        ],
        [
            'code' => 'PRIORITY_EVENT_REG',
            'name' => 'Priority Event Registration',
            'name_my' => 'Pendaftaran Acara Keutamaan',
            'description' => 'Early access to register for popular mosque events.',
            'description_my' => 'Akses awal untuk mendaftar acara masjid popular.',
            'category' => 'facilities',
            'points_cost' => 75,
            'is_active' => true,
        ],
        [
            'code' => 'FACILITY_BOOKING',
            'name' => 'Family Facility Booking',
            'name_my' => 'Tempahan Kemudahan Keluarga',
            'description' => 'Book mosque facilities (hall, kitchen) for family events.',
            'description_my' => 'Tempah kemudahan masjid (dewan, dapur) untuk acara keluarga.',
            'category' => 'facilities',
            'points_cost' => 200,
            'is_active' => true,
        ],

        // Recognition
        [
            'code' => 'APPRECIATION_CERT',
            'name' => 'Certificate of Appreciation',
            'name_my' => 'Sijil Penghargaan',
            'description' => 'Formal certificate recognizing your volunteer contributions.',
            'description_my' => 'Sijil rasmi mengiktiraf sumbangan sukarela anda.',
            'category' => 'recognition',
            'points_cost' => 200,
            'is_active' => true,
        ],
        [
            'code' => 'APPRECIATION_BOARD',
            'name' => 'Name on Mosque Appreciation Board',
            'name_my' => 'Nama di Papan Penghargaan Masjid',
            'description' => 'Your name displayed on the mosque appreciation board.',
            'description_my' => 'Nama anda dipaparkan di papan penghargaan masjid.',
            'category' => 'recognition',
            'points_cost' => 300,
            'is_active' => true,
        ],

        // Events
        [
            'code' => 'FREE_IFTAR_RAMADAN',
            'name' => 'Free Iftar Meal (Ramadan)',
            'name_my' => 'Makanan Berbuka Percuma (Ramadan)',
            'description' => 'Complimentary iftar meal during Ramadan.',
            'description_my' => 'Makanan berbuka percuma semasa Ramadan.',
            'category' => 'events',
            'points_cost' => 250,
            'is_active' => true,
        ],

        // Merchandise - Common
        [
            'code' => 'KEYCHAIN',
            'name' => 'Mosque Keychain',
            'name_my' => 'Rantai Kunci Masjid',
            'description' => 'Custom mosque-branded keychain.',
            'description_my' => 'Rantai kunci berjenama masjid.',
            'category' => 'merchandise_common',
            'points_cost' => 30,
            'is_active' => true,
        ],
        [
            'code' => 'STICKER_PACK',
            'name' => 'Mosque Sticker Pack',
            'name_my' => 'Pelekat Masjid',
            'description' => 'Set of mosque-themed stickers.',
            'description_my' => 'Set pelekat bertema masjid.',
            'category' => 'merchandise_common',
            'points_cost' => 40,
            'is_active' => true,
        ],
        [
            'code' => 'TOTE_BAG',
            'name' => 'Mosque Tote Bag',
            'name_my' => 'Beg Tote Masjid',
            'description' => 'Reusable tote bag with mosque logo.',
            'description_my' => 'Beg tote boleh guna semula dengan logo masjid.',
            'category' => 'merchandise_common',
            'points_cost' => 100,
            'is_active' => true,
        ],
        [
            'code' => 'TSHIRT',
            'name' => 'Mosque T-shirt',
            'name_my' => 'Kemeja-T Masjid',
            'description' => 'Mosque-branded t-shirt.',
            'description_my' => 'Kemeja-T berjenama masjid.',
            'category' => 'merchandise_common',
            'points_cost' => 150,
            'is_active' => true,
        ],

        // Merchandise - Limited Edition
        [
            'code' => 'TUMBLER',
            'name' => 'Mosque Tumbler',
            'name_my' => 'Tumbler Masjid',
            'description' => 'Limited edition mosque tumbler.',
            'description_my' => 'Tumbler masjid edisi terhad.',
            'category' => 'merchandise_limited',
            'points_cost' => 175,
            'is_active' => true,
        ],
        [
            'code' => 'KOPIAH',
            'name' => 'Mosque Kopiah (Embroidered)',
            'name_my' => 'Kopiah Masjid (Sulaman)',
            'description' => 'Premium embroidered kopiah with mosque emblem.',
            'description_my' => 'Kopiah bersulam premium dengan lambang masjid.',
            'category' => 'merchandise_limited',
            'points_cost' => 250,
            'is_active' => true,
        ],
        [
            'code' => 'PRAYER_MAT',
            'name' => 'Limited Edition Prayer Mat',
            'name_my' => 'Sejadah Edisi Terhad',
            'description' => 'High-quality prayer mat with exclusive mosque design.',
            'description_my' => 'Sejadah berkualiti tinggi dengan reka bentuk eksklusif masjid.',
            'category' => 'merchandise_limited',
            'points_cost' => 350,
            'is_active' => true,
        ],
        [
            'code' => 'COMMEMORATIVE_PLAQUE',
            'name' => 'Commemorative Plaque',
            'name_my' => 'Plak Peringatan',
            'description' => 'Premium plaque honoring outstanding volunteer contributions.',
            'description_my' => 'Plak peringatan menghormati sumbangan sukarela cemerlang.',
            'category' => 'merchandise_limited',
            'points_cost' => 500,
            'is_active' => true,
        ],
    ];

    public function handle()
    {
        $force = $this->option('force');

        if (!$force) {
            $this->warn('This will delete ALL existing reward redemptions and rewards.');
            if (!$this->confirm('Continue?')) {
                $this->info('Cancelled.');
                return 0;
            }
        }

        $this->info('Clearing existing dummy data...');

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        RewardRedemption::truncate();
        $this->line('  Cleared reward_redemptions table.');

        Reward::truncate();
        $this->line('  Cleared rewards table.');

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Seeding new mosque rewards catalog...');

        foreach (self::REWARDS as $data) {
            Reward::create($data);
            $this->line("  Added: {$data['name']} ({$data['points_cost']} pts)");
        }

        $this->info('');
        $this->info('Seeded ' . count(self::REWARDS) . ' rewards successfully.');
        $this->info('Visit /gamification/rewards to see the new catalog.');

        return 0;
    }
}
