<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatAkad extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'reference',
        'muzakki_name',
        'muzakki_ic',
        'amil_name',
        'amil_user_id',
        'akad_date',
        'amount',
        'notes',
    ];

    protected $casts = [
        'akad_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function getMuzakkiIcAttribute($value)
    {
        if ($value === null) return null;
        if (substr($value, 0, 3) === 'eyJ') {
            try {
                return \Illuminate\Support\Facades\Crypt::decrypt($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $value;
    }

    public function setMuzakkiIcAttribute($value)
    {
        $this->attributes['muzakki_ic'] = $value !== null
            ? \Illuminate\Support\Facades\Crypt::encrypt($value)
            : null;
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function amilUser()
    {
        return $this->belongsTo(User::class, 'amil_user_id');
    }

    public function getAkadReferenceAttribute(): string
    {
        return $this->reference ?? 'ZKT-' . $this->akad_date->format('Ymd') . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function getAmilDisplayAttribute(): string
    {
        if ($this->amilUser) {
            return $this->amilUser->name;
        }
        return $this->amil_name;
    }
}
