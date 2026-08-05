<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\BadgeEarning;
use App\Models\Donation;
use App\Models\Event;
use App\Models\EventVolunteer;
use App\Models\FundPurpose;
use App\Models\MemberPoints;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\TierMilestone;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Models\WithdrawalDocument;
use App\Models\WithdrawalRequest;
use App\Models\ZakatAkad;
use App\Services\GamificationService;
use App\Services\ReceiptNumberService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Curated, realistic sample data — 5 records per category.
 *
 * Every record looks like it was created by a real Malaysian user:
 * real names, real addresses in KL/Selangor, valid Malaysian MyKad
 * numbers, sensible donation amounts (60% small, 30% medium, 10%
 * large), and amount decisions that respect the live balance rules
 * enforced by WithdrawalController (i.e. a withdrawal cannot exceed
 * confirmed donations of the matching type).
 *
 * Inserts are funnelled through the same service layer that the HTTP
 * controllers use, so receipt numbers, encrypted donor fields, and
 * gamification side effects (points, badges, tier updates) are all
 * generated exactly as they would be for a real user.
 */
class CuratedSampleSeeder extends Seeder
{
    /**
     * Records-per-category cap. The user asked for exactly 5.
     */
    public const PER_CATEGORY = 5;

    /**
     * Hand-picked, contextually coherent personas.
     * [first_name, last_name, gender, age, address, phone]
     */
    private const ADMIN_PERSONAS = [
        ['Ahmad',         'Bin Hassan',     'M', 45, 'No 12, Jalan Sultan Ismail, Kuala Lumpur', '0123450101'],
        ['Siti Norhaliza','Binti Yusof',    'F', 41, 'No 4, Jalan Bangsar Utama, Kuala Lumpur',  '0123450102'],
        ['Muhammad',     'Bin Abdullah',    'M', 52, 'No 22, Jalan Gombak, Kuala Lumpur',        '0123450103'],
        ['Khadijah',     'Binti Ismail',    'F', 38, 'No 7, Jalan Wangsa Maju, Kuala Lumpur',    '0123450104'],
        ['Iskandar',     'Bin Mohd Salleh', 'M', 47, 'No 18, Jalan Setiawangsa, Kuala Lumpur',   '0123450105'],
    ];

    private const TREASURER_PERSONAS = [
        ['Razali',        'Bin Ahmad',         'M', 39, 'No 33, Jalan SS21/34, Petaling Jaya',    '0198760201'],
        ['Noraini',       'Binti Salleh',      'F', 36, 'No 9, Jalan USJ 9/5, Subang Jaya',       '0198760202'],
        ['Hafiz',         'Bin Ibrahim',       'M', 44, 'No 14, Jalan Kota Kemuning, Shah Alam',  '0198760203'],
        ['Salwa',         'Binti Mohd Nor',    'F', 33, 'No 27, Jalan Bandar Baru Bangi, Bangi',  '0198760204'],
        ['Faisal',        'Bin Abd Rahman',    'M', 50, 'No 5, Jalan Meru, Klang',                '0198760205'],
    ];

    private const MEMBER_PERSONAS = [
        // name, gender, age, address, phone, is_amil
        ['Amirul',        'Bin Mohd Razali',     'M', 28, 'No 11, Jalan PJU 5/20, Petaling Jaya',     '0111110301', true],
        ['Nurul Ain',     'Binti Tarmizi',       'F', 26, 'No 8, Jalan Alam Sutera, Shah Alam',       '0111110302', true],
        ['Danial Haqim',  'Bin Khairul Anwar',   'M', 24, 'No 3, Jalan Pandan Indah, Kuala Lumpur',   '0111110303', false],
        ['Syasya Nadia',  'Binti Roslan',        'F', 30, 'No 17, Jalan Bukit Jalil, Kuala Lumpur',   '0111110304', false],
        ['Luqman Hakim',  'Bin Suhaimi',         'M', 35, 'No 21, Jalan Puchong Utama, Puchong',      '0111110305', false],
    ];

    private const LOCATIONS = [
        ['Masjid Al-Hasanah',         'Dewan Serbaguna'],
        ['Masjid Al-Hidayah',         'Bilik Kuliah Utama'],
        ['Masjid An-Nur',             'Perkarangan Masjid'],
        ['Masjid Ar-Rahman',          'Dewan Komuniti'],
        ['Masjid As-Salam',           'Ruang Solat Utama'],
    ];

