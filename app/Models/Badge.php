<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Badge extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_my',
        'description',
        'description_my',
        'icon_svg',
        'icon',
        'tier',
        'points_awarded',
        'is_active',
    ];

    protected $casts = [
        'points_awarded' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['icon_url', 'is_raw_svg', 'fallback_emoji'];

    public function earnings(): HasMany
    {
        return $this->hasMany(BadgeEarning::class);
    }

    public function earnedByUsers(): HasMany
    {
        return $this->hasMany(BadgeEarning::class, 'badge_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getIconUrlAttribute(): ?string
    {
        if ($this->icon) {
            return Storage::url($this->icon);
        }
        return null;
    }

    public function getIsRawSvgAttribute(): bool
    {
        return !$this->icon && (bool)$this->icon_svg;
    }

    public function getFallbackEmojiAttribute(): string
    {
        $emojis = [
            'bronze' => '🥉',
            'silver' => '🥈',
            'gold' => '🥇',
            'platinum' => '💎',
            'diamond' => '👑',
        ];

        return $emojis[$this->tier] ?? '🏅';
    }
}
