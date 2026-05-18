<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Str;

class RegistrationCodeService
{
    public function getAllCodes(): array
    {
        try {
            $codes = [];

            $adminCode = Setting::getValue('admin_code');
            $treasurerCode = Setting::getValue('treasurer_code');

            if (!empty($adminCode)) {
                $codes[$adminCode] = 'admin';
            }
            if (!empty($treasurerCode)) {
                $codes[$treasurerCode] = 'treasurer';
            }

            if (!empty($codes)) {
                return $codes;
            }
        } catch (\Exception $e) {
        }

        return config('roles.special_codes', []);
    }

    public function getRoleForCode(string $code): ?string
    {
        $codes = $this->getAllCodes();
        return $codes[$code] ?? null;
    }

    public function getAdminCode(): ?string
    {
        try {
            return Setting::getValue('admin_code');
        } catch (\Exception $e) {
            return config('roles.special_codes') ? array_search('admin', config('roles.special_codes', [])) ?: null : null;
        }
    }

    public function getTreasurerCode(): ?string
    {
        try {
            return Setting::getValue('treasurer_code');
        } catch (\Exception $e) {
            return config('roles.special_codes') ? array_search('treasurer', config('roles.special_codes', [])) ?: null : null;
        }
    }

    public function regenerateAdminCode(): string
    {
        $code = 'ADMIN-' . strtoupper(Str::random(8));
        Setting::setValue('admin_code', $code);
        return $code;
    }

    public function regenerateTreasurerCode(): string
    {
        $code = 'TRSR-' . strtoupper(Str::random(8));
        Setting::setValue('treasurer_code', $code);
        return $code;
    }
}
