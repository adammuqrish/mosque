<?php

namespace App\Console\Commands;

use Database\Seeders\CuratedSampleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InsertCuratedSamples extends Command
{
    protected $signature = 'mosque:insert-samples
                            {--fresh : Truncate all domain tables first (asks for confirmation)}';

    protected $description = 'Insert 5 realistic, contextually-coherent sample records per category.';

    public function handle(): int
    {
        $this->info('Mosque Curated Sample Data — 5 per category');
        $this->info('==========================================');
        $this->newLine();

        if ($this->option('fresh')) {
            if (! $this->confirm('This will TRUNCATE all domain tables. Continue?', false)) {
                $this->warn('Aborted.');
                return 0;
            }

            $tables = [
                'reward_redemptions', 'withdrawal_documents', 'withdrawal_requests',
                'point_transactions', 'badge_earnings', 'event_volunteer',
                'zakat_akads', 'donations', 'volunteer_profiles',
                'events', 'member_points', 'users',
            ];

            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach ($tables as $table) {
                DB::table($table)->truncate();
                $this->line(" - truncated {$table}");
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->newLine();
        }

        try {
            Artisan::call('db:seed', [
                '--class' => CuratedSampleSeeder::class,
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            $this->error('Seeder failed: '.$e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        $this->newLine();
        $this->info('Counts after seeding:');
        foreach ([
            'users', 'volunteer_profiles', 'events', 'event_volunteer',
            'donations', 'zakat_akads', 'withdrawal_requests',
            'reward_redemptions', 'badge_earnings', 'point_transactions',
        ] as $table) {
            $count = DB::table($table)->count();
            $this->line(sprintf(' %-22s %d', $table.':', $count));
        }

        $this->newLine();
        $this->info('Login credentials (all use password "password"):');
        $this->line(' Admin:     admin.ahmad@mosque.test');
        $this->line(' Treasurer: treasurer.razali@mosque.test');
        $this->line(' Member:    member.amirul@mosque.test');

        return 0;
    }
}
