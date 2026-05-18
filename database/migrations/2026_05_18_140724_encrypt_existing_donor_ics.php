<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class EncryptExistingDonorIcs extends Migration
{
    public function up()
    {
        DB::table('donations')->whereNotNull('donor_ic')->orderBy('id')->chunk(100, function ($donations) {
            foreach ($donations as $donation) {
                if ($this->isAlreadyEncrypted($donation->donor_ic)) {
                    continue;
                }
                DB::table('donations')
                    ->where('id', $donation->id)
                    ->update(['donor_ic' => Crypt::encryptString($donation->donor_ic)]);
            }
        });

        DB::table('zakat_akads')->whereNotNull('muzakki_ic')->orderBy('id')->chunk(100, function ($akads) {
            foreach ($akads as $akad) {
                if ($this->isAlreadyEncrypted($akad->muzakki_ic)) {
                    continue;
                }
                DB::table('zakat_akads')
                    ->where('id', $akad->id)
                    ->update(['muzakki_ic' => Crypt::encryptString($akad->muzakki_ic)]);
            }
        });
    }

    public function down()
    {
        // Cannot reverse encryption without the key — left as-is for safety
    }

    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
