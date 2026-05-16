<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierMilestone extends Model
{
    protected $fillable = [
        'tier',
        'min_points',
        'name',
        'name_my',
        'benefits',
        'benefits_my',
        'icon_svg',
    ];

    protected $casts = [
        'min_points' => 'integer',
    ];

    protected $appends = ['icon_display'];

    public function getBenefitsArrayAttribute(): array
    {
        return array_filter(array_map('trim', explode(',', $this->benefits)));
    }

    public function getNameLocalizedAttribute(): string
    {
        return app()->getLocale() === 'ms' ? $this->name_my : $this->name;
    }

    public function getIconDisplayAttribute(): ?string
    {
        return $this->icon_svg;
    }
}