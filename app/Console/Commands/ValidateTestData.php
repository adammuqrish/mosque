<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ValidateTestData extends Command
{
    protected $signature = 'mosque:validate-data {--verbose : Show detailed output}';
    protected $description = 'Validate test data integrity after seeding';

    public function handle()
    {
        $this->info('Validating test data...');
        
        $errors = [];
        $warnings = [];

        $this->info('Checking user counts...');
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        $treasurerCount = DB::table('users')->where('role', 'treasurer')->count();
        $memberCount = DB::table('users')->where('role', 'member')->count();
        $totalUsers = DB::table('users')->count();

        if ($adminCount < 3) $errors[] = "Expected at least 3 admins, found {$adminCount}";
        if ($treasurerCount < 8) $errors[] = "Expected at least 8 treasurers, found {$treasurerCount}";
        if ($memberCount < 600) $errors[] = "Expected at least 600 members, found {$memberCount}";
        
        $this->info("Users: {$totalUsers} (Admins: {$adminCount}, Treasurers: {$treasurerCount}, Members: {$memberCount})");

        $this->info('Checking volunteer profiles...');
        $profileCount = DB::table('volunteer_profiles')->count();
        $profilesWithUsers = DB::table('volunteer_profiles')->whereNotNull('user_id')->count();
        
        if ($profilesWithUsers < 500) $warnings[] = "Expected at least 500 volunteer profiles, found {$profilesWithUsers}";
        
        $this->info("Volunteer profiles: {$profileCount}");

        $this->info('Checking member points...');
        $pointsCount = DB::table('member_points')->count();
        
        if ($pointsCount < 600) $warnings[] = "Expected at least 600 member points records, found {$pointsCount}";
        
        $this->info("Member points records: {$pointsCount}");

        $this->info('Checking donations...');
        $donationCount = DB::table('donations')->count();
        
        if ($donationCount < 4000) $warnings[] = "Expected at least 4000 donations, found {$donationCount}";
        
        $this->info("Donations: {$donationCount}");

        $this->info('Checking events...');
        $eventCount = DB::table('events')->count();
        
        if ($eventCount < 100) $warnings[] = "Expected at least 100 events, found {$eventCount}";
        
        $this->info("Events: {$eventCount}");

        $this->info('Checking event volunteers...');
        $volunteerCount = DB::table('event_volunteer')->count();
        
        if ($volunteerCount < 1500) $warnings[] = "Expected at least 1500 event volunteer records, found {$volunteerCount}";
        
        $this->info("Event volunteers: {$volunteerCount}");

        $this->info('Checking referrals...');
        $referrals = DB::table('users')->whereNotNull('referred_by')->count();
        
        if ($referrals < 100) $warnings[] = "Expected at least 100 referrals, found {$referrals}";
        
        $this->info("Referrals: {$referrals}");

        $this->info('Checking point transactions...');
        $transactionCount = DB::table('point_transactions')->count();
        
        if ($transactionCount < 10000) $warnings[] = "Expected at least 10000 point transactions, found {$transactionCount}";
        
        $this->info("Point transactions: {$transactionCount}");

        $this->info('Checking badge earnings...');
        $badgeEarningsCount = DB::table('badge_earnings')->count();
        
        $this->info("Badge earnings: {$badgeEarningsCount}");

        $this->info('Checking reward redemptions...');
        $redemptionCount = DB::table('reward_redemptions')->count();
        
        $this->info("Reward redemptions: {$redemptionCount}");

        $this->info('Checking withdrawal requests...');
        $withdrawalCount = DB::table('withdrawal_requests')->count();
        
        $this->info("Withdrawal requests: {$withdrawalCount}");

        $this->info('Checking for orphan records...');
        
        $orphanDonations = DB::table('donations')
            ->whereNotNull('user_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'donations.user_id');
            })->count();
            
        if ($orphanDonations > 0) {
            $errors[] = "Found {$orphanDonations} orphan donations with invalid user_id";
        }

        $orphanProfiles = DB::table('volunteer_profiles')
            ->whereNotNull('user_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'volunteer_profiles.user_id');
            })->count();
            
        if ($orphanProfiles > 0) {
            $errors[] = "Found {$orphanProfiles} orphan volunteer profiles with invalid user_id";
        }

        $orphanWithdrawals = DB::table('withdrawal_requests')
            ->whereNotNull('requested_by')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'withdrawal_requests.requested_by');
            })->count();
            
        if ($orphanWithdrawals > 0) {
            $errors[] = "Found {$orphanWithdrawals} orphan withdrawal requests with invalid requested_by";
        }

        $this->info('Checking unique constraints...');
        
        $duplicateEventVolunteers = DB::table('event_volunteer')
            ->select('event_id', 'user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('event_id', 'user_id')
            ->having('count', '>', 1)
            ->count();
            
        if ($duplicateEventVolunteers > 0) {
            $errors[] = "Found {$duplicateEventVolunteers} duplicate event_volunteer records";
        }

        $this->info('');
        $this->info('Validation Summary:');
        $this->info('--------------');
        
        if (count($errors) > 0) {
            $this->error('ERRORS (' . count($errors) . '):');
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }
        
        if (count($warnings) > 0) {
            $this->warn('WARNINGS (' . count($warnings) . '):');
            foreach ($warnings as $warning) {
                $this->warn("  - {$warning}");
            }
        }
        
        if (count($errors) === 0 && count($warnings) === 0) {
            $this->info('All validations passed!');
        }
        
        return count($errors) > 0 ? 1 : 0;
    }
}