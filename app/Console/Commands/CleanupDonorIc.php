<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Models\ZakatAkad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class CleanupDonorIc extends Command
{
    protected $signature = 'donations:cleanup-donor-ic';
    protected $description = 'Decrypt or nullify existing encrypted donor_ic and muzakki_ic values';

    public function handle()
    {
        $this->cleanupDonationDonorIc();
        $this->cleanupZakatAkadMuzakkiIc();
    }

    private function cleanupDonationDonorIc()
    {
        $donations = Donation::whereNotNull('donor_ic')->get();
        $count = 0;
        $failed = 0;

        foreach ($donations as $donation) {
            $raw = $donation->getRawOriginal('donor_ic');
            if (substr($raw, 0, 3) !== 'eyJ') {
                continue;
            }
            try {
                $decrypted = Crypt::decrypt($raw);
                $donation->donor_ic = $decrypted;
                $donation->save();
                $count++;
            } catch (\Exception $e) {
                $donation->donor_ic = null;
                $donation->save();
                $failed++;
            }
        }

        $this->info("Donations donor_ic - Decrypted: $count, Nullified: $failed");
    }

    private function cleanupZakatAkadMuzakkiIc()
    {
        $akads = ZakatAkad::whereNotNull('muzakki_ic')->get();
        $count = 0;
        $failed = 0;

        foreach ($akads as $akad) {
            $raw = $akad->getRawOriginal('muzakki_ic');
            if (substr($raw, 0, 3) !== 'eyJ') {
                continue;
            }
            try {
                $decrypted = Crypt::decrypt($raw);
                $akad->muzakki_ic = $decrypted;
                $akad->save();
                $count++;
            } catch (\Exception $e) {
                $akad->muzakki_ic = null;
                $akad->save();
                $failed++;
            }
        }

        $this->info("ZakatAkad muzakki_ic - Decrypted: $count, Nullified: $failed");
    }
}
