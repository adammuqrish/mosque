<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\RegistrationCodeService;
use Illuminate\Console\Command;

class GenerateRegistrationCodes extends Command
{
    protected $signature = 'codes:generate {--show : Only display current codes without regenerating}';

    protected $description = 'Generate secure random registration codes for admin and treasurer roles';

    public function handle(RegistrationCodeService $codeService)
    {
        if ($this->option('show')) {
            $admin = $codeService->getAdminCode();
            $treasurer = $codeService->getTreasurerCode();
            $this->line('ADMIN_CODE=' . ($admin ?: '(not set)'));
            $this->line('TREASURER_CODE=' . ($treasurer ?: '(not set)'));
            return 0;
        }

        if (!$this->confirm('This will overwrite existing codes. Continue?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $adminCode = $codeService->regenerateAdminCode();
        $treasurerCode = $codeService->regenerateTreasurerCode();

        $this->info('Registration codes generated:');
        $this->line("  ADMIN_CODE      = {$adminCode}");
        $this->line("  TREASURER_CODE  = {$treasurerCode}");

        return 0;
    }
}
