<?php

namespace App\Console\Commands;

use App\Models\Badge;
use App\Models\Donation;
use App\Models\Event;
use App\Models\EventVolunteer;
use App\Models\FundPurpose;
use App\Models\MemberPoints;
use App\Models\Reward;
use App\Models\TierMilestone;
use App\Models\User;
use App\Models\VolunteerProfile;
use App\Models\WithdrawalDocument;
use App\Models\WithdrawalRequest;
use App\Models\ZakatAkad;
use App\Services\GamificationService;
use App\Services\ReceiptNumberService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Interactive per-category bulk-insert.
 *
 * Run with no arguments to get a guided menu, or pass the category and
 * the count directly:
 *
 *   php artisan mosque:bulk-insert
 *   php artisan mosque:bulk-insert users --count=10
 *   php artisan mosque:bulk-insert donations --count=20 --sub-category=zakat
 *   php artisan mosque:bulk-insert events --count=5 --sub-category=religious
 *   php artisan mosque:bulk-insert all --count=5
 */
class InsertBulkSamples extends Command
{
    protected $signature = 'mosque:bulk-insert
                            {category? : users|events|event-volunteers|donations|zakat-akads|withdrawals|reward-redemptions|badge-earnings|referrals|points|all}
                            {--count=5 : How many records to create per sub-category}
                            {--sub-category= : Optional sub-category filter (e.g. zakat, religious, sadaqah)}
                            {--no-confirm : Skip the preview confirmation prompt}';

    protected $description = 'Insert realistic sample data one category at a time, with sub-category filtering.';

    /**
     * Maps CLI category → handler method on this command.
     */
    private const CATEGORY_HANDLERS = [
        'users'              => 'handleUsers',
        'events'             => 'handleEvents',
        'event-volunteers'   => 'handleEventVolunteers',
        'donations'          => 'handleDonations',
        'bulk-sadaqah'       => 'handleBulkSadaqah',
        'zakat-akads'        => 'handleZakatAkads',
        'withdrawals'        => 'handleWithdrawals',
        'reward-redemptions' => 'handleRewardRedemptions',
        'badge-earnings'     => 'handleBadgeEarnings',
        'referrals'          => 'handleReferrals',
        'points'             => 'handlePoints',
        'all'                => 'handleAll',
    ];

    public function handle(): int
    {
        $category = (string) $this->argument('category');

        if ($category === '' || $category === 'menu') {
            return $this->showMenu();
        }

        if (! isset(self::CATEGORY_HANDLERS[$category])) {
            $this->error("Unknown category '{$category}'.");
            $this->line('Valid: '.implode(', ', array_keys(self::CATEGORY_HANDLERS)));
            return 1;
        }

        $count = max(1, (int) $this->option('count'));
        $subCategory = $this->option('sub-category') ?: null;

        $this->info("Mosque bulk insert — category: {$category}, count: {$count}".($subCategory ? ", sub-category: {$subCategory}" : ''));
        $this->newLine();

        if (! $this->option('no-confirm') && ! $this->confirm('Proceed?', true)) {
            $this->warn('Aborted.');
            return 0;
        }

        $method = self::CATEGORY_HANDLERS[$category];
        return $this->{$method}($count, $subCategory);
    }

    private function showMenu(): int
    {
        $this->info('Mosque Bulk Insert — choose a category:');
        $this->newLine();

        $rows = [
            ['users',              'Create member/treasurer/admin accounts'],
            ['events',             'Create volunteer events'],
            ['event-volunteers',   'Attach members/treasurers to events'],
            ['donations',          'Record donations (use --sub-category for zakat/sadaqah/waqf/zakat_fitr)'],
            ['bulk-sadaqah',       'Anonymous sadaqah from collection boxes (witnesses in description)'],
            ['zakat-akads',        'Attach Zakat Akad records to confirmed zakat donations'],
            ['withdrawals',        'Create withdrawal requests (use --sub-category for type)'],
            ['reward-redemptions', 'Spend member points on rewards'],
            ['badge-earnings',     'Award badges to members'],
            ['referrals',          'Link members to referrers (awards 15 points)'],
            ['points',             'Manually award admin-driven points (use --sub-category for source reason)'],
            ['all',                'Run all of the above with the same --count'],
        ];

        $this->table(['Category', 'Description'], $rows);
        $this->newLine();
        $this->line('Example: php artisan mosque:bulk-insert donations --count=10 --sub-category=zakat');
        return 0;
    }

