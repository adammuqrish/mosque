<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event as LaravelEvent;

class GenerateMosqueTestData extends Command
{
    protected $signature = 'mosque:generate-test-data 
                            {--users= : Number of users to create (default: 700)}
                            {--events= : Number of events to create (default: 120)}
                            {--donations= : Number of donations to create (default: 4500)}
                            {--seed : Run the RealisticPopulateSeeder}
                            {--safe-reset : Safely reset database (with FK checks)}';

    protected $description = 'Generate realistic test data for the mosque management platform';

    public function handle()
    {
        $this->info('Mosque Test Data Generator');
        $this->info('========================');

        if ($this->option('safe-reset')) {
            return $this->safeReset();
        }

        if ($this->option('seed')) {
            return $this->runSeeder();
        }

        $users = (int) $this->option('users') ?: 700;
        $events = (int) $this->option('events') ?: 120;
        $donations = (int) $this->option('donations') ?: 4500;

        $this->info("Generating test data with:");
        $this->info("  - Users: {$users}");
        $this->info("  - Events: {$events}");
        $this->info("  - Donations: {$donations}");

        try {
            DB::transaction(function () use ($users, $events, $donations) {
                $this->call('mosque:generate-users', ['--count' => $users]);
                $this->call('mosque:generate-events', ['--count' => $events]);
                $this->call('mosque:generate-donations', ['--count' => $donations]);
            });
            
            $this->info('Test data generation completed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function runSeeder()
    {
        $this->info('Running RealisticPopulateSeeder...');

        try {
            $this->call('db:seed', ['--class' => 'Database\Seeders\RealisticPopulateSeeder']);
            $this->info('Seeder completed successfully!');
        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function safeReset()
    {
        $this->warn('Safely resetting database...');
        $this->warn('This will truncate all tables in reverse dependency order.');

        if (!$this->confirm('Are you sure you want to reset the database?', false)) {
            $this->info('Reset cancelled.');
            return 0;
        }

        $tables = [
            'reward_redemptions',
            'badge_earnings',
            'point_transactions',
            'event_volunteer',
            'reward_redemptions',
            'rewards',
            'badges',
            'member_points',
            'volunteer_profiles',
            'withdrawal_requests',
            'donations',
            'events',
            'tier_milestones',
            'users',
        ];

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tables as $table) {
                DB::table($table)->truncate();
                $this->info("Truncated: {$table}");
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->info('Database reset completed successfully!');
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}