    private const SKILLS = ['Teaching', 'Cooking', 'Cleaning', 'First Aid', 'Driving', 'Photography', 'IT Support', 'Public Speaking'];
    private const HOBBIES = ['Reading', 'Sports', 'Gardening', 'Cooking', 'Music', 'Crafts', 'Cycling'];
    private const LANGUAGES = ['Malay', 'English', 'Arabic', 'Tamil', 'Chinese'];

    private const SADAQAH_PURPOSES = ['General Fund', 'Kipas Gergasi', 'Aircond', 'Karpet Baru', 'Education'];
    private const ZAKAT_ASNAF = ['faqir', 'miskin', 'amil', 'mualaf', 'fisabilillah'];

    private array $admins = [];
    private array $treasurers = [];
    private array $members = [];
    private array $events = [];

    public function run(): void
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        DB::transaction(function () {
            $this->command->info('Curated sample data population — 5 records per category');
            $this->command->info('=========================================================');

            $this->seedAdmins();
            $this->seedTreasurers();
            $this->seedMembers();
            $this->seedVolunteerProfiles();
            $this->seedEvents();
            $this->seedEventVolunteers();
            $this->seedDonations();
            $this->seedZakatAkads();
            $this->seedReferrals();
            $this->seedRewardRedemptions();
            $this->seedWithdrawals();

            $this->command->info('');
            $this->command->info('Done. Counts:');
            $this->command->info('  Admins:           '.count($this->admins));
            $this->command->info('  Treasurers:       '.count($this->treasurers));
            $this->command->info('  Members:          '.count($this->members));
            $this->command->info('  Events:           '.count($this->events));
        });
    }

    // -----------------------------------------------------------------
    // 1. Admins
    // -----------------------------------------------------------------
    private function seedAdmins(): void
    {
        $this->command->info('Seeding 5 admins...');

        foreach (self::ADMIN_PERSONAS as $i => [$first, $last, $gender, $age, $address, $phone]) {
            $genderPrefix = $gender === 'F' ? 'P' : 'L';
            $ic = $this->buildMyKad($i + 1);

            $user = User::create([
                'name'              => $first.' '.$last,
                'email'             => 'admin.'.Str::lower(Str::slug($first)).'@mosque.test',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'phone'             => $phone,
                'age'               => $age,
                'address'           => $address,
                'email_verified_at' => now(),
                'referred_code'     => $this->uniqueReferralCode(),
                'is_amil'           => false,
            ]);

            $this->admins[] = $user;

            $this->ensureMemberPoints($user->id);
        }
    }

    // -----------------------------------------------------------------
    // 2. Treasurers
    // -----------------------------------------------------------------
    private function seedTreasurers(): void
    {
        $this->command->info('Seeding 5 treasurers...');

        foreach (self::TREASURER_PERSONAS as $i => [$first, $last, $gender, $age, $address, $phone]) {
            $user = User::create([
                'name'              => $first.' '.$last,
                'email'             => 'treasurer.'.Str::lower(Str::slug($first)).'@mosque.test',
                'password'          => Hash::make('password'),
                'role'              => 'treasurer',
                'phone'             => $phone,
                'age'               => $age,
                'address'           => $address,
                'email_verified_at' => now(),
                'referred_code'     => $this->uniqueReferralCode(),
                'is_amil'           => false,
            ]);

            $this->treasurers[] = $user;

            // Treasurers get a healthy starting balance so they can sponsor their
            // own volunteer events in the demo.
            $this->ensureMemberPoints($user->id, total: 120, available: 120, redeemed: 0, streak: 2, longest: 4, lastActivityDaysAgo: 3);
        }
    }

    // -----------------------------------------------------------------
    // 3. Members (2 of the 5 are amils)
    // -----------------------------------------------------------------
    private function seedMembers(): void
    {
        $this->command->info('Seeding 5 members (2 of whom are amils)...');

        foreach (self::MEMBER_PERSONAS as $i => [$first, $last, $gender, $age, $address, $phone, $isAmil]) {
            $user = User::create([
                'name'              => $first.' '.$last,
                'email'             => 'member.'.Str::lower(Str::slug($first)).'@mosque.test',
                'password'          => Hash::make('password'),
                'role'              => 'member',
                'phone'             => $phone,
                'age'               => $age,
                'address'           => $address,
                'email_verified_at' => now(),
                'referred_code'     => $this->uniqueReferralCode(),
                'is_amil'           => $isAmil,
            ]);

            $this->members[] = $user;

            $this->ensureMemberPoints($user->id);
        }
    }

    // -----------------------------------------------------------------
    // 4. Volunteer profiles (5 — one per member)
    // -----------------------------------------------------------------
    private function seedVolunteerProfiles(): void
    {
        $this->command->info('Seeding 5 volunteer profiles...');

        $interests = ['religious', 'community', 'youth', 'education', 'elderly'];
        $experiences = [
            'Befriending new Muslims at the mosque since 2020.',
            'Helped run the mosque food bank every weekend for 3 years.',
            'Taught Quran classes to children for the past 2 years.',
            'Active with the elderly visitation programme.',
            'Co-organised last year\'s gotong-royong with 40 volunteers.',
        ];

        foreach ($this->members as $i => $member) {
            VolunteerProfile::create([
                'user_id'                => $member->id,
                'skills'                 => $this->pickN(self::SKILLS, 3 + ($i % 2)),
                'availability'           => $this->pickN(['weekday_evening', 'weekend_morning', 'weekend_afternoon', 'weekday_morning'], 2 + ($i % 2)),
                'hobbies'                => $this->pickN(self::HOBBIES, 2 + ($i % 2)),
                'interests'              => $this->pickN($interests, 2 + ($i % 2)),
                'languages'              => $this->pickN(self::LANGUAGES, 2 + ($i % 2)),
                'experience'             => $experiences[$i] ?? 'Active member of the mosque community.',
                'location'               => $this->extractCity($member->address),
                'health_status'          => ['good', 'excellent'][$i % 2],
                'long_term_availability' => $i % 2 === 0
                    ? 'Available for long-term volunteer programmes'
                    : 'Available for short ad-hoc events',
            ]);
        }
    }

    // -----------------------------------------------------------------
    // 5. Events (5 — one per main category)
    //
    // 2 events are in the past (so we can complete attendance and award
    // points); 3 are upcoming (so the system shows them as "open" with
    // confirmed volunteers).
    // -----------------------------------------------------------------
    private function seedEvents(): void
    {
        $this->command->info('Seeding 5 events (2 past, 3 upcoming)...');

        $plan = [
            // [title, description, days_offset, duration_hours, max_volunteers, category, [location, sub]]
            [
                'Kuliah Subuh: Fiqh Solat',
                'Siri kuliah subuh setiap Ahad. Topik kali ini: Fiqh Solat Sunat Rawatib dan kesilapan lazim dalam solat fardu. Sesi soal jawab selepas kuliah.',
                -14, 1, 12, 'religious', [0, 0],
            ],
            [
                'Gotong-Royong Perdana Aidilfitri',
                'Membersihkan seluruh kawasan masjid, dewan serbaguna, tempat wuduk, dan kawasan parking untuk persediaan Aidilfitri. Sarapan pagi dan makan tengahari disediakan.',
                -7, 4, 20, 'maintenance', [0, 1],
            ],
            [
                'Bantuan Banjir Pantai Timur',
                'Mengumpulkan, menyusun, dan menghantar barang bantuan ke pusat pemindahan banjir di Kuantan. Termasuk barangan dapur, tilam, dan pakaian.',
                5, 6, 25, 'charity', [0, 2],
            ],
            [
                'Kelas Fardhu Ain Kanak-Kanak',
                'Kelas mingguan untuk kanak-kanak berusia 7-12 tahun. Sukarelawan membantu sebagai fasilitator dan pengawas. Sila bawa telekung sendiri untuk guru perempuan.',
                12, 3, 8, 'education', [0, 3],
            ],
            [
                'Program Kembara Anak-Anak Yatim',
                'Membawa 30 anak yatim ke Zoo Negara. Termasuk pengangkutan, makan tengahari, dan beg cenderamata. Penuh semangat dan kesabaran diperlukan.',
                21, 8, 15, 'community', [0, 4],
            ],
        ];

        foreach ($plan as [$title, $description, $dayOffset, $hours, $max, $category, $locIdx]) {
            $start = Carbon::now()->addDays($dayOffset)->setTime(8, 30);
            $end = (clone $start)->addHours($hours);
            [$location, $sub] = self::LOCATIONS[$locIdx[0]];

            $event = Event::create([
                'title'                => $title,
                'description'          => $description,
                'event_date'           => $start,
                'end_time'             => $end,
                'location'             => Str::lower(Str::slug($location, '')),
                'event_location'       => Str::lower(Str::slug($sub, '')),
                'max_volunteers'       => $max,
                'required_skills'      => $this->pickN(self::SKILLS, 2),
                'required_hobbies'     => $this->pickN(self::HOBBIES, 2),
                'required_languages'   => $this->pickN(self::LANGUAGES, 2),
                'health_requirement'   => null,
                'status'               => $dayOffset < 0 ? 'closed' : 'open',
                'gamification_category'=> $category,
            ]);

            $this->events[] = $event;
        }
    }

    // -----------------------------------------------------------------
    // 6. Event volunteers (5 per event = 25 total)
    //
    // For past events we mark them "completed" and route through the
    // GamificationService so points, badges, and transactions are all
    // generated exactly as they would be in production.
    //
    // For future events we just attach them with the "confirmed"
    // attendance status.
    // -----------------------------------------------------------------
    private function seedEventVolunteers(): void
    {
        $this->command->info('Seeding event volunteers and awarding points for past events...');

        $gamification = app(GamificationService::class);
        $volunteerPool = array_merge($this->members, $this->treasurers);

        foreach ($this->events as $event) {
            $shuffled = $volunteerPool;
            shuffle($shuffled);
            $volunteersForEvent = array_slice($shuffled, 0, self::PER_CATEGORY);
            $isPast = $event->event_date->isPast();

            foreach ($volunteersForEvent as $user) {
                $joinedAt = $isPast
                    ? $event->event_date->copy()->subDays(rand(2, 14))
                    : Carbon::now()->subDays(rand(0, 3));

                $pivot = EventVolunteer::create([
                    'event_id'          => $event->id,
                    'user_id'           => $user->id,
                    'status'            => $isPast ? 'completed' : 'confirmed',
                    'attendance_status' => $isPast ? 'completed' : 'confirmed',
                    'points_awarded'    => false,
                    'points_earned'     => 0,
                ]);
                $pivot->joined_at = $joinedAt;
                $pivot->save();

                if ($isPast) {
                    // Service-layer: updates points, writes a
                    // point_transactions row, and may award badges.
                    $gamification->awardPointsForEventCompletion($pivot);
                }
            }
        }
    }

    // -----------------------------------------------------------------
    // 7. Donations (5 per category × 4 categories = 20 total)
    //
    // Every donation goes through the model mutators so the encrypted
    // donor fields (IC, phone, address) are persisted correctly, and
    // the receipt number comes from ReceiptNumberService.
    // -----------------------------------------------------------------
    private function seedDonations(): void
    {
        $this->command->info('Seeding 20 donations (5 per category)...');

        $receiptService = app(ReceiptNumberService::class);

        // 5 per category
        $categories = [
            'zakat'     => self::PER_CATEGORY,
            'zakat_fitr'=> self::PER_CATEGORY,
            'sadaqah'   => self::PER_CATEGORY,
            'waqf'      => self::PER_CATEGORY,
        ];

        $allUsers = array_merge($this->members, $this->treasurers, $this->admins);
        $amils = User::where('is_amil', true)->get();

        $donorPool = self::MEMBER_PERSONAS;
        $donorIndex = 0;

        foreach ($categories as $category => $count) {
            for ($i = 0; $i < $count; $i++) {
                $donorUser = $allUsers[$donorIndex % count($allUsers)];
                $donorIndex++;

                // Use a different donor profile (not the user themselves)
                // so the relationship looks organic.
                [$first, $last, $gender, $age, $address, $phone] = $donorPool[$donorIndex % count($donorPool)];
                $ic = $this->buildMyKad($donorIndex + 1, $gender);

                $isZakat = in_array($category, ['zakat', 'zakat_fitr'], true);
                // Donor identity is required only for zakat/zakat_fitr/waqf.
                // Sadaqah (single-donor path) is intentionally anonymous — the
                // bulk-sadaqah / collection-box path doesn't even have donor_*
                // fields in BulkDonationRequest.
                $requiresDonorInfo = in_array($category, ['zakat', 'zakat_fitr', 'waqf'], true);
                $amount = $this->realisticDonationAmount($category, $i);

                $donation = Donation::create([
                    'user_id'       => $donorUser->id,
                    'donor_name'    => $requiresDonorInfo ? $first.' '.$last : null,
                    'donor_ic'      => $requiresDonorInfo ? $ic : null,
                    'donor_phone'   => $requiresDonorInfo ? $this->alternatePhone($phone, $i) : null,
                    'donor_email'   => $requiresDonorInfo ? Str::lower(Str::slug($first)).'@example.my' : null,
                    'donor_address' => $requiresDonorInfo ? $address : null,
                    'amount'        => $amount,
                    'category'      => $category,
                    'type'          => $this->donationType($category),
                    'fund_purpose'  => $isZakat ? 'General Fund' : self::SADAQAH_PURPOSES[$i % count(self::SADAQAH_PURPOSES)],
                    'asnaf_category'=> $isZakat ? self::ZAKAT_ASNAF[$i % count(self::ZAKAT_ASNAF)] : null,
                    'source'        => $i % 2 === 0 ? 'cash' : 'online',
                    'status'        => $i === 0 ? 'pending' : 'confirmed',
                    'reference'     => $i % 2 === 0 ? null : 'FPX'.str_pad((string) (100000 + $donorIndex * 7), 10, '0', STR_PAD_LEFT),
                    'donation_date' => Carbon::now()->subDays(rand(1, 90)),
                    'description'   => $this->realisticDescription($category, $requiresDonorInfo ? $first : null, $amount),
                ]);

                if ($donation->status === 'confirmed') {
                    $verifier = $this->treasurers[array_rand($this->treasurers)];
                    $donation->update([
                        'verified_by' => $verifier->id,
                        'verified_at' => $donation->donation_date,
                    ]);
                }

                // Receipt number is generated by the service so it is
                // globally unique across the year.
                $donation->update([
                    'receipt_number' => $receiptService->nextDonationReceiptNumber(),
                ]);
            }
        }
    }

    // -----------------------------------------------------------------
    // 8. Zakat Akads (5 — one per zakat/zakat_fitr donation)
    // -----------------------------------------------------------------
    private function seedZakatAkads(): void
    {
        $this->command->info('Seeding 5 Zakat Akads (one per zakat donation batch)...');

        $zakatDonations = Donation::whereIn('category', ['zakat', 'zakat_fitr'])
            ->where('status', 'confirmed')
            ->orderBy('id')
            ->limit(self::PER_CATEGORY)
            ->get();

        if ($zakatDonations->isEmpty()) {
            $this->command->warn('  No confirmed zakat donations found, skipping akads.');
            return;
        }

        $amils = User::where('is_amil', true)->get();
        if ($amils->isEmpty()) {
            $this->command->warn('  No amils available, skipping akads.');
            return;
        }

        $receiptService = app(ReceiptNumberService::class);

        foreach ($zakatDonations as $i => $donation) {
            $amil = $amils[$i % $amils->count()];

            ZakatAkad::create([
                'donation_id'   => $donation->id,
                'reference'     => $receiptService->nextAkadReference(),
                'muzakki_name'  => $donation->donor_name,
                'muzakki_ic'    => $donation->donor_ic,
                'amil_name'     => $amil->name,
                'amil_user_id'  => $amil->id,
                'akad_date'     => $donation->donation_date,
                'amount'        => $donation->amount,
                'notes'         => 'Akad zakat '.$donation->category.' - '.Carbon::parse($donation->donation_date)->format('M Y'),
            ]);
        }
    }

    // -----------------------------------------------------------------
    // 9. Referrals (5 — each member is referred by another user)
    // -----------------------------------------------------------------
    private function seedReferrals(): void
    {
        $this->command->info('Seeding 5 referrals...');

        $referrers = array_merge($this->members, $this->treasurers);
        $gamification = app(GamificationService::class);

        foreach ($this->members as $i => $member) {
            // Pick a referrer who is NOT this member.
            $candidates = array_filter($referrers, fn ($u) => $u->id !== $member->id);
            $referrer = $candidates[array_rand($candidates)];

            // Skip if the member is already referred (idempotent).
            if ($member->referred_by) {
                continue;
            }

            $gamification->processReferral($referrer, $member);
        }
    }

    // -----------------------------------------------------------------
    // 10. Reward redemptions (5 — only members with enough points)
    // -----------------------------------------------------------------
    private function seedRewardRedemptions(): void
    {
        $this->command->info('Seeding 5 reward redemptions...');

        $rewards = Reward::where('is_active', true)
            ->orderBy('points_cost')
            ->get();

        if ($rewards->isEmpty()) {
            $this->command->warn('  No active rewards, skipping redemptions.');
            return;
        }

        $gamification = app(GamificationService::class);
        $count = 0;

        foreach ($this->members as $member) {
            if ($count >= self::PER_CATEGORY) {
                break;
            }

            $available = $gamification->getAvailablePoints($member);
            $affordable = $rewards->filter(fn ($r) => $r->points_cost <= $available && $r->isAvailable());

            if ($affordable->isEmpty()) {
                continue;
            }

            $reward = $affordable->random();
            $result = $gamification->redeemReward($member, $reward);

            if ($result['status'] === 'success') {
                $count++;
            }
        }

        $this->command->info("  Created {$count} reward redemptions.");
    }

    // -----------------------------------------------------------------
    // 11. Withdrawals (5 — one per type, respecting live balance rules)
    // -----------------------------------------------------------------
    private function seedWithdrawals(): void
    {
        $this->command->info('Seeding 5 withdrawal requests...');

        $fundPurposes = FundPurpose::active()->ordered()->pluck('name')->toArray();
        if (empty($fundPurposes)) {
            $fundPurposes = ['General Fund'];
        }

        $availableByType = $this->availableBalanceByType();
        $plan = [
            ['type' => 'zakat',      'amount' => 800,   'status' => 'approved',     'fund_purpose' => 'General Fund',     'purpose' => 'Bantuan kecemasan kepada asnaf fakir dan miskin.'],
            ['type' => 'zakat_fitr', 'amount' => 300,   'status' => 'pending',      'fund_purpose' => 'General Fund',     'purpose' => 'Beras zakat fitrah untuk diedarkan sebelum solat Aidilfitri.'],
            ['type' => 'sadaqah',    'amount' => 450,   'status' => 'approved',     'fund_purpose' => 'Kipas Gergasi',    'purpose' => 'Pembelian 2 unit kipas gergasi untuk dewan solat utama.'],
            ['type' => 'sadaqah',    'amount' => 1500,  'status' => 'maker_checked','fund_purpose' => 'Aircond',          'purpose' => 'Pembaikan sistem pendingin hawa di bilik kuliah.'],
            ['type' => 'waqf',       'amount' => 1200,  'status' => 'approved',     'fund_purpose' => 'Construction',     'purpose' => 'Peringkat pertama projek tambahan ruang wuduk.'],
        ];

        $count = 0;
        foreach ($plan as $i => $w) {
            $available = $availableByType[$w['type']] ?? 0;
            if ($w['amount'] > $available) {
                $this->command->warn("  Skipping withdrawal #{$i}: insufficient confirmed balance for type={$w['type']} (available RM ".number_format($available, 2).').');
                continue;
            }

            $requester = $this->treasurers[$i % count($this->treasurers)];
            $approver  = $this->treasurers[($i + 1) % count($this->treasurers)];

            $wr = WithdrawalRequest::create([
                'requested_by'  => $requester->id,
                'type'          => $w['type'],
                'fund_purpose'  => $w['fund_purpose'],
                'amount'        => $w['amount'],
                'purpose'       => $w['purpose'],
                'status'        => $w['status'],
            ]);

            if ($w['status'] === 'approved') {
                $wr->update([
                    'approved_by' => $approver->id,
                    'approved_at' => Carbon::now()->subDays(rand(1, 7)),
                ]);
            } elseif ($w['status'] === 'maker_checked') {
                $wr->update([
                    'maker_checked_by' => $approver->id,
                    'maker_checked_at' => Carbon::now()->subDays(rand(1, 7)),
                ]);
            }

            $count++;
        }

        $this->command->info("  Created {$count} withdrawal requests.");
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    private function ensureMemberPoints(int $userId, int $total = 0, int $available = 0, int $redeemed = 0, int $streak = 0, int $longest = 0, ?int $lastActivityDaysAgo = null): MemberPoints
    {
        return MemberPoints::firstOrCreate(
            ['user_id' => $userId],
            [
                'total_points'      => $total,
                'available_points'  => $available,
                'redeemed_points'   => $redeemed,
                'current_streak'    => $streak,
                'longest_streak'    => max($longest, $streak),
                'last_activity_date'=> $lastActivityDaysAgo === null ? null : Carbon::now()->subDays($lastActivityDaysAgo),
            ]
        );
    }

    private function pickN(array $items, int $n): array
    {
        if ($n >= count($items)) {
            return $items;
        }
        $keys = array_rand($items, $n);
        return array_map(fn ($k) => $items[$k], is_array($keys) ? $keys : [$keys]);
    }

    private function uniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referred_code', $code)->exists());

        return $code;
    }

    private function buildMyKad(int $seed, ?string $gender = null): string
    {
        // YY MM DD PB ####
        $yy = str_pad((string) (70 + ($seed % 30)), 2, '0', STR_PAD_LEFT);
        $mm = str_pad((string) ((($seed * 3) % 12) + 1), 2, '0', STR_PAD_LEFT);
        $dd = str_pad((string) ((($seed * 7) % 27) + 1), 2, '0', STR_PAD_LEFT);
        $pb = str_pad((string) (10 + ($seed % 13)), 2, '0', STR_PAD_LEFT);
        $last = str_pad((string) (1000 + ($seed * 13) % 8999), 4, '0', STR_PAD_LEFT);

        return "{$yy}{$mm}{$dd}-{$pb}-{$last}";
    }

    private function alternatePhone(string $base, int $i): string
    {
        // Keep the area code, vary the last 4 digits deterministically.
        return substr($base, 0, 7).str_pad((string) (1000 + $i * 37 % 8999), 4, '0', STR_PAD_LEFT);
    }

    private function extractCity(string $address): string
    {
        if (preg_match('/(Kuala Lumpur|Petaling Jaya|Subang Jaya|Shah Alam|Bangi|Puchong|Klang|Ampang|Cheras)/i', $address, $m)) {
            return $m[1];
        }
        return 'Kuala Lumpur';
    }

    private function realisticDonationAmount(string $category, int $i): float
    {
        // 60% small, 30% medium, 10% large — matches the live distribution.
        $roll = ($i * 17 + crc32($category)) % 100;
        if ($category === 'zakat_fitr') {
            return match (true) {
                $roll < 60 => (float) (7 * (($i % 4) + 1)),    // 7–28 (one per family member)
                $roll < 90 => (float) (35 * (($i % 3) + 1)),   // 35–105
                default    => (float) (70 * (($i % 3) + 1)),   // 70–210
            };
        }
        if ($category === 'waqf') {
            return match (true) {
                $roll < 60 => (float) (100 + $i * 50),
                $roll < 90 => (float) (500 + $i * 200),
                default    => (float) (2000 + $i * 750),
            };
        }
        return match (true) {
            $roll < 60 => (float) (5 + $i * 7),
            $roll < 90 => (float) (50 + $i * 25),
            default    => (float) (250 + $i * 150),
        };
    }

    private function donationType(string $category): string
    {
        return match ($category) {
            'zakat', 'zakat_fitr' => 'obligatory',
            'waqf'                => 'endowment',
            default               => 'voluntary',
        };
    }

    private function realisticDescription(string $category, ?string $firstName, float $amount): string
    {
        $pretty = 'RM '.number_format($amount, 2);
        $name   = $firstName ?? 'Tanpa Nama';

        return match ($category) {
            'zakat'      => "Zakat pendapatan {$name} - {$pretty} untuk diagihkan kepada asnaf yang layak.",
            'zakat_fitr' => "Zakat fitrah {$name} - {$pretty} untuk kegunaan keluarga sendiri.",
            'sadaqah'    => "Sumbangan ikhlas sebanyak {$pretty} untuk tabung masjid (dana: ".self::SADAQAH_PURPOSES[array_rand(self::SADAQAH_PURPOSES)].").",
            'waqf'       => "Wakaf tunai {$name} - {$pretty} untuk projek jangka panjang masjid.",
            default      => "Sumbangan {$name} - {$pretty}.",
        };
    }

    private function availableBalanceByType(): array
    {
        $confirmed = Donation::where('status', 'confirmed');

        $committed = WithdrawalRequest::whereIn('status', ['pending', 'maker_checked', 'approved'])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $balances = [];
        foreach (['zakat', 'zakat_fitr', 'sadaqah', 'waqf'] as $type) {
            $in = match ($type) {
                'zakat'      => (clone $confirmed)->where('category', 'zakat')->sum('amount'),
                'zakat_fitr' => (clone $confirmed)->where('category', 'zakat_fitr')->sum('amount'),
                'sadaqah'    => (clone $confirmed)->where('type', 'voluntary')->sum('amount'),
                'waqf'       => (clone $confirmed)->where('type', 'endowment')->sum('amount'),
            };
            $balances[$type] = $in - ($committed[$type] ?? 0);
        }

        return $balances;
    }
}