    // -----------------------------------------------------------------
    // users
    // -----------------------------------------------------------------
    private function handleUsers(int $count, ?string $sub): int
    {
        $role = $sub ?: $this->choice('Which role?', ['admin', 'treasurer', 'member'], 'member');
        $created = 0;

        $firstNames = ['Aminah', 'Badrul', 'Farah', 'Hakim', 'Izzati', 'Jasmin', 'Khalid', 'Liyana', 'Mizan', 'Nadia', 'Omar', 'Puteri'];
        $lastNames  = ['Binti Ahmad', 'Bin Yusof', 'Binti Khalid', 'Bin Ibrahim', 'Binti Hassan', 'Bin Ali'];

        for ($i = 0; $i < $count; $i++) {
            $first = $firstNames[array_rand($firstNames)];
            $last  = $lastNames[array_rand($lastNames)];
            $gender = str_starts_with($last, 'Binti') ? 'F' : 'M';

            $user = User::create([
                'name'              => $first.' '.$last,
                'email'             => Str::lower(Str::slug($first)).'.'.Str::random(4).'@mosque.test',
                'password'          => Hash::make('password'),
                'role'              => $role,
                'phone'             => '01'.random_int(20000000, 99999999),
                'age'               => random_int(20, 60),
                'address'           => 'No '.random_int(1, 99).', Jalan '.Str::title($first).', Kuala Lumpur',
                'email_verified_at' => now(),
                'referred_code'     => $this->uniqueReferralCode(),
                'is_amil'           => $role === 'member' && $i === 0,
            ]);

            MemberPoints::firstOrCreate(['user_id' => $user->id]);
            $created++;
        }

        $this->info("Created {$created} {$role} user(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // events
    // -----------------------------------------------------------------
    private function handleEvents(int $count, ?string $sub): int
    {
        $categories = \App\Models\Event::CATEGORIES;
        $category = $sub ?: $this->choice('Which event category?', $categories, $categories[0]);

        $titles = [
            'religious'  => ['Kuliah Maghrib', 'Tadarus Al-Quran', 'Ceramah Jumaat'],
            'charity'    => ['Bantuan Banjir', 'Food Bank Run', 'Back-to-School Drive'],
            'education'  => ['Kelas Fardhu Ain', 'Tuisyen Percuma', 'Bengkel ICT'],
            'community'  => ['Gotong-Royong', 'Hari Keluarga', 'Kembara Anak-Anak Yatim'],
            'youth'      => ['Kem Belia', 'Leadership Camp', 'Sports Day'],
            'elderly'    => ['Lawatan Warga Emas', 'Saringan Kesihatan', 'Bengkel Kesihatan'],
            'maintenance'=> ['Pengecatan Masjid', 'Baiki Atap Bocor', 'Tukar Karpet'],
        ];

        $created = 0;
        for ($i = 0; $i < $count; $i++) {
            $title = $titles[$category][array_rand($titles[$category])];
            $start = Carbon::now()->addDays(random_int(1, 30))->setTime(random_int(8, 20), [0, 15, 30, 45][array_rand([0, 15, 30, 45])]);
            $end   = (clone $start)->addHours(random_int(2, 6));

            $event = Event::create([
                'title'                => $title.' - '.\Illuminate\Support\Str::random(4),
                'description'          => "Acara kategori {$category} yang memerlukan sukarelawan. Sila hubungi pihak masjid untuk maklumat lanjut.",
                'event_date'           => $start,
                'end_time'             => $end,
                'location'             => Str::slug('Masjid Al-Hasanah', ''),
                'event_location'       => Str::slug('Dewan Serbaguna', ''),
                'max_volunteers'       => random_int(8, 30),
                'required_skills'      => ['Teaching', 'Cooking', 'First Aid'],
                'required_hobbies'     => ['Reading', 'Sports'],
                'required_languages'   => ['Malay', 'English'],
                'status'               => 'open',
                'gamification_category'=> $category,
            ]);
            $created++;
        }

        $this->info("Created {$created} {$category} event(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // event-volunteers
    // -----------------------------------------------------------------
    private function handleEventVolunteers(int $count, ?string $sub): int
    {
        $events = Event::where('status', 'open')->get();
        $users  = User::whereIn('role', ['member', 'treasurer'])->get();

        if ($events->isEmpty() || $users->isEmpty()) {
            $this->error('Need at least one open event and one member/treasurer.');
            return 1;
        }

        $gamification = app(GamificationService::class);
        $created = 0;

        foreach ($events->take($count) as $event) {
            $picked = $users->shuffle()->take(random_int(2, 5));

            foreach ($picked as $user) {
                if (EventVolunteer::where('event_id', $event->id)->where('user_id', $user->id)->exists()) {
                    continue;
                }

                $isPast = $event->event_date->isPast();
                $pivot = EventVolunteer::create([
                    'event_id'          => $event->id,
                    'user_id'           => $user->id,
                    'status'            => $isPast ? 'completed' : 'confirmed',
                    'attendance_status' => $isPast ? 'completed' : 'confirmed',
                    'points_awarded'    => false,
                ]);
                $pivot->joined_at = $event->event_date->copy()->subDays(random_int(1, 14));
                $pivot->save();

                if ($isPast) {
                    $gamification->awardPointsForEventCompletion($pivot);
                }
                $created++;
            }
        }

        $this->info("Created {$created} event volunteer records.");
        return 0;
    }

    // -----------------------------------------------------------------
    // donations
    // -----------------------------------------------------------------
    private function handleDonations(int $count, ?string $sub): int
    {
        $categories = ['zakat', 'zakat_fitr', 'sadaqah', 'waqf'];
        $category = $sub ?: $this->choice('Which donation category?', $categories, 'sadaqah');
        if (! in_array($category, $categories, true)) {
            $this->error("Invalid donation category: {$category}");
            return 1;
        }

        $users = User::whereIn('role', ['member', 'treasurer', 'admin'])->get();
        if ($users->isEmpty()) {
            $this->error('No members/treasurers/admins to attach donations to.');
            return 1;
        }

        $receiptService = app(ReceiptNumberService::class);
        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $first = ['Ahmad', 'Siti', 'Muhammad', 'Nurul', 'Hassan', 'Aisyah'][array_rand(['Ahmad', 'Siti', 'Muhammad', 'Nurul', 'Hassan', 'Aisyah'])];
            $last  = ['Bin Ali', 'Binti Ahmad', 'Bin Yusof', 'Binti Salleh'][array_rand(['Bin Ali', 'Binti Ahmad', 'Bin Yusof', 'Binti Salleh'])];
            $gender = str_contains($last, 'Binti') ? 'F' : 'M';
            $amount = $this->realisticAmount($category, $i);
            $isZakat = in_array($category, ['zakat', 'zakat_fitr'], true);
            // Donor identity is required only for zakat/zakat_fitr/waqf.
            // Sadaqah is anonymous (the form has no donor_* fields at all).
            $requiresDonorInfo = in_array($category, ['zakat', 'zakat_fitr', 'waqf'], true);

            $donation = Donation::create([
                'user_id'       => $user->id,
                'donor_name'    => $requiresDonorInfo ? $first.' '.$last : null,
                'donor_ic'      => $requiresDonorInfo ? $this->buildMyKad($i, $gender) : null,
                'donor_phone'   => $requiresDonorInfo ? '01'.random_int(20000000, 99999999) : null,
                'donor_email'   => $requiresDonorInfo ? Str::lower($first).'@example.my' : null,
                'donor_address' => $requiresDonorInfo ? 'No '.random_int(1, 50).', Jalan '.Str::title($first).', Kuala Lumpur' : null,
                'amount'        => $amount,
                'category'      => $category,
                'type'          => match ($category) {
                    'zakat', 'zakat_fitr' => 'obligatory',
                    'waqf'                => 'endowment',
                    default               => 'voluntary',
                },
                'fund_purpose'  => $isZakat ? 'General Fund' : 'General Fund',
                'asnaf_category'=> $isZakat ? 'faqir' : null,
                'source'        => $i % 2 === 0 ? 'cash' : 'online',
                'status'        => $i === 0 ? 'pending' : 'confirmed',
                'donation_date' => Carbon::now()->subDays(random_int(1, 60)),
                'description'   => $requiresDonorInfo
                    ? "Sumbangan {$category} RM ".number_format($amount, 2)." oleh {$first}."
                    : "Sumbangan ikhlas RM ".number_format($amount, 2)." untuk tabung masjid (anonymous sadaqah).",
            ]);

            if ($donation->status === 'confirmed') {
                $verifier = User::where('role', 'treasurer')->inRandomOrder()->first();
                $donation->update([
                    'verified_by' => $verifier?->id,
                    'verified_at' => $donation->donation_date,
                ]);
            }

            $donation->update(['receipt_number' => $receiptService->nextDonationReceiptNumber()]);
            $created++;
        }

        $this->info("Created {$created} {$category} donation(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // bulk-sadaqah (collection-box sadaqah with witnesses)
    // -----------------------------------------------------------------
    private function handleBulkSadaqah(int $count, ?string $sub): int
    {
        $users = User::whereIn('role', ['member', 'treasurer', 'admin'])->get();
        if ($users->isEmpty()) {
            $this->error('No users to attribute the collection event to.');
            return 1;
        }

        $treasurers = User::where('role', 'treasurer')->get();
        $receiptService = app(ReceiptNumberService::class);

        $witnesses = [
            'Encik Razali Bin Ahmad',  'Puan Salwa Bt Mohd Nor',  'Ustaz Khairul Anwar',
            'Haji Faisal Abd Rahman',  'Cik Noraini Bt Salleh',   'Encik Hafiz Bin Ibrahim',
            'Puan Khadijah Bt Ismail', 'Ustazah Nurul Ain',       'Encik Muhammad bin Abdullah',
        ];
        $purposes = ['General Fund', 'Kipas Gergasi', 'Aircond', 'Karpet Baru', 'Education'];

        $created = 0;
        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $witness = $witnesses[array_rand($witnesses)];
            $purpose = $purposes[$i % count($purposes)];
            $amount = match (true) {
                $i % 3 === 0 => random_int(20, 80),    // small collection box
                $i % 3 === 1 => random_int(80, 200),   // medium
                default      => random_int(200, 500),  // large
            };

            $notes = 'Kutipan Pukal — Saksi: '.$witness.' (dana: '.$purpose.')';

            $donation = Donation::create([
                'user_id'       => $user->id,
                'donor_name'    => null,
                'donor_ic'      => null,
                'donor_phone'   => null,
                'donor_email'   => null,
                'donor_address' => null,
                'amount'        => $amount,
                'category'      => 'sadaqah',
                'type'          => 'voluntary',
                'fund_purpose'  => $purpose,
                'asnaf_category'=> null,
                'source'        => 'cash',
                'status'        => 'confirmed',
                'donation_date' => Carbon::now()->subDays(random_int(1, 30)),
                'receipt_number'=> $receiptService->nextDonationReceiptNumber(),
                'description'   => $notes,
            ]);

            $verifier = $treasurers->isNotEmpty() ? $treasurers->random() : null;
            $donation->update([
                'verified_by' => $verifier?->id,
                'verified_at' => $donation->donation_date,
            ]);
            $created++;
        }

        $this->info("Created {$created} bulk sadaqah collection event(s) with witnesses.");
        return 0;
    }

    // -----------------------------------------------------------------
    // zakat-akads
    // -----------------------------------------------------------------
    private function handleZakatAkads(int $count, ?string $sub): int
    {
        $zakatDonations = Donation::whereIn('category', ['zakat', 'zakat_fitr'])
            ->where('status', 'confirmed')
            ->whereDoesntHave('zakatAkad')
            ->orderBy('id')
            ->limit($count)
            ->get();

        if ($zakatDonations->isEmpty()) {
            $this->error('No confirmed zakat donations awaiting an Akad.');
            return 1;
        }

        $amils = User::where('is_amil', true)->get();
        if ($amils->isEmpty()) {
            $this->error('No amils (users.is_amil=true) available.');
            return 1;
        }

        $receiptService = app(ReceiptNumberService::class);
        foreach ($zakatDonations as $i => $donation) {
            $amil = $amils[$i % $amils->count()];
            ZakatAkad::create([
                'donation_id'  => $donation->id,
                'reference'    => $receiptService->nextAkadReference(),
                'muzakki_name' => $donation->donor_name,
                'muzakki_ic'   => $donation->donor_ic,
                'amil_name'    => $amil->name,
                'amil_user_id' => $amil->id,
                'akad_date'    => $donation->donation_date,
                'amount'       => $donation->amount,
                'notes'        => 'Auto-generated akad for '.$donation->category,
            ]);
        }

        $this->info("Created {$zakatDonations->count()} Zakat Akad(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // withdrawals
    // -----------------------------------------------------------------
    private function handleWithdrawals(int $count, ?string $sub): int
    {
        $types = ['zakat', 'zakat_fitr', 'sadaqah', 'waqf'];
        $type = $sub ?: $this->choice('Which withdrawal type?', $types, 'sadaqah');
        if (! in_array($type, $types, true)) {
            $this->error("Invalid withdrawal type: {$type}");
            return 1;
        }

        $balances = $this->availableBalanceByType();
        $available = $balances[$type] ?? 0;

        $treasurers = User::where('role', 'treasurer')->get();
        if ($treasurers->count() < 2) {
            $this->error('Need at least 2 treasurers (one to request, one to approve).');
            return 1;
        }

        $fundPurposes = FundPurpose::active()->ordered()->pluck('name')->toArray() ?: ['General Fund'];
        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            $amount = match (true) {
                $available < 200  => max(50, $available / ($count + 1)),
                $i % 3 === 0      => random_int(100, 800),
                $i % 3 === 1      => random_int(800, 1500),
                default           => random_int(1500, 3000),
            };
            $amount = min($amount, $available);
            if ($amount < 50) {
                $this->warn("Skipping #{$i}: insufficient balance (RM ".number_format($available, 2).")");
                continue;
            }

            $requester = $treasurers[0];
            $approver  = $treasurers[1];

            $wr = WithdrawalRequest::create([
                'requested_by' => $requester->id,
                'type'         => $type,
                'fund_purpose' => $fundPurposes[$i % count($fundPurposes)],
                'amount'       => round($amount, 2),
                'purpose'      => "Auto-generated withdrawal #{$i} for {$type}.",
                'status'       => $amount >= 1000 ? 'maker_checked' : 'pending',
            ]);

            if ($wr->status === 'maker_checked') {
                $wr->update([
                    'maker_checked_by' => $approver->id,
                    'maker_checked_at' => now(),
                ]);
            }
            $created++;
        }

        $this->info("Created {$created} {$type} withdrawal request(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // reward-redemptions
    // -----------------------------------------------------------------
    private function handleRewardRedemptions(int $count, ?string $sub): int
    {
        $rewards = Reward::where('is_active', true)->get();
        if ($rewards->isEmpty()) {
            $this->error('No active rewards in the catalog.');
            return 1;
        }

        $members = User::where('role', 'member')->get();
        $gamification = app(GamificationService::class);
        $created = 0;

        foreach ($members as $member) {
            if ($created >= $count) break;

            $available = $gamification->getAvailablePoints($member);
            $affordable = $rewards->filter(fn ($r) => $r->points_cost <= $available && $r->isAvailable());

            if ($affordable->isEmpty()) continue;

            $result = $gamification->redeemReward($member, $affordable->random());
            if ($result['status'] === 'success') {
                $created++;
            }
        }

        $this->info("Created {$created} reward redemption(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // badge-earnings
    // -----------------------------------------------------------------
    private function handleBadgeEarnings(int $count, ?string $sub): int
    {
        $members = User::where('role', 'member')->get();
        $badges = Badge::where('is_active', true)->get();
        if ($members->isEmpty() || $badges->isEmpty()) {
            $this->error('Need at least one member and one active badge.');
            return 1;
        }

        $created = 0;
        foreach ($members as $member) {
            if ($created >= $count) break;

            $already = $member->badgeEarnings()->pluck('badge_id')->toArray();
            $candidate = $badges->whereNotIn('id', $already)->shuffle()->first();
            if (! $candidate) continue;

            $earning = BadgeEarning::create([
                'user_id'         => $member->id,
                'badge_id'        => $candidate->id,
                'earned_at'       => now(),
                'source_event_id' => null,
            ]);

            if ($candidate->points_awarded > 0) {
                $mp = MemberPoints::firstOrCreate(['user_id' => $member->id]);
                $mp->total_points += $candidate->points_awarded;
                $mp->available_points += $candidate->points_awarded;
                $mp->save();

                PointTransaction::create([
                    'user_id'       => $member->id,
                    'type'          => 'earned',
                    'points'        => $candidate->points_awarded,
                    'balance_after' => $mp->total_points,
                    'reason'        => "Badge earned: {$candidate->name}",
                    'source_type'   => 'badge',
                    'source_id'     => $candidate->id,
                ]);
            }
            $created++;
        }

        $this->info("Created {$created} badge earning(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // referrals
    // -----------------------------------------------------------------
    private function handleReferrals(int $count, ?string $sub): int
    {
        $members = User::where('role', 'member')->whereNull('referred_by')->get();
        $referrers = User::whereIn('role', ['member', 'treasurer'])->get();

        if ($members->isEmpty() || $referrers->isEmpty()) {
            $this->error('No unreferred members or no available referrers.');
            return 1;
        }

        $gamification = app(GamificationService::class);
        $created = 0;
        $members = $members->shuffle();

        foreach ($members as $member) {
            if ($created >= $count) break;

            $candidates = $referrers->where('id', '!=', $member->id);
            $referrer = $candidates->random();
            $gamification->processReferral($referrer, $member);
            $created++;
        }

        $this->info("Processed {$created} referral(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // points (manual admin adjustment)
    // -----------------------------------------------------------------
    private function handlePoints(int $count, ?string $sub): int
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            $this->error('No admin user to attribute the adjustment to.');
            return 1;
        }

        $reason = $sub ?: 'Manual bonus (admin adjustment)';
        $members = User::where('role', 'member')->inRandomOrder()->limit($count)->get();
        $created = 0;

        foreach ($members as $member) {
            $points = random_int(20, 200);
            $mp = MemberPoints::firstOrCreate(['user_id' => $member->id]);
            $mp->total_points += $points;
            $mp->available_points += $points;
            $mp->save();

            PointTransaction::create([
                'user_id'       => $member->id,
                'type'          => 'earned',
                'points'        => $points,
                'balance_after' => $mp->total_points,
                'reason'        => $reason,
                'source_type'   => 'admin_adjustment',
                'admin_id'      => $admin->id,
                'admin_notes'   => $reason,
            ]);
            $created++;
        }

        $this->info("Created {$created} manual point adjustment(s).");
        return 0;
    }

    // -----------------------------------------------------------------
    // all
    // -----------------------------------------------------------------
    private function handleAll(int $count, ?string $sub): int
    {
        $this->warn('Running every category with --count='.$count);
        foreach (['users', 'events', 'event-volunteers', 'donations', 'bulk-sadaqah', 'zakat-akads', 'withdrawals', 'reward-redemptions', 'badge-earnings', 'referrals', 'points'] as $cat) {
            $this->newLine();
            $this->info("--- {$cat} ---");
            $this->call('mosque:bulk-insert', [
                'category'   => $cat,
                '--count'    => $count,
                '--no-confirm' => true,
            ]);
        }
        return 0;
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    private function uniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referred_code', $code)->exists());
        return $code;
    }

    private function buildMyKad(int $seed, ?string $gender = null): string
    {
        $yy = str_pad((string) (70 + ($seed * 7) % 30), 2, '0', STR_PAD_LEFT);
        $mm = str_pad((string) ((($seed * 3) % 12) + 1), 2, '0', STR_PAD_LEFT);
        $dd = str_pad((string) ((($seed * 11) % 27) + 1), 2, '0', STR_PAD_LEFT);
        $pb = str_pad((string) (10 + ($seed * 5) % 13), 2, '0', STR_PAD_LEFT);
        $last = str_pad((string) (1000 + ($seed * 17) % 8999), 4, '0', STR_PAD_LEFT);
        return "{$yy}{$mm}{$dd}-{$pb}-{$last}";
    }

    private function realisticAmount(string $category, int $i): float
    {
        $roll = ($i * 11 + crc32($category)) % 100;
        return match ($category) {
            'zakat_fitr' => match (true) {
                $roll < 60 => (float) (7 * (($i % 4) + 1)),
                $roll < 90 => (float) (35 * (($i % 3) + 1)),
                default    => (float) (70 * (($i % 3) + 1)),
            },
            'waqf'       => match (true) {
                $roll < 60 => (float) (100 + $i * 50),
                $roll < 90 => (float) (500 + $i * 200),
                default    => (float) (2000 + $i * 750),
            },
            default      => match (true) {
                $roll < 60 => (float) (5 + $i * 7),
                $roll < 90 => (float) (50 + $i * 25),
                default    => (float) (250 + $i * 150),
            },
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
