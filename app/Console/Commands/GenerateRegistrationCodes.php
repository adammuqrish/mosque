<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRegistrationCodes extends Command
{
    protected $signature = 'codes:generate {--show : Only display current codes without regenerating}';

    protected $description = 'Generate secure random registration codes for admin and treasurer roles';

    public function handle()
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env file not found.');
            return 1;
        }

        if ($this->option('show')) {
            $this->line('ADMIN_CODE=' . (env('ADMIN_CODE') ?: '(not set)'));
            $this->line('TREASURER_CODE=' . (env('TREASURER_CODE') ?: '(not set)'));
            return 0;
        }

        if (!$this->confirm('This will overwrite existing ADMIN_CODE and TREASURER_CODE in your .env file. Continue?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $adminCode = 'ADMIN-' . strtoupper(Str::random(8));
        $treasurerCode = 'TRSR-' . strtoupper(Str::random(8));

        $env = file_get_contents($envPath);

        if (preg_match('/^ADMIN_CODE=.*$/m', $env)) {
            $env = preg_replace('/^ADMIN_CODE=.*$/m', 'ADMIN_CODE=' . $adminCode, $env);
        } else {
            $env .= PHP_EOL . 'ADMIN_CODE=' . $adminCode;
        }

        if (preg_match('/^TREASURER_CODE=.*$/m', $env)) {
            $env = preg_replace('/^TREASURER_CODE=.*$/m', 'TREASURER_CODE=' . $treasurerCode, $env);
        } else {
            $env .= PHP_EOL . 'TREASURER_CODE=' . $treasurerCode;
        }

        file_put_contents($envPath, $env);

        $this->info('Registration codes generated:');
        $this->line("  ADMIN_CODE      = {$adminCode}");
        $this->line("  TREASURER_CODE  = {$treasurerCode}");
        $this->warn('Run: php artisan config:clear (or restart server) for changes to take effect.');

        return 0;
    }
}
