<?php

namespace App\Services;

use App\Models\ReceiptNumberSequence;
use Illuminate\Support\Facades\DB;

class ReceiptNumberService
{
    const PADDING = 5;

    public function nextDonationReceiptNumber(): string
    {
        return $this->next('DON');
    }

    public function nextAkadReference(): string
    {
        return $this->next('ZKT');
    }

    private function next(string $prefix): string
    {
        $year = now()->format('Y');

        $sequence = DB::transaction(function () use ($prefix, $year) {
            $sequence = ReceiptNumberSequence::where('prefix', $prefix)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = ReceiptNumberSequence::create([
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $sequence->increment('last_number');
            $sequence->refresh();

            return $sequence;
        });

        $number = str_pad($sequence->last_number, self::PADDING, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$number}";
    }
}
