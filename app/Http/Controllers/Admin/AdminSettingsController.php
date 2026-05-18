<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminSettingsController extends Controller
{
    public function index(\App\Services\RegistrationCodeService $codeService)
    {
        $adminCode = $codeService->getAdminCode();
        $treasurerCode = $codeService->getTreasurerCode();

        return view('admin.settings', compact('adminCode', 'treasurerCode'));
    }

    public function regenerateAdmin(\App\Services\RegistrationCodeService $codeService)
    {
        $newCode = $codeService->regenerateAdminCode();

        return redirect()->route('admin.settings')
            ->with('success', "Kod Admin baru: {$newCode}");
    }

    public function regenerateTreasurer(\App\Services\RegistrationCodeService $codeService)
    {
        $newCode = $codeService->regenerateTreasurerCode();

        return redirect()->route('admin.settings')
            ->with('success', "Kod Bendahari baru: {$newCode}");
    }
}
