<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'amount', 'category', 'type', 'fund_purpose', 'asnaf_category',
        'source', 'status', 'reference', 'description', 'donation_date',
        'receipt_number',
        'verified_by', 'verified_at',
        'donor_name', 'donor_ic', 'donor_phone', 'donor_email', 'donor_address',
    ];

    protected $casts = [
        'donation_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function getDonorIcAttribute($value)
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

    public function setDonorIcAttribute($value)
    {
        $this->attributes['donor_ic'] = $value !== null
            ? \Illuminate\Support\Facades\Crypt::encrypt($value)
            : null;
    }

    public function getDonorPhoneAttribute($value)
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

    public function setDonorPhoneAttribute($value)
    {
        $this->attributes['donor_phone'] = $value !== null
            ? \Illuminate\Support\Facades\Crypt::encrypt($value)
            : null;
    }

    public function getDonorAddressAttribute($value)
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

    public function setDonorAddressAttribute($value)
    {
        $this->attributes['donor_address'] = $value !== null
            ? \Illuminate\Support\Facades\Crypt::encrypt($value)
            : null;
    }

    public function getTypeLabelAttribute(): string
    {
        switch ($this->type) {
            case 'obligatory':
                return 'Obligatory (Wajib)';
            case 'endowment':
                return 'Endowment (Waqf)';
            default:
                return 'Voluntary (Sunnah)';
        }
    }

    public function getShariahTypeLabelAttribute(): string
    {
        $labels = [
            'zakat' => 'Zakat',
            'zakat_fitr' => 'Zakat Fitr',
            'sadaqah' => 'Sadaqah',
            'waqf' => 'Waqf',
        ];
        return $labels[$this->category] ?? ucfirst($this->category);
    }

    public function getFundPurposeLabelAttribute(): ?string
    {
        if (!$this->fund_purpose) return null;

        $common = [
            'General Fund' => 'General Fund',
            'Operations' => 'Operations',
            'Construction' => 'Construction',
            'Education' => 'Education',
            'Humanitarian' => 'Humanitarian',
            'Sadaqah (General)' => 'Sadaqah (General)',
            'Sadaqah Jariyah' => 'Sadaqah Jariyah',
            'Infaq' => 'Infaq',
        ];
        return $common[$this->fund_purpose] ?? $this->fund_purpose;
    }

    public function getAsnafLabelAttribute(): ?string
    {
        if (!$this->asnaf_category) return null;

        $asnafLabels = [
            'faqir' => 'Faqir (Poor)',
            'miskin' => 'Miskin (Needy)',
            'amil' => 'Amil (Collector)',
            'mualaf' => 'Mualaf (New Muslim)',
            'riqab' => 'Riqab (Captives)',
            'gharimin' => 'Gharimin (Debtors)',
            'fisabilillah' => 'Fisabilillah (In God\'s Cause)',
            'ibnus_sabil' => 'Ibnus Sabil (Travelers)',
        ];

        return $asnafLabels[$this->asnaf_category] ?? $this->asnaf_category;
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeObligatory($query)
    {
        return $query->where('type', 'obligatory');
    }

    public function scopeVoluntary($query)
    {
        return $query->where('type', 'voluntary');
    }

    public function scopeEndowment($query)
    {
        return $query->where('type', 'endowment');
    }

    public function scopeByFundPurpose($query, string $purpose)
    {
        return $query->where('fund_purpose', $purpose);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', 'disputed');
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'disputed' => 'Disputed',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending' => 'yellow',
            'confirmed' => 'green',
            'disputed' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }

    public function getCanVerifyAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function getDistinctPurposes(): array
    {
        return self::whereNotNull('fund_purpose')
            ->distinct()
            ->pluck('fund_purpose')
            ->toArray();
    }

    public static function getSuggestedPurposes(): array
    {
        return FundPurpose::active()->ordered()->pluck('name')->toArray();
    }

    // Accessors
    public function getDonorDisplayNameAttribute(): string
    {
        return $this->donor_name ?: 'Anonymous';
    }

    public function getDonorDisplayIcAttribute(): ?string
    {
        if (!$this->donor_ic) return null;
        $first = substr($this->donor_ic, 0, 6);
        $last = substr($this->donor_ic, -4);
        return $first . '-' . str_repeat('*', 2) . '-' . $last;
    }

    public function getHasDonorInfoAttribute(): bool
    {
        return !is_null($this->donor_name);
    }

    public function zakatAkad()
    {
        return $this->hasOne(ZakatAkad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
