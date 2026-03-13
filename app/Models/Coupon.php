<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'amount',
        'max_uses',
        'used_count',
        'minimum_amount',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'amount' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isValidFor(User $user, float $amount): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }
        if ($this->minimum_amount !== null && $amount < (float) $this->minimum_amount) {
            return false;
        }

        return true;
    }

    public function couponUsage(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}
