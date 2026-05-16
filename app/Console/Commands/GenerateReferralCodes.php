<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Console\Command;

/**
 * Artisan command to bulk-generate referral codes for existing members.
 * Usage: php artisan referral:generate-all [--dry-run]
 * 
 * This command finds all members without a referred_code and generates one for them.
 * Useful for onboarding existing users to the new referral system.
 */
class GenerateReferralCodes extends Command
{
    /**
     * The name and signature of the console command.
     * --dry-run flag allows preview without actual generation.
     */
    protected $signature = 'referral:generate-all {--dry-run}';

    /**
     * The console command description.
     */
    protected $description = 'Generate referral codes for all members without codes';

    /**
     * Execute the console command.
     * 
     * @param GamificationService $gamification
     * @return int
     */
    public function handle(GamificationService $gamification)
    {
        // STEP 1: Count how many members need referral codes
        $count = User::whereNull('referred_code')
            ->where('role', 'member')
            ->count();

        // STEP 2: If dry-run mode, just show preview and exit
        if ($this->option('dry-run')) {
            $this->info("🔍 DRY RUN: Would generate codes for {$count} users.");
            return 0;
        }

        // STEP 3: If no users need codes, show message and exit
        if ($count === 0) {
            $this->info('✅ All members already have referral codes. Nothing to do.');
            return 0;
        }

        // STEP 4: Process users in chunks of 50 to avoid memory issues
        $generatedCount = 0;
        User::whereNull('referred_code')
            ->where('role', 'member')
            ->chunk(50, function ($users) use ($gamification, &$generatedCount) {
                foreach ($users as $user) {
                    // STEP 5: Generate unique 8-char referral code for each user
                    $gamification->generateReferralCode($user);
                    $generatedCount++;
                    $this->line("✅ Generated for: {$user->name} ({$user->email})");
                }
            });

        // STEP 6: Show completion summary
        $this->info("🎉 Completed! Generated codes for {$generatedCount} users.");
        return 0;
    }
}
