<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Donation;
use App\Models\Event;
use App\Models\VolunteerProfile;
use App\Models\MemberPoints;
use App\Models\PointTransaction;
use App\Models\EventVolunteer;
use App\Models\Badge;
use App\Models\BadgeEarning;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\WithdrawalRequest;
use App\Models\TierMilestone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RealisticPopulateSeeder extends Seeder
{
    protected $faker;
    protected $adminIds = [];
    protected $treasurerIds = [];
    protected $memberIds = [];
    protected $eventIds = [];
    protected $batchSize = 500;

    public function run()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        try {
            DB::transaction(function () {
                $this->command->info('Starting realistic data population...');

                $this->seedRoleHierarchyUsers();
                $this->seedVolunteerProfiles();
                $this->seedReferralsAndPoints();
                $this->seedEvents();
                $this->seedEventVolunteers();
                $this->seedDonations();
                $this->seedPointTransactions();
                $this->seedBadgesAndRewards();
                $this->seedRewardRedemptions();
                $this->seedWithdrawalRequests();

                $this->command->info('Data population completed successfully!');
            });
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
            $this->command->error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    protected function seedRoleHierarchyUsers()
    {
        $this->command->info('Creating users...');

        $adminEmails = ['admin1@mosque.local', 'admin2@mosque.local', 'admin3@mosque.local'];
        foreach ($adminEmails as $index => $email) {
            $user = User::create([
                'name' => ['Admin Masjid Utama', 'Admin Operaciones', 'Admin Kewangan'][$index],
                'email' => $email,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'admin',
                'phone' => '012345000' . $index,
                'age' => rand(30, 50),
                'address' => 'Kuala Lumpur',
                'referred_code' => 'REF' . strtoupper(Str::random(6)),
                'referred_by' => null,
                'hide_from_leaderboard' => false,
            ]);
            $this->adminIds[] = $user->id;
        }

        $treasurerEmails = ['treasurer1@mosque.local', 'treasurer2@mosque.local', 'treasurer3@mosque.local', 
                         'treasurer4@mosque.local', 'treasurer5@mosque.local', 'treasurer6@mosque.local',
                         'treasurer7@mosque.local', 'treasurer8@mosque.local'];
        foreach ($treasurerEmails as $index => $email) {
            $user = User::create([
                'name' => 'Bendahari ' . ($index + 1),
                'email' => $email,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'treasurer',
                'phone' => '019876000' . $index,
                'age' => rand(25, 55),
                'address' => 'Kuala Lumpur',
                'referred_code' => 'REF' . strtoupper(Str::random(6)),
                'referred_by' => null,
                'hide_from_leaderboard' => false,
            ]);
            $this->treasurerIds[] = $user->id;
            
            MemberPoints::create([
                'user_id' => $user->id,
                'total_points' => rand(100, 500),
                'available_points' => rand(50, 200),
                'redeemed_points' => rand(0, 100),
                'current_streak' => rand(0, 10),
                'longest_streak' => rand(5, 20),
                'last_activity_date' => now()->subDays(rand(0, 30)),
            ]);
        }

        $firstNames = ['Ahmad', 'Muhammad', 'Ali', 'Hassan', 'Omar', 'Yusuf', 'Ibrahim', 'Ismail', 'Abdullah', 'Khalid', 
                      'Faris', 'Danial', 'Aiman', 'Hazim', 'Irwan', 'Syamil', 'Naim', 'Azri', 'Fitri', 'Rafiq', 
                      'Haiqal', 'Syarif', 'Zul', 'Amir', 'Aisyah', 'Nurul', 'Fatimah', 'Siti', 'Mariam', 'Hidayah', 
                      'Nadia', 'Intan', 'Sofia', 'Amira', 'Nur', 'Izzah', 'Dhia', 'Ain', 'Syifa', 'Lina'];
        $lastNames = ['Bin Ahmad', 'Bin Mohammad', 'Bin Yusof', 'Bin Khalid', 'Bin Ibrahim', 'Bin Ali', 'Bin Omar',
                      'Binti Ahmad', 'Binti Mohammad', 'Binti Yusof', 'Binti Khalid', 'Binti Ibrahim', 'Binti Ali'];

        $chunks = array_chunk($firstNames, 50);
        
        for ($i = 0; $i < 689; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $name = $firstName . ' ' . $lastNames[array_rand($lastNames)] . ' ' . ($i + 1);
            
            $user = User::create([
                'name' => $name,
                'email' => 'member' . ($i + 1) . '@mosque.local',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'member',
                'phone' => '01' . rand(20000000, 99999999),
                'age' => rand(18, 70),
                'address' => ['Kuala Lumpur', 'Petaling Jaya', 'Shah Alam', 'Klang', 'Ampang', 'Puchong', 'Subang Jaya', 'Cheras', 'Kajang', 'Bangi'][rand(0, 9)],
                'referred_code' => 'REF' . strtoupper(Str::random(6)),
                'referred_by' => null,
                'hide_from_leaderboard' => rand(1, 100) <= 5,
            ]);
            $this->memberIds[] = $user->id;

            MemberPoints::create([
                'user_id' => $user->id,
                'total_points' => rand(0, 300),
                'available_points' => rand(0, 100),
                'redeemed_points' => rand(0, 50),
                'current_streak' => rand(0, 5),
                'longest_streak' => rand(0, 10),
                'last_activity_date' => now()->subDays(rand(0, 60)),
            ]);

            if (($i + 1) % 100 === 0) {
                $this->command->info('Created ' . ($i + 1) . ' member users...');
            }
        }

        $this->command->info('Created ' . count($this->adminIds) . ' admins, ' . count($this->treasurerIds) . ' treasurers, ' . count($this->memberIds) . ' members');
    }

    protected function seedVolunteerProfiles()
    {
        $this->command->info('Creating volunteer profiles...');

        $skillOptions = ['teaching', 'cooking', 'first_aid', 'driving', 'medical', 'carpentry', 'electrical', 'plumbing', 'it', 'photography', 'music', 'sports'];
        $hobbyOptions = ['reading', 'sports', 'music', 'gardening', 'cooking', 'crafts', 'gaming', 'travel', 'volunteering'];
        $languageOptions = ['english', 'malay', 'arabic', 'chinese', 'tamil'];
        $availabilityOptions = ['weekday_morning', 'weekday_afternoon', 'weekday_evening', 'weekend_morning', 'weekend_afternoon', 'weekend_evening'];
        $locationOptions = ['Kuala Lumpur', 'Petaling Jaya', 'Shah Alam', 'Klang', 'Ampang', 'Puchong', 'Subang Jaya', 'Cheras'];

        $allUserIds = array_merge($this->treasurerIds, $this->memberIds);
        shuffle($allUserIds);
        $profileCount = min(count($allUserIds), 600);

        for ($i = 0; $i < $profileCount; $i++) {
            VolunteerProfile::create([
                'user_id' => $allUserIds[$i],
                'skills' => json_encode(array_rand(array_flip($skillOptions), rand(1, 5))),
                'hobbies' => json_encode(array_rand(array_flip($hobbyOptions), rand(1, 4))),
                'interests' => json_encode(array_rand(array_flip(['religious', 'education', 'community', 'youth', 'elderly', 'health', 'environment']), rand(1, 4))),
                'languages' => json_encode(array_rand(array_flip($languageOptions), rand(1, 3))),
                'availability' => json_encode(array_rand(array_flip($availabilityOptions), rand(1, 4))),
                'location' => $locationOptions[array_rand($locationOptions)],
                'experience' => rand(0, 20),
                'health_status' => ['good', 'excellent', 'fair'][rand(0, 2)],
                'long_term_availability' => rand(1, 100) <= 80,
            ]);

            if (($i + 1) % 100 === 0) {
                $this->command->info('Created ' . ($i + 1) . ' volunteer profiles...');
            }
        }

        $this->command->info('Created volunteer profiles for ' . $profileCount . ' users');
    }

    protected function seedReferralsAndPoints()
    {
        $this->command->info('Creating referrals...');

        $allMemberIds = array_merge($this->treasurerIds, $this->memberIds);
        $referralCount = (int)(count($allMemberIds) * 0.18);
        
        shuffle($allMemberIds);
        
        for ($i = 0; $i < $referralCount; $i++) {
            if ($i === 0) continue;
            
            $referredUserId = $allMemberIds[$i];
            $referrerIndex = rand(0, $i - 1);
            $referrerId = $allMemberIds[$referrerIndex];

            if ($referredUserId !== $referrerId) {
                $user = User::find($referredUserId);
                $user->referred_by = $referrerId;
                $user->save();

                $referrerPoints = MemberPoints::where('user_id', $referrerId)->first();
                if ($referrerPoints) {
                    $oldBalance = $referrerPoints->available_points;
                    $referrerPoints->increment('available_points', 15);
                    $referrerPoints->increment('total_points', 15);
                    $referrerPoints->refresh();
                    
                    PointTransaction::create([
                        'user_id' => $referrerId,
                        'type' => 'earned',
                        'points' => 15,
                        'balance_after' => $oldBalance + 15,
                        'reason' => 'Referral bonus - new member joined',
                        'source_type' => 'referral',
                        'source_id' => $referredUserId,
                        'admin_id' => null,
                        'admin_notes' => null,
                    ]);
                }
            }

            if (($i + 1) % 50 === 0) {
                $this->command->info('Created ' . ($i + 1) . ' referrals...');
            }
        }

        $this->command->info('Created ' . $referralCount . ' referrals with referral points');
    }

    protected function seedEvents()
    {
        $this->command->info('Creating events...');

        $titles = [
            'Quran Reading Session', 'Tahfiz Class', 'Islamic Studies', 'Community Cleaning',
            'Food Bank Distribution', 'Elderly Visit', 'Orphan Visit', 'Friday Prayer Setup',
            'Ramadan Iftar Prep', 'Aidilfitri Cleanup', 'Zakat Collection Drive', 'Charity Sale',
            'Kids Islamic Camp', 'Youth Leadership Camp', 'Elderly Care Visit', 'Mosque Maintenance',
            'Garden Cleanup', 'Book Donation', 'Blood Drive', 'Health Checkup', 'Marriage Clinic',
            'Parenting Workshop', 'Financial Planning', 'Haji Preparation', 'Umrah Preparation',
            'Madrasah Teaching', 'Youth Sports Day', 'Charity Run', 'Fundraising Event',
            ' Mosque Open Day', 'Community Outreach', 'Interfaith Dialogue'
        ];

        $locations = [
            'Masjid Al-Hidayah', 'Masjid Al-Mukarramah', 'Masjid Nurul Yaqin',
            'Masjid Jamek', 'Community Hall', 'Youth Center', 'Elderly Home', 'Orphanage'
        ];

        $gamificationCategories = ['education', 'community', 'religious', 'charity', 'maintenance', 'youth', 'elderly'];
        
        $startDate = Carbon::now()->subMonths(24);
        $endDate = Carbon::now()->addMonths(3);
        
        for ($i = 0; $i < 120; $i++) {
            $eventDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
            $isPast = $eventDate->isPast();
            
            $skillsOptions = ['teaching', 'cooking', 'first_aid', 'driving', 'medical'];
            $hobbiesOptions = ['reading', 'sports', 'music', 'gardening', 'cooking'];
            
            $numSkills = rand(1, 3);
            $numHobbies = rand(1, 2);
            
            $selectedSkills = array_slice(array_keys(array_flip($skillsOptions)), 0, $numSkills);
            $selectedHobbies = array_slice(array_keys(array_flip($hobbiesOptions)), 0, $numHobbies);
            
            $event = Event::create([
                'title' => $titles[$i % count($titles)] . ' ' . ($i + 1),
                'description' => 'This is a volunteer event for mosque community activities.',
                'event_date' => $eventDate,
                'location' => $locations[$i % count($locations)],
                'max_volunteers' => rand(5, 30),
                'required_skills' => json_encode($selectedSkills),
                'required_hobbies' => json_encode($selectedHobbies),
                'required_languages' => json_encode(['malay', 'english']),
                'status' => $isPast ? 'closed' : 'open',
                'gamification_category' => $gamificationCategories[$i % count($gamificationCategories)],
            ]);
            
            $this->eventIds[] = $event->id;

            if (($i + 1) % 20 === 0) {
                $this->command->info('Created ' . ($i + 1) . ' events...');
            }
        }

        $this->command->info('Created ' . count($this->eventIds) . ' events');
    }

    protected function seedEventVolunteers()
    {
        $this->command->info('Creating event volunteer records...');

        $allUserIds = array_merge($this->treasurerIds, $this->memberIds);
        $volunteerRecords = [];
        
        foreach ($this->eventIds as $eventId) {
            $event = Event::find($eventId);
            if (!$event) continue;
            
            $isPast = $event->event_date->isPast();
            $volunteerCount = rand(3, min($event->max_volunteers, 20));
            
            $shuffledUsers = $allUserIds;
            shuffle($shuffledUsers);
            $selectedUsers = array_slice($shuffledUsers, 0, $volunteerCount);
            
            foreach ($selectedUsers as $userId) {
                if ($isPast) {
                    $attendance = rand(1, 100) <= 85 ? 'completed' : 'absent';
                    $points = $attendance === 'completed' ? rand(20, 80) : 0;
                } else {
                    $attendance = 'confirmed';
                    $points = 0;
                }

                $volunteerRecords[] = [
                    'event_id' => $eventId,
                    'user_id' => $userId,
                    'status' => $attendance,
                    'attendance_status' => $attendance,
                    'absence_reason' => $attendance === 'absent' ? ['sick', 'emergency', 'family_matter'][rand(0, 2)] : null,
                    'points_awarded' => $points > 0 ? 1 : 0,
                    'points_earned' => $points,
                    'joined_at' => $event->event_date->subDays(rand(1, 30)),
                ];

                if ($points > 0) {
                    $pointsRecord = MemberPoints::where('user_id', $userId)->first();
                    if ($pointsRecord) {
                        $oldAvailable = $pointsRecord->available_points;
                        $oldTotal = $pointsRecord->total_points;
                        
                        $pointsRecord->increment('available_points', $points);
                        $pointsRecord->increment('total_points', $points);
                        $pointsRecord->refresh();

                        PointTransaction::create([
                            'user_id' => $userId,
                            'type' => 'earned',
                            'points' => $points,
                            'balance_after' => $oldTotal + $points,
                            'reason' => 'Event attendance: ' . $event->title,
                            'source_type' => 'event',
                            'source_id' => $eventId,
                            'admin_id' => null,
                            'admin_notes' => null,
                        ]);
                    }
                }
            }

            if (count($volunteerRecords) >= $this->batchSize) {
                EventVolunteer::insert($volunteerRecords);
                $volunteerRecords = [];
            }
        }

        if (count($volunteerRecords) > 0) {
            EventVolunteer::insert($volunteerRecords);
        }

        $this->command->info('Created event volunteer records with points');
    }

    protected function seedDonations()
    {
        $this->command->info('Creating donations...');

        $categories = ['zakat', 'sedekah', 'wakaf', 'yasin', 'korban', 'bersuci', 'd娲捐赠', 'maintenance', 'charity'];
        $sources = ['cash', 'online'];

        $donationRecords = [];
        $startDate = Carbon::now()->subMonths(24);
        
        for ($i = 0; $i < 4500; $i++) {
            $amountRandom = rand(1, 100);
            if ($amountRandom <= 60) {
                $amount = rand(5, 50);
            } elseif ($amountRandom <= 90) {
                $amount = rand(51, 200);
} else {
                $amount = rand(201, 2000);
            }

            // All donations must have a user_id (NOT NULL due to constrained())
            $userId = $this->memberIds[array_rand($this->memberIds)];

            $donationDate = Carbon::createFromTimestamp(rand($startDate->timestamp, Carbon::now()->timestamp));

            $donationRecords[] = [
                'user_id' => $userId,
                'amount' => $amount,
                'category' => $categories[array_rand($categories)],
                'source' => $sources[array_rand($sources)],
                'description' => 'Donation for mosque activities',
                'donation_date' => $donationDate,
                'created_at' => $donationDate,
                'updated_at' => $donationDate,
            ];

            if (count($donationRecords) >= $this->batchSize) {
                Donation::insert($donationRecords);
                $donationRecords = [];
                $this->command->info('Inserted batch of donations...');
            }
        }

        if (count($donationRecords) > 0) {
            Donation::insert($donationRecords);
        }

        $this->command->info('Created 4500 donations');
    }

    protected function seedPointTransactions()
    {
        $this->command->info('Creating additional point transactions...');

        $allUserIds = array_merge($this->treasurerIds, $this->memberIds);
        
        $transactionRecords = [];
        $types = ['earned', 'redeemed', 'adjusted'];
        
        for ($i = 0; $i < 12000; $i++) {
            $userId = $allUserIds[array_rand($allUserIds)];
            $type = $types[array_rand($types)];
            
            $points = $type === 'redeemed' ? -rand(10, 150) : rand(10, 100);
            
            $pointsRecord = MemberPoints::where('user_id', $userId)->first();
            $balanceAfter = $pointsRecord ? $pointsRecord->total_points : 0;

            if ($type === 'earned') {
                $reason = ['Event attendance', 'Referral bonus', 'Badge unlock', 'Streak bonus'][rand(0, 3)];
            } elseif ($type === 'redeemed') {
                $reason = ['Reward redemption', 'Prize claim'][rand(0, 1)];
            } else {
                $reason = 'Admin adjustment';
            }

            $transactionRecords[] = [
                'user_id' => $userId,
                'type' => $type,
                'points' => $points,
                'balance_after' => $balanceAfter,
                'reason' => $reason,
                'source_type' => ['event', 'badge', 'referral', 'reward'][rand(0, 3)],
                'source_id' => rand(1, 200),
                'admin_id' => $type === 'adjusted' ? $this->adminIds[array_rand($this->adminIds)] : null,
                'admin_notes' => $type === 'adjusted' ? 'Performance bonus' : null,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 365)),
            ];

            if (count($transactionRecords) >= $this->batchSize) {
                PointTransaction::insert($transactionRecords);
                $transactionRecords = [];
            }
        }

        if (count($transactionRecords) > 0) {
            PointTransaction::insert($transactionRecords);
        }

        $this->command->info('Created additional point transactions');
    }

    protected function seedBadgesAndRewards()
    {
        $this->command->info('Creating badge earnings...');

        $badges = Badge::all();
        if ($badges->isEmpty()) {
            $this->command->warn('No badges found, skipping badge earnings');
            return;
        }

        $allUserIds = array_merge($this->treasurerIds, $this->memberIds);
        
        $earnings = [];
        
        foreach ($allUserIds as $userId) {
            $numBadges = rand(0, min($badges->count(), 12));
            $userBadges = $badges->random($numBadges);
            
            foreach ($userBadges as $badge) {
                $earnings[] = [
                    'user_id' => $userId,
                    'badge_id' => $badge->id,
                    'earned_at' => now()->subDays(rand(0, 365)),
                    'source_event_id' => rand(1, 120),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $pointsRecord = MemberPoints::where('user_id', $userId)->first();
                if ($pointsRecord && $badge->points_awarded > 0) {
                    $oldTotal = $pointsRecord->total_points;
                    $oldAvailable = $pointsRecord->available_points;
                    
                    $pointsRecord->increment('total_points', $badge->points_awarded);
                    $pointsRecord->increment('available_points', $badge->points_awarded);
                    $pointsRecord->refresh();

                    PointTransaction::create([
                        'user_id' => $userId,
                        'type' => 'earned',
                        'points' => $badge->points_awarded,
                        'balance_after' => $oldTotal + $badge->points_awarded,
                        'reason' => 'Badge unlock: ' . $badge->name,
                        'source_type' => 'badge',
                        'source_id' => $badge->id,
                        'admin_id' => null,
                        'admin_notes' => null,
                    ]);
                }
            }

            if (count($earnings) >= $this->batchSize) {
                BadgeEarning::insert($earnings);
                $earnings = [];
            }
        }

        if (count($earnings) > 0) {
            BadgeEarning::insert($earnings);
        }

        $this->command->info('Created badge earnings');
    }

    protected function seedRewardRedemptions()
    {
        $this->command->info('Creating reward redemptions...');

        $rewards = Reward::all();
        if ($rewards->isEmpty()) {
            $this->command->warn('No rewards found, skipping redemptions');
            return;
        }

        $allUserIds = array_merge($this->treasurerIds, $this->memberIds);
        
        $redemptionRecords = [];
        
        for ($i = 0; $i < 300; $i++) {
            $userId = $allUserIds[array_rand($allUserIds)];
            $reward = $rewards->random();
            $status = ['pending', 'claimed', 'rejected'][rand(0, 2)];
            
            $redemptionRecords[] = [
                'user_id' => $userId,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_cost,
                'status' => $status,
                'redeemed_at' => now()->subDays(rand(0, 180)),
                'claimed_at' => $status === 'claimed' ? now()->subDays(rand(0, 30)) : null,
                'expires_at' => now()->addDays(rand(30, 90)),
                'claim_code' => strtoupper(Str::random(8)),
                'fulfillment_notes' => $status === 'claimed' ? 'Fulfilled' : null,
                'fulfilled_by' => $status === 'claimed' ? $this->adminIds[array_rand($this->adminIds)] : null,
                'fulfilled_at' => $status === 'claimed' ? now()->subDays(rand(0, 30)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($redemptionRecords) >= $this->batchSize) {
                RewardRedemption::insert($redemptionRecords);
                $redemptionRecords = [];
            }
        }

        if (count($redemptionRecords) > 0) {
            RewardRedemption::insert($redemptionRecords);
        }

        $this->command->info('Created reward redemptions');
    }

    protected function seedWithdrawalRequests()
    {
        $this->command->info('Creating withdrawal requests...');

        $purposes = ['masjid_maintenance', 'charity', 'events', 'utilities', 'equipment', 'renovation', 'community_programs'];
        
        $requestRecords = [];
        
        for ($i = 0; $i < 150; $i++) {
            $statusRand = rand(1, 100);
            if ($statusRand <= 50) {
                $status = 'pending';
                $approvedBy = null;
                $approvedAt = null;
                $rejectionReason = null;
            } elseif ($statusRand <= 80) {
                $status = 'approved';
                $approvedBy = $this->adminIds[array_rand($this->adminIds)];
                $approvedAt = now()->subDays(rand(1, 180));
                $rejectionReason = null;
            } else {
                $status = 'rejected';
                $approvedBy = null;
                $approvedAt = null;
                $rejectionReason = ['insufficient_funds', 'lack_of_documentation', 'not_approved_by_committee', 'duplicate_request'][rand(0, 3)];
            }

            $requestRecords[] = [
                'requested_by' => $this->treasurerIds[array_rand($this->treasurerIds)],
                'amount' => rand(100, 5000),
                'purpose' => $purposes[array_rand($purposes)],
                'status' => $status,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'rejection_reason' => $rejectionReason,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 180)),
            ];
        }

        WithdrawalRequest::insert($requestRecords);

        $this->command->info('Created 150 withdrawal requests');
    }
}