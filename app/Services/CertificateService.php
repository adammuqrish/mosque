<?php

namespace App\Services;

use App\Models\User;
use App\Models\RewardRedemption;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function generateCertificate(User $user, RewardRedemption $redemption): string
    {
        $tier = $user->memberPoints ? $user->memberPoints->tier : null;
        $data = [
            'user' => $user,
            'redemption' => $redemption,
            'reward' => $redemption->reward,
            'date' => Carbon::now()->format('d F Y'),
            'tier' => $tier,
        ];

        $pdf = PDF::loadView('gamification.certificate', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = "certificates/certificate_{$user->id}_{$redemption->id}_" . time() . ".pdf";
        
        Storage::disk('public')->put($filename, $pdf->output());
        
        return $filename;
    }

    public function downloadCertificate(RewardRedemption $redemption): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if ($redemption->status !== 'claimed') {
            abort(403, 'Reward must be claimed before downloading certificate');
        }

        $path = $this->generateCertificate($redemption->user, $redemption);
        
        return Storage::disk('public')->download($path, "certificate_{$redemption->id}.pdf");
    }
}
