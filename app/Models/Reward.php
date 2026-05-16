<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Reward extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_my',
        'description',
        'description_my',
        'image_svg',
        'image',
        'category',
        'points_cost',
        'stock_quantity',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'points_cost' => 'integer',
        'stock_quantity' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url', 'is_raw_svg'];

    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now()->toDateString());
            });
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;

        $now = now()->toDateString();

        if ($this->valid_from && $this->valid_from > $now) return false;
        if ($this->valid_until && $this->valid_until < $now) return false;

        if ($this->stock_quantity !== null && $this->stock_quantity <= 0) return false;

        return true;
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    public function getIsRawSvgAttribute(): bool
    {
        return !$this->image && (bool)$this->image_svg;
    }
}
